<?php
/**
 * Microsoft Azure Storage webp image conversion and support.
 *
 * Version: 1.0.0
 *
 * Author: Microsoft Open Technologies, Inc.
 *
 * Author URI: http://www.microsoft.com/
 *
 * License: New BSD License (BSD)
 *
 * Copyright (c) Microsoft Open Technologies, Inc.
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * Redistributions of source code must retain the above copyright notice, this list
 * of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this
 * list of conditions  and the following disclaimer in the documentation and/or
 * other materials provided with the distribution.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A  PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS
 * OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)  HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN
 * IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP Version 5.6
 *
 * @category  WordPress_Plugin
 * @package   Windows_Azure_Storage_For_WordPress
 * @author    Microsoft Open Technologies, Inc. <msopentech@microsoft.com>
 * @copyright Microsoft Open Technologies, Inc.
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @link      http://www.microsoft.com
 * @since     4.4.0
 */
class Windows_Azure_Webp {

	/**
	 * Windows_Azure_Webp constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		// Check if imagewebp is available. PHP 5.4 is required at a minimum for webp support.
		if ( ! function_exists( '\\imagewebp' ) ) {
			return;
		}

		// Webp is only supported by the block editor.
		if ( ! WindowsAzureStorageUtil::is_block_editor_enabled() ) {
			return;
		}

		// Only run this if webp image generation is enabled.
		if ( ! Windows_Azure_Helper::get_webp_setting() ) {
			return;
		}

		// Hook into the process for generating attachment metadata.
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'handle_update_attachment_metadata' ), 7, 2 );

		// Clean up the image sizes created in the filter above for $this->handle_update_attachment_metadata.
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'remove_handle_update_attachment_metadata' ), 10, 2 );

		// Filter the src attribute for images.
		add_filter( 'wp_get_attachment_image_src', array( $this, 'webp_attachment_image_src' ), 10, 3 );

		// Filter the srcset attribute for images.
		add_filter( 'wp_calculate_image_srcset', array( $this, 'webp_calculate_image_srcset' ), 7, 5 );

		// Calculate image srcset meta.
		add_filter( 'wp_calculate_image_srcset_meta', array( $this, 'webp_calculate_image_srcset_meta' ), 10, 4 );

		// Correct our image metadata if we have webp images.
		add_filter( 'wp_get_attachment_metadata', array( $this, 'webp_attachment_metadata' ), 10, 2 );

		// Convert core image blocks to support webp conversions. Run as early as we can to avoid other modifications
		// down the filter chain.
		add_filter( 'render_block', array( $this, 'webp_block_editor_images' ), 1, 2 );

		// Webp is not support in open graph so remove in WPSEO.
		add_filter( 'wpseo_opengraph_image', array( $this, 'get_original_image_og' ), 10, 2 );
		add_filter( 'wpseo_twitter_image', array( $this, 'get_original_image_twitter' ), 10, 2 );
	}

	/**
	 * Handles the generation of new webp images.
	 *
	 * @access public
	 * @filter wp_generate_attachment_metadata
	 *
	 * @param array   $data    Attachment data.
	 * @param integer $post_id Associated post id.
	 *
	 * @return array
	 */
	public function handle_update_attachment_metadata( $data, $post_id ) {
		$mime_type          = get_post_mime_type( $post_id );
		$allowed_mime_types = array(
			'image/jpeg',
			'image/jpg',
			'image/png',
		);

		// If the mime type isn't in our list, return the attachment data.
		if ( ! in_array( $mime_type, $allowed_mime_types, true ) ) {
			return $data;
		}

		$upload_file_name = get_attached_file( $post_id, true );
		$pathinfo         = pathinfo( $upload_file_name );

		// Convert the original image. If one can't be generated, return the attachment data.
		$original_webp = $this->convert_file( $upload_file_name, $mime_type );
		if ( ! $original_webp ) {
			return $data;
		}

		// Set the original image size for the new webp image.
		$webp_sizes = array(
			'full--webp' => array(
				'width'     => $data['width'],
				'height'    => $data['height'],
				'file'      => $pathinfo['filename'] . '.webp',
				'mime-type' => 'image/webp',
				'original'  => $pathinfo['basename'],
			),
		);

		// Generate all the sizes registered in WordPress.
		if ( isset( $data['sizes'] ) ) {
			foreach ( $data['sizes'] as $size_name => $details ) {
				$webp_details              = $details;
				$webp_details['mime-type'] = 'image/webp';
				$file_pathinfo             = pathinfo( $details['file'] );
				$generated_file            = $this->convert_file(
					$pathinfo['dirname'] . '/' . $details['file'],
					$details['mime-type']
				);

				// If we successfully generated a new webp image, add it to the sizes array.
				if ( $generated_file ) {
					$webp_details['file']                = $file_pathinfo['filename'] . '.webp';
					$webp_details['original']            = $file_pathinfo['basename'];
					$webp_sizes[ $size_name . '--webp' ] = $webp_details;
				}
			}
			$data['sizes'] = array_merge( $data['sizes'], $webp_sizes );
		} else {
			$data['sizes'] = $webp_sizes;
		}

		// Store all the info into post meta.
		update_post_meta( $post_id, 'webp_attachment_details', $webp_sizes );

		return $data;
	}

	/**
	 * Cleans up the extra sizes added previously in $this->handle_update_attachment_metadata.
	 *
	 * @access public
	 * @filter wp_generate_attachment_metadata
	 *
	 * @param array   $data    Attachment data.
	 * @param integer $post_id Associated post id.
	 *
	 * @return array
	 */
	public function remove_handle_update_attachment_metadata( $data, $post_id ) {
		$webp_meta = get_post_meta( $post_id, 'webp_attachment_details', true );
		if ( $webp_meta && $data ) {
			$data['sizes'] = array_diff_key( $data['sizes'], $webp_meta );
		}

		return $data;
	}

	/**
	 * Filters the 'src' attribute in an image.
	 *
	 * @access public
	 * @filter wp_get_attachment_image_src
	 *
	 * @param bool|array   $image         Either array with src, width & height, icon src, or false.
	 * @param int|string   $attachment_id Image attachment ID.
	 * @param string|array $size          Size of image. Image size or array of width and height values
	 *
	 * @return array|bool
	 */
	public function webp_attachment_image_src( $image, $attachment_id, $size ) {
		if ( is_array( $image ) ) {
			$webp_meta = $this->get_webp_meta( $attachment_id );
			if ( $webp_meta ) {
				// Convert our $size variable into a string if it is an array.
				if ( is_array( $size ) ) {
					$size = implode( '-', $size );
				}

				// If the size is "original", convert to "full".
				if ( 'original' === $size ) {
					$size = 'full';
				}

				// Update the file to webp if the size exists.
				if ( array_key_exists( $size . '--webp', $webp_meta ) ) {
					$file_pathinfo = pathinfo( $image[0] );
					if ( $file_pathinfo['basename'] === $webp_meta[ $size . '--webp' ]['original'] ) {
						$image[0] = $file_pathinfo['dirname'] . '/' . $webp_meta[ $size . '--webp' ]['file'];
					}
				}
			}
		}

		return $image;
	}

	/**
	 * Filters the 'srcset' attribute sources in an image.
	 *
	 * @access public
	 * @filter wp_calculate_image_srcset
	 *
	 * @param array  $sources {
	 *     One or more arrays of source data to include in the 'srcset'.
	 *
	 *     @type array $width {
	 *         @type string $url        The URL of an image source.
	 *         @type string $descriptor The descriptor type used in the image candidate string, either 'w' or 'x'.
	 *         @type int    $value      The source width if paired with a 'w' descriptor, or a pixel density value if
	 *                                  paired with an 'x' descriptor.
	 *     }
	 * }
	 * @param array  $size_array {
	 *     An array of requested width and height values.
	 *
	 *     @type int $0 The width in pixels.
	 *     @type int $1 The height in pixels.
	 * }
	 * @param string $image_src     The 'src' of the image.
	 * @param array  $image_meta    The image metadata as returned by 'wp_get_attachment_metadata()'.
	 * @param int    $attachment_id Image attachment ID or 0.
	 *
	 * @return array
	 */
	public function webp_calculate_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
		$webp_meta = $this->get_webp_meta( $attachment_id );
		if ( $webp_meta ) {
			foreach ( $sources as &$source ) {
				$url_pathinfo = pathinfo( $source['url'] );
				foreach ( $webp_meta as $key => $details ) {
					if ( $url_pathinfo['basename'] === $details['original'] ) {
						// Update the file path to our webp image.
						$source['url'] = $url_pathinfo['dirname'] . '/' . $details['file'];
					}
				}
			}
		}

		return $sources;
	}

	/**
	 * Let plugins pre-filter the image meta to be able to fix inconsistencies in the stored data.
	 *
	 * @access public
	 * @filter wp_calculate_image_srcset_meta
	 *
	 * @param array  $image_meta    The image metadata as returned by wp_get_attachment_metadata().
	 * @param array  $size_array    Array of width and height values in pixels (in that order).
	 * @param string $image_src     The 'src' of the image.
	 * @param int    $attachment_id The image attachment ID or 0 if not supplied.
	 *
	 * @return array A modified version of the meta.
	 */
	public function webp_calculate_image_srcset_meta( $image_meta, $size_array, $image_src, $attachment_id ) {
		$webp_meta = $this->get_webp_meta( $attachment_id );
		if ( $webp_meta ) {
			if ( isset( $image_meta['sizes'] ) ) {
				foreach ( $image_meta['sizes'] as $size_name => $details ) {
					$webp_key = $size_name . '--webp';
					if ( isset( $webp_meta[ $webp_key ] ) ) {
						$image_meta['sizes'][ $size_name ] = $webp_meta[ $webp_key ];
					}
				}
			}
		}

		return $image_meta;
	}

	/**
	 * Updates the attachment metadata to ensure it returns our .webp versions if it is set.
	 *
	 * @access public
	 * @filter wp_get_attachment_metadata
	 *
	 * @param array $data          Array of metadata for the given attachment.
	 * @param int   $attachment_id Attachment post ID.
	 *
	 * @return mixed
	 */
	public function webp_attachment_metadata( $data, $attachment_id ) {
		$webp_meta = $this->get_webp_meta( $attachment_id );
		if ( $webp_meta ) {
			$file_ext = pathinfo( $data['file'], PATHINFO_EXTENSION );

			// Update the file name and the URL with our webp versions.
			$data['file'] = str_replace( $file_ext, 'webp', $data['file'] );
			$data['url']  = str_replace( $file_ext, 'webp', $data['url'] );
		}

		return $data;
	}

	/**
	 * Update the generated HTML of the block editor images to utilize webp images.
	 *
	 * @access public
	 * @filter render_block
	 *
	 * @param string $block_content The string of HTML that is generated by the block editor for a particular block.
	 * @param array  $block         An array of data that defines a single block.
	 *
	 * @return string
	 */
	public function webp_block_editor_images( $block_content, $block ) {
		// Update the core/image block markup.
		if ( 'core/image' === $block['blockName'] ) {
			$attributes = $this->get_webp_image_atts( absint( $block['attrs']['id'] ), $block['attrs']['sizeSlug'] );
			if ( ! $attributes ) {
				return $block_content;
			}

			$block_content = $this->update_block_content_image( $block_content, $attributes );
		}

		// Update the core/media-text block markup.
		if ( 'core/media-text' === $block['blockName'] ) {
			$attributes = $this->get_webp_image_atts( absint( $block['attrs']['mediaId'] ) );
			if ( ! $attributes ) {
				return $block_content;
			}

			$block_content = $this->update_block_content_image( $block_content, $attributes );
		}

		// Update the core/cover block markup.
		if ( 'core/cover' === $block['blockName'] ) {
			$attributes = $this->get_webp_image_atts( absint( $block['attrs']['id'] ) );
			if ( ! $attributes ) {
				return $block_content;
			}

			$block_content = $this->update_block_content_image( $block_content, $attributes );
		}

		return $block_content;
	}

	/**
	 * Ensure OpenGraph image is in original format and not webp.
	 *
	 * @access public
	 * @filter wpseo_opengraph_image
	 *
	 * @param string $image_url image URL from yoast.
	 * @param object $presenter Yoast SEO presenter.
	 *
	 * @return string
	 */
	public function get_original_image_og( $image_url, $presenter ) {
		return $this->get_original_image( $image_url, $presenter, 'opengraph' );
	}

	/**
	 * Ensure Twitter image is in original format and not webp.
	 *
	 * @access public
	 * @filter wpseo_twitter_image
	 *
	 * @param string $image_url image URL from yoast.
	 * @param object $presenter Yoast SEO presenter.
	 *
	 * @return string
	 */
	public function get_original_image_twitter( $image_url, $presenter ) {
		return $this->get_original_image( $image_url, $presenter, 'twitter' );
	}



	/**
	 * Helper method to fetch the webp metadata from post meta.
	 *
	 * @access private
	 *
	 * @param int $attachment_id The ID of the attachement (aka media/image).
	 *
	 * @return mixed
	 */
	private function get_webp_meta( $attachment_id ) {
		return get_post_meta( $attachment_id, 'webp_attachment_details', true );
	}

	/**
	 * Generate a webp version of an image.
	 *
	 * @access private
	 *
	 * @param string $source    File on disk to generate a webp image from.
	 * @param string $mime_type The mime type.
	 *
	 * @return false|string
	 */
	private function convert_file( $source, $mime_type ) {
		$path_info = pathinfo( $source );
		$folder    = $path_info['dirname'] . '/';
		$file_name = $path_info['filename'];

		/**
		 * Set image quality of the new webp image.
		 *
		 * @since 4.3.3-dev
		 *
		 * @param int $image_quality Set the quality of the webp images we will generate. Default: 90
		 */
		$image_quality = apply_filters( 'windows_azure_webp_image_quality', 90 );

		// Handle PNG or JPEG images appropriately.
		if ( 'image/png' === $mime_type ) {
			$source = imagecreatefrompng( $source );
			// Check for transparency.
			if ( ! imageistruecolor( $source ) ) {
				imagepalettetotruecolor( $source );
			}
			$result = imagewebp( $source, $folder . $file_name . '.webp', (int) $image_quality );
		} else {
			$result = imagewebp(
				imagecreatefromjpeg( $source ),
				$folder . $file_name . '.webp',
				(int) $image_quality
			);
		}

		if ( true === $result ) {
			return $folder . $file_name . '.webp';
		}

		return false;
	}

	/**
	 * Returns an array of all attributes needed to convert an image to use their webp versions.
	 *
	 * @access private
	 *
	 * @param int         $attachment_id The attachment ID of the image we want to get attributes for.
	 * @param string|null $size          The size of the image. Default 'full'.
	 *
	 * @return array|false
	 */
	private function get_webp_image_atts( $attachment_id, $size = 'full' ) {
		// Get the webp meta, and if we don't have any, return the block as it is.
		$webp_meta = get_post_meta( $attachment_id, 'webp_attachment_details', true );
		if ( ! $webp_meta ) {
			return false;
		}

		// Get the webp version of the image if one is set.
		$image = wp_get_attachment_image_src( $attachment_id, $size );
		if ( ! $image ) {
			return false;
		}

		// Disable our image_src filter so we can gather the original image source data.
		remove_filter(
			'wp_get_attachment_image_src',
			array( $this, 'webp_attachment_image_src' )
		);

		$orig_image = wp_get_attachment_image_src( $attachment_id, $size );
		if ( ! $orig_image ) {
			return false;
		}

		add_filter( 'wp_get_attachment_image_src', array( $this, 'webp_attachment_image_src' ), 10, 3 );

		// Break out our array values of the original image into separate variables.
		list( $orig_src, $width, $height ) = $orig_image;

		// Set up some variables and where we'll keep all the attribute info we'll update.
		$attributes = array();
		$size_array = array( absint( $width ), absint( $height ) );
		$image_meta = wp_get_attachment_metadata( $attachment_id );

		// Get the webp generated srcset.
		// NOTE: This is needed as WordPress won't generate the srcset once we modify the block content.
		$attributes['srcset'] = wp_calculate_image_srcset( $size_array, $image[0], $image_meta, $attachment_id );

		// Disable some of our webp filters used to generate the original srcset attribute data.
		remove_filter(
			'wp_calculate_image_srcset',
			array( $this, 'webp_calculate_image_srcset' ),
			7
		);
		remove_filter(
			'wp_calculate_image_srcset_meta',
			array( $this, 'webp_calculate_image_srcset_meta' )
		);
		remove_filter(
			'wp_get_attachment_metadata',
			array( $this, 'webp_attachment_metadata' )
		);

		// Get the original srcset and image metadata.
		$image_meta  = wp_get_attachment_metadata( $attachment_id );
		$orig_srcset = wp_calculate_image_srcset( $size_array, $orig_src, $image_meta, $attachment_id );

		// Turn back on the image srcset filters.
		add_filter( 'wp_calculate_image_srcset', array( $this, 'webp_calculate_image_srcset' ), 7, 5 );
		add_filter(
			'wp_calculate_image_srcset_meta',
			array( $this, 'webp_calculate_image_srcset_meta' ),
			10,
			4
		);
		add_filter( 'wp_get_attachment_metadata', array( $this, 'webp_attachment_metadata' ), 10, 2 );

		// Add the original data to new data attributes for legacy browser fallback and update some preset attributes.
		$attributes['class']    = ' webp-format';
		$attributes['orig-src'] = $orig_src;
		if ( $orig_srcset ) {
			$attributes['orig-srcset'] = $orig_srcset;
		}

		return $attributes;
	}

	/**
	 * Parses a string of HTML from the block editor and updates the images with the supplied attributes.
	 *
	 * @access private
	 *
	 * @param string $block_content The content contained with in a particular block.
	 * @param array  $attributes    The attributes we wish to add/update on an image within the block content.
	 *
	 * @return string
	 */
	private function update_block_content_image( $block_content, $attributes ) {
		// Parse the HTML string so we can modify the data.
		$dom = new DOMDocument();

		// Disable reporting as libxml will error on HTML5 markup.
		libxml_use_internal_errors( true );
		$dom->loadHTML(
			mb_convert_encoding( $block_content, 'HTML-ENTITIES', 'UTF-8' ),
			LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED
		);
		libxml_clear_errors();

		$nodes = $dom->getElementsByTagName( 'img' );

		foreach ( $nodes as $node ) {
			$node->setAttribute( 'class', $node->getAttribute( 'class' ) . $attributes['class'] );
			$node->setAttribute(
				'src',
				str_replace(
					array( '.jpg', '.jpeg', '.png' ),
					'.webp',
					$node->getAttribute( 'src' )
				)
			);
			$node->setAttribute( 'srcset', $attributes['srcset'] );
			$node->setAttribute( 'data-orig-src', $attributes['orig-src'] );

			if ( isset( $attributes['orig-srcset'] ) ) {
				$node->setAttribute( 'data-orig-srcset', $attributes['orig-srcset'] );
			}
		}

		return $dom->saveHTML();
	}

	/**
	 * Ensure we return the original format for Yoast OG and Twitter social share and not webp.
	 *
	 * @access private
	 *
	 * @param string $image_url image URL from yoast.
	 * @param object $presenter Yoast SEO presenter.
	 * @param string $type      The type of data that is being filtered for from Yoast.
	 *
	 * @return string
	 */
	private function get_original_image( $image_url, $presenter, $type ) {
		if ( empty( $image_url ) ) {
			return $image_url;
		}

		$extension = pathinfo( $image_url, PATHINFO_EXTENSION );
		if ( 'webp' !== $extension ) {
			return $image_url;
		}

		// Set the default variable for our original image.
		$new_url = '';

		if ( 'opengraph' === $type ) {
			// Fetch the image URL from Yoast OpenGraph post meta.
			$new_url = get_post_meta( get_the_ID(), '_yoast_wpseo_opengraph-image', true );
		} elseif ( 'twitter' === $type ) {
			// Fetch the image URL from Yoast Twitter post meta.
			$new_url = get_post_meta( get_the_ID(), '_yoast_wpseo_twitter-image', true );
		}

		if ( empty( $new_url ) ) {
			$attachment_id = $presenter->model->open_graph_image_id;
			$new_url       = $this->try_to_get_original_image_url( $attachment_id, $image_url );
		}

		return $new_url;
	}


	/**
	 * Get attachment ID and lookup in the post meta to find the original image
	 *
	 * @access private
	 *
	 * @param int    $attachment_id ID of the attachment to lookup.
	 * @param string $image_url     URL that comes from Yoast.
	 *
	 * @return string new url
	 */
	private function try_to_get_original_image_url( $attachment_id, $image_url ) {
		// Sometimes Yoast can't give us the attachment_id, so we'll have to try our best to find it.
		if ( is_null( $attachment_id ) ) {
			$attachment_id = attachment_url_to_postid( $image_url );
			if ( 0 === $attachment_id ) {
				return ''; // Return empty as we couldn't find this photo.
			}
		}

		$webp_meta = get_post_meta( $attachment_id, 'webp_attachment_details', true );
		if ( empty( $webp_meta ) ) {
			$attachment_meta = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );
			return $attachment_meta['url'];
		}

		if ( ! empty( $webp_meta['og_large--webp'] ) ) {
			$new_image = $webp_meta['og_large--webp']['original'];
		}

		if ( empty( $new_image ) ) {
			$new_image = $webp_meta['original--webp']['original'];
		}

		$old_url = explode( '/', $image_url );

		return str_replace( end( $old_url ), $new_image, $image_url );
	}

}
new Windows_Azure_Webp();
