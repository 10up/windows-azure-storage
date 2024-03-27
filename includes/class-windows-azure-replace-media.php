<?php

/**
 * Microsoft Azure Storage REST API client.
 *
 * Version: 4.0.0
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
 */


class Windows_Azure_Replace_Media {

	/**
	 * Class constructor
	 *
	 */

	public function __construct() {
		// Add fields to attachment editor
		add_filter( 'attachment_fields_to_edit', array( $this, 'register_azure_fields_attachment_editor' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_replace_media_script' ) );

		// ajax event to replace media
		add_action( 'wp_ajax_nopriv_azure-storage-media-replace', array(  $this, 'process_media_replacement' ) );
		add_action( 'wp_ajax_azure-storage-media-replace', array(  $this, 'process_media_replacement' ) );

		// Ajax event to set transient for replacement
		add_action( 'wp_ajax_nopriv_azure-storage-media-replace-set-transient', array(  $this, 'set_media_replacement_transient' ) );
		add_action( 'wp_ajax_azure-storage-media-replace-set-transient', array(  $this, 'set_media_replacement_transient' ) );

	}


	/**
	 * Register replace media button
	 *
	 * @param array   $form_fields Form fields
	 * @param WP_Post $post the post ID
	 * @return array
	 */
	public function register_azure_fields_attachment_editor( $form_fields, $post ) {
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( ! is_null( $screen ) && 'attachment' === $screen->id ) {
					return $form_fields;
			}
		}
		wp_enqueue_media();
		$mime_type = get_post_mime_type( $post->ID );
		if ( 'application/pdf' === $mime_type ) {
			$form_fields['azure-media-replace'] = array(
				'label' => esc_html__( 'Replace media', 'windows-azure-storage' ),
				'input' => 'html',
				'html'  => '<button class="button-secondary" id="azure-media-replacement" onclick="replaceMedia(' . $post->ID . ');">' . esc_html__( 'Replace this media', 'windows-azure-storage' ) . '</button>',
			);
		}
	
		return $form_fields;
	}

	/**
	 * Enqueue media replace script
	 *
	 * @return void
	 */
	public function enqueue_replace_media_script() {
		$js_ext  = ( ! defined( 'SCRIPT_DEBUG' ) || false === SCRIPT_DEBUG ) ? '.min.js' : '.js';
		wp_enqueue_script( 'windows-azure-storage-media-replace', MSFT_AZURE_PLUGIN_URL . 'js/windows-azure-storage-media-replace' . $js_ext, array( 'jquery', 'media-editor' ), MSFT_AZURE_PLUGIN_VERSION, true );
		
		wp_localize_script(
			'windows-azure-storage-media-replace',
			'AzureMediaReplaceObject',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'azure-storage-media-replace' ),
				'i18n'    => array(
					'title'              => __( 'Replace this media', 'windows-azure-storage' ),
					'replaceMediaButton' => __( 'Replace media', 'windows-azure-storage' ),
				),
			) 
		);
	}

	/**
	 * Ajax handler to process replace request
	 *
	 * @return void
	 */
	public function process_media_replacement() {

		$nonce = sanitize_text_field( $_POST['nonce'] );

		if ( ! wp_verify_nonce( $nonce, 'azure-storage-media-replace' ) ) {
				die ( 'nope' );
		}

		$current_attachment = filter_input( INPUT_POST, 'current_attachment', FILTER_VALIDATE_INT );
		$replace_attachment = filter_input( INPUT_POST, 'replace_attachment', FILTER_VALIDATE_INT );

		echo wp_json_encode( $this->replace_media_with( $current_attachment, $replace_attachment ) );

		wp_die();
	}

	/**
	 * Replace media id with another id
	 *
	 * @param int $source_attachment_id Source attachment ID
	 * @param int $media_to_replace_id Replacement file ID
	 * @return Array
	 */
	private function replace_media_with( $source_attachment_id, $media_to_replace_id ) {
		if ( empty( $source_attachment_id ) || empty( $media_to_replace_id ) ) {
			return __( 'Cannot determine images IDs, aborting...', 'windows-azure-storage' );
		}

		$source_file  = get_post_meta( $source_attachment_id, '_wp_attached_file', true );
		$replace_file = get_post_meta( $media_to_replace_id, '_wp_attached_file', true );

		if ( empty( $source_file ) || empty( $replace_file ) ) {
			return __( 'Path issues, aborting...', 'windows-azure-storage' );
		}

		$source_filetype  = wp_check_filetype( $source_file );
		$replace_filetype = wp_check_filetype( $replace_file );

		if ( empty( $source_filetype['type'] ) && empty( $replace_filetype['type'] ) ) {
			return __( 'Cannot determine file type, aborting...', 'windows-azure-storage' );
		}

		$source_media_type  = explode( '/', $source_filetype['type'] );
		$replace_media_type = explode( '/', $replace_filetype['type'] );

		if ( ( is_array( $source_media_type ) && is_array( $replace_filetype ) ) && ( $source_media_type[0] !== $replace_media_type[0] ) ) {
			return __( 'File type mismatch', 'windows-azure-storage' );
		}

		// Let's replace the file remotely
		$default_azure_storage_account_container_name = \Windows_Azure_Helper::get_default_container();

		// only upload file if file exists locally
		try {
			$full_blob_url = \Windows_Azure_Helper::get_full_blob_url( $replace_file );
			if ( ! empty( $full_blob_url ) ) {
				\Windows_Azure_Helper::copy_media_to_blob_storage(
					$default_azure_storage_account_container_name,
					$replace_file,
					$source_file
				);
			}
		} catch ( Exception $e ) {
			echo '<p>', sprintf( __( 'Error in uploading file. Error: %s', 'windows-azure-storage' ), esc_html( $e->getMessage() ) ), '</p>';
		}

		$replacement = array();
		
		$replacement['is_image']  = $this->is_image( $source_filetype );
		$replacement['file_name'] = basename( $replacement['original_image'] );
		
		$replacement = $this->media_meta_replacement( $source_attachment_id, $media_to_replace_id );

		return $replacement;
	}

	/**
	 * If Attachment is an image
	 *
	 * @param string $filetype mime type
	 * @return boolean
	 */
	private function is_image( $filetype ) {
		return ( strpos( $filetype['type'], 'image' ) !== false );
	}

	/**
	 * Media meta replacement and copy
	 *
	 * @param int $source_attachment_id Source attachment ID
	 * @param int $media_to_replace_id Replacement file ID
	 * @return array
	 */
	private function media_meta_replacement( $source_attachment_id, $media_to_replace_id ) {
		$replacement_meta_attachment_file = get_post_meta( $media_to_replace_id, '_wp_attached_file', true );
		$replacement_azure_data           = get_post_meta( $media_to_replace_id, 'windows_azure_storage_info', true );
		$replacement_attachment_data      = get_post_meta( $media_to_replace_id, '_wp_attachment_metadata', true );

		$source_meta_attachment_file = get_post_meta( $source_attachment_id, '_wp_attached_file', true );
		$source_azure_data           = get_post_meta( $source_attachment_id, 'windows_azure_storage_info', true );
		$source_attachment_data      = get_post_meta( $source_attachment_id, '_wp_attachment_metadata', true );
		$source_attachment_version   = get_post_meta( $source_attachment_id, '_wp_attachment_replace_version', true );
		if ( empty( $source_attachment_data ) ) {
			$source_attachment_version = 1;
		}
		$new_version = ++$source_attachment_version;

		$return_data = array();

		$return_data['ID']     = $source_attachment_id;
		$return_data['old_ID'] = $media_to_replace_id;

		$source_filename  = pathinfo( basename( $source_meta_attachment_file ) );
		$replace_filename = pathinfo( basename( $replacement_meta_attachment_file ) );

		if ( 'pdf' === $source_filename['extension'] ) {
			unset( $source_attachment_data['sizes'] );
			if ( ! empty( $replacement_attachment_data['sizes'] ) ) {
				foreach ( $replacement_attachment_data['sizes'] as $size_key => $size_data ) {
					$size_data['file'] = str_replace( $replace_filename, $source_filename, $size_data['file'] );
					$source_attachment_data['sizes'][ $size_key ] = $size_data;
				}

				update_post_meta( $source_attachment_id, '_wp_attachment_metadata', $source_attachment_data );
			}

			if ( ! empty( $replacement_azure_data['thumbnails'] ) ) {
				// Let's replace the file remotely
				$default_azure_storage_account_container_name = \Windows_Azure_Helper::get_default_container();
				
				unset( $source_azure_data['thumbnails'] );
				
				foreach ( $replacement_azure_data['thumbnails'] as $thumbnails ) {
					$new_filename                      = str_replace( $replace_filename, $source_filename, $thumbnails );
					$source_azure_data['thumbnails'][] = $new_filename;
					try {
						$full_blob_url = \Windows_Azure_Helper::get_full_blob_url( $thumbnails );
						if ( ! empty( $full_blob_url ) ) {
							\Windows_Azure_Helper::copy_media_to_blob_storage(
								$default_azure_storage_account_container_name,
								$thumbnails,
								$new_filename,
							);
						}
					} catch ( Exception $e ) {
						echo '<p>', sprintf( __( 'Error in uploading file. Error: %s', 'windows-azure-storage' ), esc_html( $e->getMessage() ) ), '</p>';
					}
				}

				update_post_meta( $source_attachment_id, 'windows_azure_storage_info', $source_azure_data );
			}

			wp_delete_attachment( $media_to_replace_id, true );
		}

		$return_data['original_image']  = $source_filename;
		$return_data['attachment_data'] = $source_attachment_data;
		$return_data['attachment_data'] = $source_attachment_data;
		$return_data['azure_data']      = $source_azure_data;
		$return_data['version']         = $new_version;

		return $return_data;
	}
}
