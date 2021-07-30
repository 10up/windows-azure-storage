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
 * PHP Version 5
 *
 * @category  WordPress_Plugin
 * @package   Windows_Azure_Storage_For_WordPress
 * @author    Microsoft Open Technologies, Inc. <msopentech@microsoft.com>
 * @copyright Microsoft Open Technologies, Inc.
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @link      http://www.microsoft.com
 * @since     4.3.3-dev
 */
class Windows_Azure_Webp {

	/**
	 * Windows_Azure_Webp constructor.
	 */
	public function __construct() {
		// Check if imagewebp is available. PHP 5.4 is required at a minimum for webp support.
		if ( ! function_exists( '\\imagewebp' ) ) {
			return;
		}

		// Hook into the process for generating attachment metadata.
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'handle_update_attachment_metadata' ), 7, 2 );

		// Clean up the image sizes created in the filter above for $this->handle_update_attachment_metadata.
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'remove_handle_update_attachment_metadata' ), 10, 2 );

		// Filter the srcset attribute for images.
		add_filter( 'wp_calculate_image_srcset', array( $this, 'webp_calculate_image_srcset' ), 7, 5 );

		// Filter the src attribute for images.
		add_filter( 'wp_get_attachment_image_src', array( $this, 'webp_attachment_image_src' ), 10, 4 );

		// Calculate image srcset meta.
		add_filter( 'wp_calculate_image_srcset_meta', array( $this, 'webp_calculate_image_srcset_meta' ), 10, 4 );

		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'add_original_attributes' ), 10, 3 );
	}

	/**
	 * Handles the generation of new webp images.
	 *
	 * @filter wp_generate_attachment_metadata
	 *
	 * @param array  $data    Attachment data.
	 *
	 * @param integer $post_id Associated post id.
	 *
	 * @return array
	 */
	public function handle_update_attachment_metadata( array $data, int $post_id ): array {
		$mime_type         = get_post_mime_type( $post_id );
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
			'original--webp' => array(
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
	 * @filter wp_generate_attachment_metadata
	 *
	 * @param string  $data    Attachment data.
	 * @param integer $post_id Associated post id.
	 *
	 * @return string
	 */
	public function remove_handle_update_attachment_metadata( string $data, int $post_id ): string {
		$webp_meta = get_post_meta( $post_id, 'webp_attachment_details', true );
		if ( $webp_meta && $data ) {
			$data['sizes'] = array_diff_key( $data['sizes'], $webp_meta );
		}

		return $data;
	}

	/**
	 * Filters the 'srcset' attribute sources in an image.
	 *
	 * @filter wp_calculate_image_srcset
	 *
	 * @param array  $sources {
	 *     One or more arrays of source data to include in the 'srcset'.
	 *
	 *     @type array $width {
	 *         @type string $url        The URL of an image source.
	 *         @type string $descriptor The descriptor type used in the image candidate string,
	 *                                  either 'w' or 'x'.
	 *         @type int    $value      The source width if paired with a 'w' descriptor, or a
	 *                                  pixel density value if paired with an 'x' descriptor.
	 *     }
	 * }
	 *
	 * @param array $size_array     {
	 *     An array of requested width and height values.
	 *
	 *     @type int $0 The width in pixels.
	 *     @type int $1 The height in pixels.
	 * }
	 *
	 * @param string $image_src     The 'src' of the image.
	 * @param array  $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
	 * @param int    $attachment_id Image attachment ID or 0.
	 *
	 * @return array
	 */
	public function webp_calculate_image_srcset( array $sources, array $size_array, string $image_src, array $image_meta, int $attachment_id ): array {
		$webp_meta = get_post_meta( $attachment_id, 'webp_attachment_details', true );
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
	 * Filters the 'src' attribute in an image.
	 *
	 * @filter wp_get_attachment_image_src
	 *
	 * @param bool|array   $image         Either array with src, width & height, icon src, or false.
	 * @param int|string   $attachment_id Image attachment ID.
	 * @param string|array $size          Size of image. Image size or array of width and height values
	 *                                    (in that order). Default 'thumbnail'.
	 * @param bool         $icon          Whether the image should be treated as an icon. Default false.
	 *
	 * @return array|bool
	 */
	public function webp_attachment_image_src( $image, $attachment_id, $size, $icon ) {
		if ( is_array( $image ) ) {
			$webp_meta = get_post_meta( $attachment_id, 'webp_attachment_details', true );
			if ( $webp_meta ) {
				// Convert our $size variable into a string if it is an array.
				if ( is_array( $size ) ) {
					$size = implode( '-', $size );
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
	 * Let plugins pre-filter the image meta to be able to fix inconsistencies in the stored data.
	 *
	 * @filter wp_calculate_image_srcset_meta
	 *
	 * @param array  $image_meta    The image meta data as returned by wp_get_attachment_metadata().
	 * @param array  $size_array    Array of width and height values in pixels (in that order).
	 * @param string $image_src     The 'src' of the image.
	 * @param int    $attachment_id The image attachment ID or 0 if not supplied.
	 *
	 * @return array A modified version of the meta.
	 */
	function webp_calculate_image_srcset_meta( array $image_meta, array $size_array, string $image_src, int $attachment_id ): array {
		$webp_meta = get_post_meta( $attachment_id, 'webp_attachment_details', true );
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
	 * Add original attributes
	 *
	 * @filter wp_get_attachment_image_attributes
	 *
	 * @param array        $attr       Array of attributes.
	 * @param object       $attachment Object.
	 * @param array|string $size       Size.
	 *
	 * @return array
	 */
	function add_original_attributes( array $attr, object $attachment, $size ): array {
		$webp_meta = get_post_meta( $attachment->ID, 'webp_attachment_details', true );

		// Return the set attributes if no webp metadata was found.
		if ( false === $webp_meta || empty( $webp_meta ) ) {
			return $attr;
		}

		// Remove conflicting filters.
		remove_filter( 'wp_calculate_image_srcset', array( $this, 'webp_calculate_image_srcset' ), 7 );
		remove_filter( 'wp_get_attachment_image_src', array( $this, 'webp_attachment_image_src' ), 10 );
		remove_filter( 'wp_calculate_image_srcset_meta', array( $this, 'webp_calculate_image_srcset_meta' ), 10 );

		// Fetch our image. If one isn't found, before returning early, re-add the filters we previously removed.
		$image = wp_get_attachment_image_src( $attachment->ID, $size );
		if ( ! $image ) {
			add_filter( 'wp_calculate_image_srcset', array( $this, 'webp_calculate_image_srcset' ), 7, 5 );
			add_filter( 'wp_get_attachment_image_src', array( $this, 'webp_attachment_image_src' ), 10, 4 );
			add_filter( 'wp_calculate_image_srcset_meta', array( $this, 'webp_calculate_image_srcset_meta' ), 10, 4 );

			return $attr;
		}

		// Assign variables from data in our $image array.
		list( $src, $width, $height ) = $image;

		// Fetch the image metadata. If found, assign sizes and srcset attributes.
		$image_meta = wp_get_attachment_metadata( $attachment->ID );
		if ( is_array( $image_meta ) ) {
			$size_array = array( absint( $width ), absint( $height ) );
			$srcset     = wp_calculate_image_srcset( $size_array, $src, $image_meta, $attachment->ID );
			if ( $srcset ) {
				$attr['data-orig-srcset'] = $srcset;
			}
			$attr['data-orig-src'] = $src;
		}

		$attr['class'] .= ' webp-format';

		// Before wrapping up, re-add the filters we removed at the beginning.
		add_filter( 'wp_calculate_image_srcset', __NAMESPACE__ . '\\webp_calculate_image_srcset', 7, 5 );
		add_filter( 'wp_get_attachment_image_src', __NAMESPACE__ . '\\webp_attachment_image_src', 10, 4 );
		add_filter( 'wp_calculate_image_srcset_meta', __NAMESPACE__ . '\\webp_calculate_image_srcset_meta', 10, 4 );

		return $attr;
	}

	/**
	 * Generate a webp version of an image.
	 *
	 * @param string $source    File on disk to generate a webp image from.
	 * @param string $mime_type The mime type.
	 *
	 * @return false|string
	 */
	private function convert_file( string $source, string $mime_type ) {
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
			$result = imagewebp( imagecreatefromjpeg( $source ), $folder . $file_name . '.webp', (int) $image_quality );
		}

		if ( true === $result ) {
			return $folder . $file_name . '.webp';
		}

		return false;
	}
}
new Windows_Azure_Webp();