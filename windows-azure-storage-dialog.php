<?php
/**
 * Shows popup dialog when clicked on the Microsoft Azure Toolbar
 *
 * Version: 3.0.1
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

/**
 * Enqueue JavaScript and CSS needed by the settings page dialog.
 *
 * @internal Callback for 'admin_enqueue_scripts'.
 * @since    3.0.0 Moved to a callback for 'admin_enqueue_scripts' instead of 'admin_print_scripts'.
 *
 * @param string $hook_suffix The hook of the current admin page.
 */
function windows_azure_storage_dialog_scripts( $hook_suffix ) {
	$js_ext  = ( ! defined( 'SCRIPT_DEBUG' ) || false === SCRIPT_DEBUG ) ? '.min.js' : '.js';
	$css_ext = ( ! defined( 'SCRIPT_DEBUG' ) || false === SCRIPT_DEBUG ) ? '.min.css' : '.css';
	wp_enqueue_script( 'windows-azure-storage-admin', MSFT_AZURE_PLUGIN_URL . 'js/windows-azure-storage-admin' . $js_ext, array(), MSFT_AZURE_PLUGIN_VERSION );
	wp_enqueue_style( 'windows-azure-storage-style', MSFT_AZURE_PLUGIN_URL . 'css/windows-azure-storage' . $css_ext, array(), MSFT_AZURE_PLUGIN_VERSION );
	wp_localize_script( 'windows-azure-storage-admin', 'azureStorageConfig', array(
		'l10n' => array(
			'uploadingToAzure' => __( 'Uploading to Azure', 'windows-azure-storage' ),
		),
	) );
}

add_action( 'admin_enqueue_scripts', 'windows_azure_storage_dialog_scripts' );

/**
 * Delete a blob from specified container
 *
 * @param string $container_name Name of the parent container.
 * @param string $blob_name      Name of the blob to be deleted.
 *
 * @deprecated 4.0
 *
 * @return void
 */
function deleteBlob( $container_name, $blob_name ) {
	_deprecated_function( __FUNCTION__, '4.0' );
	try {
		if ( WindowsAzureStorageUtil::blobExists( $container_name, $blob_name ) ) {
			$_SERVER['REQUEST_URI'] = remove_query_arg(
				array(
					'delete_blob',
					'filename',
					'selected_container',
				),
				$_SERVER['REQUEST_URI']
			);
			WindowsAzureStorageUtil::deleteBlob( $container_name, $blob_name );
		}
	} catch ( Exception $e ) {
		/* translators: 1: blob (file) name, 2: container name, 3: error message */
		$message = sprintf(
			__( 'Error in deleting blob %1$s from container %2$s: %3$s', 'windows-azure-storage' ),
			$blob_name,
			$container_name,
			$e->getMessage()
		);
		echo '<p class="warning">' . esc_html( $message ) . '</p>';
	}
}

/**
 * Generate ISO 8601 compliant date string in UTC time zone
 *
 * @param int $timestamp Input timestamp for conversion.
 *
 * @deprecated 4.0
 *
 * @return string
 */
function isoDate( $timestamp = null ) {
	_deprecated_function( __FUNCTION__, '4.0' );
	$tz = @date_default_timezone_get();
	@date_default_timezone_set( 'UTC' );
	if ( is_null( $timestamp ) ) {
		$timestamp = time();
	}

	$return_value = str_replace( '+00:00', 'Z', @date( 'c', $timestamp ) );
	@date_default_timezone_set( $tz );

	return $return_value;
}
