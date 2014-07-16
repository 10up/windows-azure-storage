<?php
/**
 * Plugin Name: Windows Azure Storage for WordPress
 * 
 * Plugin URI: http://www.wordpress.org/extend/plugins/windows-azure-storage/
 * 
 * Description: This WordPress plugin allows you to use Windows Azure Storage Service to host your media for your WordPress powered blog.
 * 
 * Version: 2.2
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
 * 'Windows Azure SDK for PHP v<TODO>' and its dependencies are included 
 * in the library directory, this will override 'Windows Azure SDK
 * for PHP' is already installed in the machine if USESDKINSTALLEDGLOBALLY
 * is not defined.
 * 'Windows Azure SDK for PHP' provide access to underlying Windows Azure 
 * Blob Storage
 * https://github.com/windowsazure/azure-sdk-for-php/
 */

require_once "library/WindowsAzure/WindowsAzure.php";
// include path to dependencies in the include_path
$path = dirname(__FILE__) . '/library/dependencies';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

// import namepaces required for consuming Azure Blob Storage
use WindowsAzure\Blob\BlobService;
use WindowsAzure\Blob\BlobSettings;
use WindowsAzure\Blob\Models\CreateContainerOptions;
use WindowsAzure\Blob\Models\PublicAccessType;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\Common\ServicesBuilder;
use windowsazure\common\internal\resources;
use WindowsAzure\Blob\Models\Block;
use WindowsAzure\Blob\Models\BlobBlockType;
use WindowsAzure\Blob\Models\CreateBlobOptions;
use WindowsAzure\Blob\Models\CommitBlobBlocksOptions;
use WindowsAzure\Blob\Models\ContainerAcl;

require_once 'windows-azure-storage-settings.php';
require_once 'windows-azure-storage-dialog.php';
require_once 'windows-azure-storage-util.php';

// Check prerequisite for plugin
register_activation_hook(__FILE__, 'check_prerequisite');

add_action('admin_menu', 'windows_azure_storage_plugin_menu');
add_filter('media_buttons_context', 'windows_azure_storage_media_buttons_context');

/**
 * Define and return tabs for Windows Azure Storage Dialog.
 *
 * @param array  $tabs  Array of existing tabs.
 *
 * @return array Returns array of new tabs
 */
function azure_storage_media_menu($tabs) {
  $newtab = array('browse' => __('Browse', 'storagebrowse'),
      'search' => __('Search', 'storagesearch'),
      'upload' => __('Upload', 'storageupload'));
  return array_merge($tabs, $newtab);
}

// Hook for adding tabs
add_filter('media_upload_tabs', 'azure_storage_media_menu');

// Add callback for three tabs in the Windows Azure Storage Dialog
add_action("media_upload_browse", "browse_tab");
add_action("media_upload_search", "search_tab");
add_action("media_upload_upload", "upload_tab");

// Hooks for handling default file uploads
if (get_option('azure_storage_use_for_default_upload') == 1) {
    add_filter(
        'wp_update_attachment_metadata', 
        'windows_azure_storage_wp_update_attachment_metadata', 
        9, 
        2
    );
    
    // Hook for handling blog posts via xmlrpc. This is not full proof check
    add_filter('content_save_pre', 'windows_azure_storage_content_save_pre');
    
    //TODO: implement wp_unique_filename filter once it is available in WordPress
    add_filter('wp_handle_upload_prefilter', 'windows_azure_storage_wp_handle_upload_prefilter');

    // Hook for handling media uploads
    add_filter('wp_handle_upload', 'windows_azure_storage_wp_handle_upload');
    
    // Filter to modify file name when XML-RPC is used
    //TODO: remove this filter when wp_unique_filename filter is available in WordPress
    add_filter( 'xmlrpc_methods', 'windows_azure_storage_xmlrpc_methods');
}

// Hook for acecssing attachment (media file) URL
add_filter(
    'wp_get_attachment_url',
    'windows_azure_storage_wp_get_attachment_url', 
    9, 
    2
);

// Hook for acecssing metadata about attachment (media file)
add_filter(
    'wp_get_attachment_metadata', 
    'windows_azure_storage_wp_get_attachment_metadata', 
    9, 
    2
);

// Hook for handling deleting media files from standard WordpRess dialog
add_action('delete_attachment', 'windows_azure_storage_delete_attachment');


/**
 * Check prerequisite for the plugin and report error
 *
 * @return void
 */ 
function check_prerequisite()
{
    $windowsAzureFilePath = WP_PLUGIN_DIR . "/windows-azure-storage/library/WindowsAzure/WindowsAzure.php";
    if ((file_exists($windowsAzureFilePath) === true) && (is_readable($windowsAzureFilePath) === true)) {
        return;
    }

    // Windows Azure SDK for PHP is not available
    $message = '<p style="color: red"><a href="https://github.com/windowsazure/azure-sdk-for-php/">'
        . 'Windows Azure SDK for PHP</a> is not found. ' 
        . 'Please download and copy the Windows Azure SDK for PHP to library directory and dependencies to '
        . 'to dependencies directory </p>';

    if (function_exists('deactivate_plugins')) { 
        deactivate_plugins(__FILE__); 
    } else {
        $message = $message . '<p style="color: red"><strong>' 
            . 'Please deactivate this plugin Immediately</strong></p>';
    }

    die($message);
}

/**
 * Replacing the callback for XML-RPC metaWeblog.newMediaObject
 *
 * @param array $methods XML-RPC methods
 *
 * @return array $methods Modified XML-RPC methods
 */
function windows_azure_storage_xmlrpc_methods($methods) {
	$methods['metaWeblog.newMediaObject'] = 'windows_azure_storage_newMediaObject';
	return $methods;
}

/**
 * Upload a file
 * Added unique blob name to the WordPress core mw_newMediaObject method
 *
 * @param array $args Method parameters
 *
 * @return array
 */
function windows_azure_storage_newMediaObject($args) {
	global $wpdb, $wp_xmlrpc_server;

	$blog_ID     = (int) $args[0];
	$username  = $wp_xmlrpc_server->escape($args[1]);
	$password   = $wp_xmlrpc_server->escape($args[2]);
	$data        = $args[3];

	$name = sanitize_file_name( $data['name'] );
	$type = $data['type'];
	$bits = $data['bits'];

	if ( !$user = $wp_xmlrpc_server->login($username, $password) )
		return $wp_xmlrpc_server->error;

	/** This action is documented in wp-includes/class-wp-xmlrpc-server.php */
	do_action( 'xmlrpc_call', 'metaWeblog.newMediaObject' );

	if ( !current_user_can('upload_files') ) {
		$wp_xmlrpc_server->error = new IXR_Error( 401, __( 'You do not have permission to upload files.' ) );
		return $wp_xmlrpc_server->error;
	}

	/**
	 * Filter whether to preempt the XML-RPC media upload.
	 *
	 * Passing a truthy value will effectively short-circuit the media upload,
	 * returning that value as a 500 error instead.
	 *
	 * @since 2.1.0
	 *
	 * @param bool $error Whether to pre-empt the media upload. Default false.
	 */
	if ( $upload_err = apply_filters( 'pre_upload_error', false ) ) {
		return new IXR_Error( 500, $upload_err );
	}

	if ( !empty($data['overwrite']) && ($data['overwrite'] == true) ) {
		// Get postmeta info on the object.
		$old_file = $wpdb->get_row("
			SELECT ID
			FROM {$wpdb->posts}
			WHERE post_title = '{$name}'
				AND post_type = 'attachment'
		");

		// Delete previous file.
		wp_delete_attachment($old_file->ID);

		// Make sure the new name is different by pre-pending the
		// previous post id.
		$filename = preg_replace('/^wpid\d+-/', '', $name);
		$name = "wpid{$old_file->ID}-{$filename}";
	}

	//default azure storage container
	$container = WindowsAzureStorageUtil::getDefaultContainer();

	$uploadDir = wp_upload_dir();
	if ($uploadDir['subdir'][0] == "/" ) {
		$uploadDir['subdir'] = substr($uploadDir['subdir'], 1);
	}

	// Prepare blob name
	$blobName = ($uploadDir['subdir'] == "") ? $name : $uploadDir['subdir'] . "/" . $name;

	$blobName = WindowsAzureStorageUtil::uniqueBlobName($container, $blobName);

	$name = basename($blobName);

	$upload = wp_upload_bits($name, null, $bits);
	if ( ! empty($upload['error']) ) {
		$errorString = sprintf(__('Could not write file %1$s (%2$s)'), $name, $upload['error']);
		return new IXR_Error(500, $errorString);
	}
	// Construct the attachment array
	$post_id = 0;
	if ( ! empty( $data['post_id'] ) ) {
		$post_id = (int) $data['post_id'];

		if ( ! current_user_can( 'edit_post', $post_id ) )
			return new IXR_Error( 401, __( 'Sorry, you cannot edit this post.' ) );
	}
	$attachment = array(
		'post_title' => $name,
		'post_content' => '',
		'post_type' => 'attachment',
		'post_parent' => $post_id,
		'post_mime_type' => $type,
		'guid' => $upload[ 'url' ]
	);

	// Save the data
	$id = wp_insert_attachment( $attachment, $upload[ 'file' ], $post_id );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );

	/**
	 * Fires after a new attachment has been added via the XML-RPC MovableType API.
	 *
	 * @since 3.4.0
	 *
	 * @param int   $id   ID of the new attachment.
	 * @param array $args An array of arguments to add the attachment.
	 */
	do_action( 'xmlrpc_call_success_mw_newMediaObject', $id, $args );

	$struct = array(
		'id'   => strval( $id ),
		'file' => $upload['file'],
		'url'  => $upload[ 'url' ],
		'type' => $type
	);

	/** This filter is documented in wp-admin/includes/file.php */
	return apply_filters( 'wp_handle_upload', $struct, 'upload' );
}

/**
 * Wordpress hook for wp_get_attachment_url
 * 
 * @param string  $url    post url 
 *
 * @param integer $postID post id
 *
 * @return string Returns metadata url
 */
function windows_azure_storage_wp_get_attachment_url($url, $postID)
{
    $mediaInfo = get_post_meta($postID, 'windows_azure_storage_info', true);

    if (!empty($mediaInfo)) {
        return $mediaInfo['url'];
    } else {
        return $url;
    }
}

/**
 * Wordpress hook for wp_get_attachment_metadata. Cache mediainfo for 
 * further reference.
 * 
 * @param string  $data    attachment data
 *
 * @param integer $postID  Associated post id
 *
 * @return array Return input data without modification
 */
function windows_azure_storage_wp_get_attachment_metadata($data, $postID)
{
    if (is_numeric($postID)) {
        // Cache this metadata. Needed for deleting files
        $mediaInfo = get_post_meta($postID, 'windows_azure_storage_info', true);
    }

    return $data;
}

/**
 * Wordpress hook for wp_update_attachment_metadata, hook for handling
 * default media file upload in wordpress.
 * 
 * @param string  $data    attachment data
 *
 * @param integer $postID  Associated post id
 *
 * @return array data after updating information about blob storage URL and tags
 */
function windows_azure_storage_wp_update_attachment_metadata($data, $postID)
{
    $default_azure_storage_account_container_name 
        = WindowsAzureStorageUtil::getDefaultContainer();
    // Get full file path of uploaded file
    $uploadFileName = get_attached_file($postID, true);

    // If attachment metadata is empty (for video), generate correct blob names
    if (empty($data) || empty($data['file'])) {
        // Get upload directory
        $uploadDir = wp_upload_dir();
        if ($uploadDir['subdir']{0} == "/" ) {
            $uploadDir['subdir'] = substr($uploadDir['subdir'], 1);
        }

        // Prepare blob name
        $relativeFileName = ($uploadDir['subdir'] == "") ? 
                                basename($uploadFileName) : 
                                $uploadDir['subdir'] . "/" . basename($uploadFileName);
    } else {
        // Prepare blob name
        $relativeFileName = $data['file'];
    }
    
    try {
        // Get full file path of uploaded file
        $data['file'] = $uploadFileName;

        // Get mime-type of the file
        $mimeType = get_post_mime_type($postID);

        try {
            WindowsAzureStorageUtil::putBlockBlob(
                $default_azure_storage_account_container_name,
                $relativeFileName,
                $uploadFileName,
                $mimeType, 
                array(
                    'tag' => "WordPressDefaultUpload", 
                    'mimetype' => $mimeType
                )
            );
        } catch (Exception $e) {
            echo "<p>Error in uploading file. Error: " . $e->getMessage() . "</p><br/>";
            return $data;
        }
        
        $url = WindowsAzureStorageUtil::getStorageUrlPrefix() 
            . "/" . $relativeFileName;
            
        // Set new url in returned data
        $data['url'] = $url;
        
        // Handle thumbnail and medium size files
        $thumbnails = Array();
        if (!empty($data["sizes"])) {
            $file_upload_dir = substr($relativeFileName, 0, strripos($relativeFileName, "/"));
            
            foreach ($data["sizes"] as $size) {
                // Do not prefix file name with wordpress upload folder path
                $sizeFileName = dirname($data['file']) . "/" . $size["file"];

                // Move only if file exists. Some theme may use same file name for multiple sizes
                if (file_exists($sizeFileName)) {
                    $blobName = ($file_upload_dir == "") ? $size["file"] : $file_upload_dir . "/" . $size["file"];                  
                    WindowsAzureStorageUtil::putBlockBlob(
                        $default_azure_storage_account_container_name,
                        $blobName,
                        $sizeFileName,
                        $mimeType,
                        array(
                            'tag' => "WordPressDefaultUploadSizesThumbnail",
                            'mimetype' => $mimeType
                        )
                    );

                    $thumbnails[] = $blobName;
                
                    // Delete the local thumbnail file
                    unlink($sizeFileName);
                }
            }
        }
        
        delete_post_meta($postID, 'windows_azure_storage_info');

        add_post_meta(
            $postID, 'windows_azure_storage_info', 
            array(
                'container' => $default_azure_storage_account_container_name, 
                'blob' => $relativeFileName, 
                'url' => $url,
                'thumbnails' => $thumbnails
            )
        );
        
        // Delete the local file
        unlink($uploadFileName);
    } catch (Exception $e) {
        echo "<p>Error in uploading file. Error: " . $e->getMessage() . "</p><br/>";
    }

    return $data;
}

/**
 * Hook for updating post content prior to saving it in the database
 *
 * @param string $text post content
 *
 * @return string Updated post content
 */  
function windows_azure_storage_content_save_pre($text)
{
    return getUpdatedUploadUrl($text);
}

/**
 * TODO: Implement wp_unique_filename filter once its available in WordPress.
 *
 * Hook for altering the file name.
 * Check whether the blob exists in the container and generate a unique file name for the blob.
 *
 * @param array $file An array of data for a single file.
 *
 * @return array Updated file data.
 */
function windows_azure_storage_wp_handle_upload_prefilter($file)
{
	//default azure storage container
	$container = WindowsAzureStorageUtil::getDefaultContainer();

	$uploadDir = wp_upload_dir();
	if ($uploadDir['subdir'][0] == "/" ) {
		$uploadDir['subdir'] = substr($uploadDir['subdir'], 1);
	}

	// Prepare blob name
	$blobName = ($uploadDir['subdir'] == "") ? $file['name'] : $uploadDir['subdir'] . "/" . $file['name'];

	$blobName = WindowsAzureStorageUtil::uniqueBlobName($container, $blobName);

	$file['name'] = basename($blobName);

	return $file;
}

/**
 * Hook for handling media uploads
 * 
 * @param array $uploads upload metadata
 * 
 * @return array updated metadata
 */ 
function windows_azure_storage_wp_handle_upload($uploads)
{
    $wp_upload_dir = wp_upload_dir();
    $uploads['url'] = WindowsAzureStorageUtil::getStorageUrlPrefix() 
        . $wp_upload_dir['subdir'] . "/" . basename($uploads['file']);
    return $uploads;
}

/**
 * Common function to replace wordpress file uplaod url with 
 * Windows Azure Storage URLs
 *
 * @param string $url original upload URL
 *
 * @return string Updated upload URL
 */
function getUpdatedUploadUrl($url)
{
    $wp_upload_dir = wp_upload_dir();
    $upload_dir_url = $wp_upload_dir['baseurl'];
    $storage_url_prefix = WindowsAzureStorageUtil::getStorageUrlPrefix();
    
    return str_replace($upload_dir_url, $storage_url_prefix, $url);
}

/**
 * Hook for handling deleting media files from standard WordpRess dialog
 *
 * @param string $postID post id
 *
 * @return void
 */ 
function windows_azure_storage_delete_attachment($postID)
{
    if (is_numeric($postID)) {
        $mediaInfo = get_post_meta($postID, 'windows_azure_storage_info', true);

        if (!empty($mediaInfo)) {
            // Delete media file from blob storage
            $containerName = $mediaInfo['container'];
            $blobName = $mediaInfo['blob'];
            WindowsAzureStorageUtil::deleteBlob($containerName, $blobName);

            // Delete associated thumbnails from blob storage (if any)
            $thumbnails = $mediaInfo['thumbnails'];
            if (!empty($thumbnails)) {
                foreach ($thumbnails as $thumbnail_blob) {
                    WindowsAzureStorageUtil::deleteBlob($containerName, $thumbnail_blob);
                }
            }
        }
    }
}

/**
 * Add Browse tab to the popup windows
 *
 * @return void
 */
function browse_tab()
{
    add_action('admin_print_scripts', 'windows_azure_storage_dialog_scripts');
    wp_enqueue_style('media');
    wp_iframe('windows_azure_storage_dialog_browse_tab');
}

/**
 * Add Search tab to the popup windows
 *
 * @return void
 */
function search_tab()
{
    add_action('admin_print_scripts', 'windows_azure_storage_dialog_scripts');
    wp_enqueue_style('media');
    wp_iframe('windows_azure_storage_dialog_search_tab');
}

/**
 * Add Upload tab to the popup windows
 *
 * @return void
 */
function upload_tab()
{
    add_action('admin_print_scripts', 'windows_azure_storage_dialog_scripts');
    wp_enqueue_style('media');
    wp_iframe('windows_azure_storage_dialog_upload_tab');
}

/**
 * Hook for adding new toolbar button in edit post page
 * 
 * @param string $context context for edit page
 * 
 * @return string updated context
 */
function windows_azure_storage_media_buttons_context($context)
{
    global $post_ID, $temp_ID;
    
    $image_btn = "../wp-content/plugins/windows-azure-storage/images/WindowsAzure.jpg";
    $image_title = 'Windows Azure Storage';
    
    $uploading_iframe_ID = (int)(0 == $post_ID ? $temp_ID : $post_ID);
    $media_upload_iframe_src = "media-upload.php?post_id=$uploading_iframe_ID";
    
    $browse_iframe_src = apply_filters(
        'browse_iframe_src', 
        "$media_upload_iframe_src&amp;tab=browse"
    );
    $search_iframe_src = apply_filters(
        'browse_iframe_src', 
        "$media_upload_iframe_src&amp;tab=search"
    );
    $upload_iframe_src = apply_filters(
        'browse_iframe_src', 
        "$media_upload_iframe_src&amp;tab=upload"
    );
    $createcontainer_iframe_src = apply_filters(
        'browse_iframe_src', 
        "$media_upload_iframe_src&amp;tab=createcontainer"
    );

    $out = ' <a href="' . $media_upload_iframe_src . 
        '&tab=WindowsAzureStorageTab&TB_iframe=true&height=500&width=640"' . 
        'class="thickbox" title="' . $image_title . '"><img src="' . $image_btn 
        . '" width="20" Height="20" alt="' . $image_title . '" /></a>';
        
    $out = ' <a href="' . $browse_iframe_src 
        . '&TB_iframe=true&height=500&width=640" class="thickbox" title="' 
        . $image_title . '"><img src="' . $image_btn 
        . '" width="20" Height="20" alt="' . $image_title . '" /></a>';
    
    return $context . $out;
}

/**
 * Add option page for Windows Azure Storage Plugin
 * 
 * @return void
 */
function windows_azure_storage_plugin_menu()
{
    add_options_page(
        'Windows Azure Storage Plugin Settings', 
        'Windows Azure', 
        'manage_options', 
        'b5506889-50de-42db-bf63-e9f248ca94e9', 
        'windows_azure_storage_plugin_options_page'
    );

    // Call register settings function
    add_action('admin_init', 'windows_azure_storage_plugin_register_settings');
}
?>
