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

		//ajax event to replace media
		add_action( 'wp_ajax_nopriv_azure-storage-media-replace', array(  $this, 'process_media_replacement' ) );
		add_action( 'wp_ajax_azure-storage-media-replace', array(  $this, 'process_media_replacement' ) );
	}


	public function register_azure_fields_attachment_editor( $form_fields, $post ) {
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( ! is_null( $screen ) && 'attachment' === $screen->id ) {
					return $form_fields;
			}
		}
		wp_enqueue_media();

		$form_fields['azure-media-replace'] = array(
			'label' => esc_html__( 'Replace media', 'windows-azure-storage' ),
			'input' => 'html',
			'html'  => '<button class="button-secondary" id="azure-media-replacement" onclick="replaceMedia(' . $post->ID . ');">' . esc_html__( 'Replace this media', 'windows-azure-storage' ) . '</button>',
		);
	
		return $form_fields;
	}

	public function enqueue_replace_media_script() {
		$js_ext  = ( ! defined( 'SCRIPT_DEBUG' ) || false === SCRIPT_DEBUG ) ? '.min.js' : '.js';
		wp_enqueue_script( 'windows-azure-storage-media-replace', MSFT_AZURE_PLUGIN_URL . 'js/windows-azure-storage-media-replace' . $js_ext, array( 'jquery', 'media-editor' ), MSFT_AZURE_PLUGIN_VERSION, true );
		
		wp_localize_script( 'windows-azure-storage-media-replace', 'AzureMediaReplaceObject', array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'nonce'   => wp_create_nonce( 'azure-storage-media-replace' ),
			'i18n'    => array(
				'title'              => __( 'Replace this media', 'windows-azure-storage' ),
				'replaceMediaButton' => __( 'Replace media', 'windows-azure-storage' ),
			)
		) );
	}

	public function process_media_replacement() {

		$nonce = sanitize_text_field( $_POST['nonce'] );

    if ( ! wp_verify_nonce( $nonce, 'azure-storage-media-replace' ) ) {
        die ( 'nope' );
    }

		$current_attachment = filter_input( INPUT_POST, 'current_attachment', FILTER_VALIDATE_INT );
		$replace_attachment = filter_input( INPUT_POST, 'replace_attachment', FILTER_VALIDATE_INT );

		echo json_encode( $this->replace_media_with( $current_attachment, $replace_attachment ) );

		wp_die();
	}

	private function replace_media_with( $source_attachment_id, $media_to_replace_id ) {
		if ( empty( $source_attachment_id ) || empty( $media_to_replace_id ) ) {
			return __( 'Cannot determine images IDs, aborting...', 'windows-azure-storage' );;
		}

		$source_file  = get_attached_file( $source_attachment_id );
		$replace_file = get_attached_file( $media_to_replace_id );

		if ( empty( $source_file ) || empty( $replace_file ) ) {
			return __( 'Path issues, aborting...', 'windows-azure-storage' );
		}

		$source_filetype  = wp_check_filetype( $source_file );
		$replace_filetype = wp_check_filetype( $replace_file );

		if ( ! $source_filetype['type'] && ! $replace_filetype ) {
			return __( 'Cannot determine file type, aborting...', 'windows-azure-storage' );
		}

		$source_media_type = explode( '/', $source_filetype['type'] );
		$replace_media_type = explode( '/', $replace_filetype['type'] );

		if ( reset( $source_media_type ) !== reset( $replace_media_type ) ) {
			return __( 'File type mismatch', 'windows-azure-storage' );
		}

		$replacement = array();

		$check = apply_filters( 'windowz-azure-storage-delete-files-on-replacement', true );
		if ( $check ) {
			$this->remove_attachment_files( $source_attachment_id );
		}

		$replacement = $this->media_replacement( $source_attachment_id, $media_to_replace_id );
		$replacement['is_image'] = $this->is_image( $source_filetype );
		$replacement['file_name'] = basename( $replacement['original_image'] );

		$this->remove_attachment_post( $media_to_replace_id );

		return $replacement;

	}

	private function is_image( $filetype ) {
		return ( strpos( $filetype['type'], 'image' ) !== false );
	}

	private function media_replacement( $source_attachment_id, $media_to_replace_id ) {
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

		// Save data to be replaced just in case
		update_post_meta( $source_attachment_id, 'prev_wp_attached_file', $source_meta_attachment_file );
		update_post_meta( $source_attachment_id, 'prev_wp_attachment_metadata', $source_attachment_data );
		update_post_meta( $source_attachment_id, 'prev_windows_azure_storage_info', $source_azure_data );

		$return_data = array();

		$return_data['ID'] = $source_attachment_id;
		$return_data['old_ID'] = $media_to_replace_id;

		// Replace data from the new 
		if (
			update_post_meta( $source_attachment_id, '_wp_attached_file', $replacement_meta_attachment_file )
		) {
			$return_data['original_image'] = $replacement_meta_attachment_file;
		}

		if (
			update_post_meta( $source_attachment_id, '_wp_attachment_metadata', $replacement_attachment_data )
		) {
			$return_data['attachment_data'] = $replacement_attachment_data;
		}

		if (
			update_post_meta( $source_attachment_id, 'windows_azure_storage_info', $replacement_azure_data )
		) {
			$return_data['azure_data'] = $replacement_azure_data;
		}

		if (
			update_post_meta( $source_attachment_id, '_wp_attachment_replace_version', $new_version )
		) {
			$return_data['version'] = $new_version;
		}

		return $return_data;
	}

	private function remove_attachment_files( $attachment_id ) {
		$meta         = wp_get_attachment_metadata( $attachment_id );
		$backup_sizes = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );
		$file         = get_attached_file( $attachment_id );

		return wp_delete_attachment_files( $attachment_id, $meta, $backup_sizes, $file );

	}

	private function remove_attachment_post( $attachment_id ) {
		global $wpdb;

		if ( empty( $attachment_id ) ) {
			return false;
		}

		wp_delete_object_term_relationships( $attachment_id, array( 'category', 'post_tag' ) );
		wp_delete_object_term_relationships( $attachment_id, get_object_taxonomies( 'attachment' ) );

		// Delete all for any posts.
		delete_metadata( 'post', null, '_thumbnail_id', $attachment_id, true );

		wp_defer_comment_counting( true );

	$comment_ids = $wpdb->get_col( $wpdb->prepare( "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = %d ORDER BY comment_ID DESC", $attachment_id ) );
	foreach ( $comment_ids as $comment_id ) {
		wp_delete_comment( $comment_id, true );
	}

	wp_defer_comment_counting( false );

	$post_meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE post_id = %d ", $attachment_id ) );
	foreach ( $post_meta_ids as $mid ) {
		delete_metadata_by_mid( 'post', $mid );
	}

	delete_post_meta( $attachment_id, '_wp_trash_meta_status' );

	$result = $wpdb->delete( $wpdb->posts, array( 'ID' => $attachment_id ) );

	if ( ! $result ) {
		return false;
	}

	return $attachment_id;

	}
}
