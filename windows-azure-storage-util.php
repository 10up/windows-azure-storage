<?php
/**
 * Various utility functions for accessing Microsoft Azure Storage
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
 * Used for performing operations on Microsoft Azure Blob Storage
 *
 * @category  WordPress_Plugin
 * @package   Windows_Azure_Storage_For_WordPress
 * @author    Microsoft Open Technologies, Inc. <msopentech@microsoft.com>
 * @copyright Microsoft Open Technologies, Inc.
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @link      http://www.microsoft.com
 */
class WindowsAzureStorageUtil {
	/**
	 * Maximal blob size (in bytes)
	 */
	const MAX_BLOB_SIZE = 67108864;

	/**
	 * Maximal block blob transfer size (in bytes)
	 */
	const MAX_BLOB_TRANSFER_SIZE = 4194304;

	/**
	 * Get Microsoft Azure Storage host name defined as per plugin settings
	 *
	 * @deprecated 4.0
	 *
	 * @return string host Name
	 */
	public static function getHostName() {
		_deprecated_function( __FUNCTION__, '4.0', 'Windows_Azure_Helper::get_hostname()' );

		return \Windows_Azure_Helper::get_hostname();
	}

	/**
	 * Get Microsoft Azure Storage account name defined in plugin settings
	 *
	 * @deprecated 4.0 Use Windows_Azure_Helper::get_account_name()
	 *
	 * @return string Account Name
	 */
	public static function getAccountName() {
		_deprecated_function( __METHOD__, '4.0', 'Windows_Azure_Helper::get_account_name()' );

		return Windows_Azure_Helper::get_account_name();
	}

	/**
	 * Get Microsoft Azure Storage account key defined in plugin settings
	 *
	 * @deprecated 4.0 Use Windows_Azure_Helper::get_account_key()
	 *
	 * @return string Account Key
	 */
	public static function getAccountKey() {
		_deprecated_function( __FUNCTION__, '4.0', 'Windows_Azure_Helper::get_account_key()' );

		return Windows_Azure_Helper::get_account_key();
	}

	/**
	 * Get default container name defined in plugin settings
	 *
	 * @deprecated 4.0 Use Windows_Azure_Helper::get_default_container()
	 *
	 * @return string Default container name
	 */
	public static function getDefaultContainer() {
		_deprecated_function( __FUNCTION__, '4.0', 'Windows_Azure_Helper::get_default_container()' );

		return Windows_Azure_Helper::get_default_container();
	}

	/**
	 * Get CNAME to be used for the base URL instead of the domain from Azure.
	 *
	 * @since      1.0.0
	 * @since      3.0.0 Return a (maybe) filtered URL.
	 *
	 * @deprecated 4.0
	 *
	 * @return string CNAME to use for media URLs.
	 */
	public static function getCNAME() {
		_deprecated_function( __METHOD__, '4.0', 'Windows_Azure_Helper::get_cname()' );

		return \Windows_Azure_Helper::get_cname();
	}

	/**
	 * Get HTTP proxy host if the web server needs http proxy for internet
	 *
	 * @deprecated 4.0 Use Windows_Azure_Helper::get_http_proxy_host()
	 *
	 * @return string HTTP proxy host name
	 */
	public static function getHttpProxyHost() {
		_deprecated_function( __FUNCTION__, '4.0', 'Windows_Azure_Helper::get_http_proxy_host()' );

		return Windows_Azure_Helper::get_http_proxy_host();
	}

	/**
	 * Get HTTP proxy port if the web server needs http proxy for internet
	 *
	 * @deprecated 4.0 Use Windows_Azure_Helper::get_http_proxy_port()
	 *
	 * @return string HTTP proxy port number
	 */
	public static function getHttpProxyPort() {
		_deprecated_function( __FUNCTION__, '4.0', 'Windows_Azure_Helper::get_http_proxy_port()' );

		return Windows_Azure_Helper::get_http_proxy_port();
	}

	/**
	 * Get HTTP proxy user-name
	 *
	 * @deprecated 4.0 Use Windows_Azure_Helper::get_http_proxy_username()
	 *
	 * @return string HTTP proxy user-name
	 */
	public static function getHttpProxyUserName() {
		_deprecated_function( __FUNCTION__, '4.0', 'Windows_Azure_Helper::get_http_proxy_username()' );

		return Windows_Azure_Helper::get_http_proxy_username();
	}

	/**
	 * Get HTTP proxy password
	 *
	 * @deprecated 4.0 Use Windows_Azure_Helper::get_http_proxy_password()
	 *
	 * @return string HTTP proxy password
	 */
	public static function getHttpProxyPassword() {
		_deprecated_function( __FUNCTION__, '4.0', 'Windows_Azure_Helper::get_http_proxy_password()' );

		return Windows_Azure_Helper::get_http_proxy_password();
	}

	/**
	 * Create blob storage client using Azure SDK for PHP
	 *
	 * @param string $account_name    Microsoft Azure Storage account name.
	 * @param string $account_key     Microsoft Azure Storage account primary key.
	 * @param string $proxy_host      Http proxy host.
	 * @param string $proxy_port      Http proxy port.
	 * @param string $proxy_user_name Http proxy user name.
	 * @param string $proxy_password  Http proxy password.
	 *
	 * @deprecated 4.0
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public static function getStorageClient(
		$account_name = null,
		$account_key = null,
		$proxy_host = null,
		$proxy_port = null,
		$proxy_user_name = null,
		$proxy_password = null
	) {
		throw new Exception( __( 'Function has been removed.', 'windows-azure-storage' ), -1 );
	}

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
	public static function deleteBlob( $container_name, $blob_name ) {
		_deprecated_function( __METHOD__, '4.0', 'Windows_Azure_Helper::delete_blob()' );
		Windows_Azure_Helper::delete_blob( $container_name, $blob_name );
	}

	/**
	 * Check if a blob exists
	 *
	 * @since      Unknown
	 * @since      3.0.0 Wrapper for blob_exists_in_container().
	 * @see        WindowsAzureStorageUtil::blob_exists_in_container()
	 *
	 * @param string $container_name Name of the parent container.
	 * @param string $blob_name      Name of the blob to be checked.
	 *
	 * @deprecated 4.0
	 *
	 * @return boolean
	 */
	public static function blobExists( $container_name, $blob_name ) {
		_deprecated_function( __FUNCTION__, '4.0', 'WindowsAzureStorageUtil::blob_exists_in_container()' );

		return self::blob_exists_in_container( $blob_name, $container_name );
	}

	/**
	 * Creates a public container
	 *
	 * @param string        $container_name Name of the container to create.
	 * @param BlobRestProxy $storage_client Reference of storage client to use.
	 *
	 * @deprecated 4.0
	 *
	 * @return void
	 */
	public static function createPublicContainer( $container_name, $storage_client = null ) {
		_deprecated_function( __FUNCTION__, '4.0', 'Windows_Azure_Helper::create_container()' );
		Windows_Azure_Helper::create_container( $container_name );
	}

	/**
	 * Modifies the CNAME protocol if needed.
	 *
	 * If the CNAME is configured different than what Azure supports or the current site's protocol,
	 * this will modify it to match, based on the filter's value.
	 *
	 * @since      3.0.0
	 *
	 * @param string $cname The CNAME value set in the plugin options.
	 *
	 * @deprecated 4.0
	 *
	 * @return string The (maybe) new CNAME with the filtered protocol.
	 */
	protected static function _maybe_rewrite_cname( $cname ) {
		_deprecated_function( __FUNCTION__, '4.0' );
		/**
		 * Filter to allow 'https' as the CNAME protocol.
		 *
		 * Microsoft Azure does not support secure protocols for CNAMEs, which causes two problems:
		 * 1. if a CNAME with http is used, it will result in mixed-content warnings;
		 * 2. if a CNAME with https is used, it will result in invalid certificate warnings.
		 * Either of these is likely to get a site blocked from viewing, depending on the browser settings.
		 * We warn against using 'http' with a CNAME when 'is_ssl' is true because of mixed content, and
		 * 'https' with a CNAME in general because of Azure's lack of support, but if you want to
		 * force 'https' with your CNAME, this is the place to do it.
		 *
		 * @since 3.0.0
		 *
		 * @param bool $allow_cname_https Default false.
		 */
		$allow_cname_https = apply_filters( 'windows_azure_storage_allow_cname_https', false );

		if ( 0 === strpos( $cname, 'https://' ) && false === $allow_cname_https ) {
			$cname = str_replace( 'https://', 'http://', $cname );
		}

		return $cname;
	}

	/**
	 * Get the base URL for the blob.
	 *
	 * The base URL can be a CNAME domain or Azure one, with or without the container
	 * name appended. This will generate the correct base URL for an asset after running
	 * through a set of conditional checks.
	 *
	 * @since Unknown
	 * @since 3.0.0 Switched to 'https' for all Azure URLs.
	 *
	 * @param bool $append_container Optional. Whether to append the container name to the URL. Default true.
	 *
	 * @return string|WP_Error The base blob URL for an account, or an error if one can't be found/created.
	 */
	public static function get_storage_url_base( $append_container = true ) {
		$azure_storage_account_name                   = \Windows_Azure_Helper::get_account_name();
		$default_azure_storage_account_container_name = \Windows_Azure_Helper::get_default_container();

		/**
		 * Filter the blob URL protocol to force a specific one.
		 *
		 * @since 3.0.0
		 *
		 * @param string $protocol Default 'https'; also allow 'http' and 'relative' (for protocol-relative URLs).
		 */
		$protocol = apply_filters( 'windows_azure_storage_blob_protocol', 'https' );

		// Whitelist the protocols and fall back to secure if necessary.
		if ( ! in_array( $protocol, array( 'https', 'http', 'relative' ), true ) ) {
			$protocol = 'https';
		}

		if ( 'relative' === $protocol ) {
			$protocol = '//';
		} else {
			$protocol .= '://';
		}

		// Get CNAME if defined.
		$cname = \Windows_Azure_Helper::get_cname();
		if ( ! ( empty( $cname ) ) ) {
			$url = sprintf( '%1$s/%2$s',
				$cname,
				$append_container ? $default_azure_storage_account_container_name : ''
			);
		} else {
			$blob_storage_host_name = \Windows_Azure_Helper::get_hostname();
			$storage_account_name   = \Windows_Azure_Helper::get_account_name();

			if ( Windows_Azure_Helper::DEV_STORE_NAME === $storage_account_name ) {
				// Use development storage.
				$url = sprintf( '%1$s%2%s/%3$s/%4$s',
					$protocol,
					$blob_storage_host_name,
					$azure_storage_account_name,
					$append_container ? $default_azure_storage_account_container_name : ''
				);
			} else {
				// Use cloud storage.
				$url = sprintf( '%1$s%2$s.%3$s/%4$s',
					$protocol,
					$azure_storage_account_name,
					$blob_storage_host_name,
					$append_container ? $default_azure_storage_account_container_name : ''
				);
			}
		}

		if ( ! isset( $url ) || empty( $url ) ) {
			return new WP_Error(
				__( 'No Azure URL', 'windows-azure-storage' ),
				__( 'A valid Azure Storage URL could not be found for this account.', 'windows-azure-storage' ),
				array(
					'name'      => $azure_storage_account_name,
					'container' => $default_azure_storage_account_container_name,
				)
			);
		}

		return trailingslashit( $url );
	}

	/**
	 * Genarate a blob name that is unique for the given container.
	 *
	 * @param string $container The default Azure storage container.
	 * @param string $blob_name The blob name.
	 *
	 * @deprecated 4.0
	 *
	 * @return string Unique blob name
	 */
	public static function uniqueBlobName( $container, $blob_name ) {
		_deprecated_function( __FUNCTION__, '4.0', 'Windows_Azure_Helper::get_unique_blob_name()' );

		return \Windows_Azure_Helper::get_unique_blob_name( $container, $blob_name );
	}

	/**
	 * Upload the given file to an Azure Storage container as a block blob.
	 *
	 * Block blobs are comprised of blocks, each of which is identified by a block ID.
	 * This allows creation or modification of a block blob by writing a set of blocks
	 * and committing them by their block IDs, resulting in an overall efficient upload.
	 *
	 * If writing a block blob that is no more than 64MB in size, upload it
	 * in its entirety with a single write operation. Otherwise, chunk the blob into discrete
	 * blocks and upload each of them, then commit the blob ID to signal to Azure that they
	 * should be combined into a blob. Files over 64MB are then deleted from temporary local storage.
	 *
	 * When you upload a block to a blob in your storage account, it is associated with the
	 * specified block blob, but it does  not become part of the blob until you commit a list
	 * of blocks that includes the new block's ID.
	 *
	 * @param string $container_name    The container to add the blob to.
	 * @param string $blob_name         The name of the blob to upload.
	 * @param string $local_file_name   The full path to local file to be uploaded.
	 * @param string $blob_content_type Optional. Content type of the blob.
	 * @param array  $metadata          Optional. Metadata to describe the blob.
	 *
	 * @deprecated 4.0
	 *
	 * @throws \Exception|ServiceException Exception if local file can't be read;
	 *                                     ServiceException if response code is incorrect.
	 */
	public static function putBlockBlob( $container_name, $blob_name, $local_file_name, $blob_content_type = null, $metadata = array() ) {
		_deprecated_function( __FUNCTION__, '4.0', 'Windows_Azure_Helper::put_uploaded_file_to_blob_storage()' );
		\Windows_Azure_Helper::put_uploaded_file_to_blob_storage( $container_name, $blob_name, $local_file_name );
	}

	/**
	 * Verify if a blob exists in the Storage container.
	 *
	 * @since      3.0.0
	 *
	 * @param string $blob_name      The blob to check.
	 * @param string $container_name Optional. The container to check. Defaults to default container in settings.
	 *
	 * @deprecated 4.0
	 *
	 * @return bool|WP_Error True if blob exists, false if not; WP_Error if container doesn't exist.
	 */
	public static function blob_exists_in_container( $blob_name, $container_name = '' ) {
		_deprecated_function( __FUNCTION__, '4.0', 'Windows_Azure_Helper::get_blob_properties()' );
		$result = \Windows_Azure_Helper::get_blob_properties( $container_name, $blob_name );

		return ! is_wp_error( $result );
	}

	/**
	 * Check if a container exists in the current account.
	 *
	 * @since      3.0.0
	 * @link       https://goo.gl/6XsKAJ Official SDK example for checking containers.
	 *
	 * @param string $container_name The container name to check.
	 *
	 * @deprecated 4.0
	 *
	 * @return bool True if the container exists in the account, false if not.
	 */
	public static function container_exists_in_storage( $container_name ) {
		_deprecated_function( __FUNCTION__, '4.0', 'Windows_Azure_Helper::get_container_acl()' );
		$result = \Windows_Azure_Helper::get_container_acl( $container_name );

		return ! is_wp_error( $result );
	}

	/**
	 * Create signature
	 *
	 * @param string  $account_name       Account name for Microsoft Azure.
	 * @param string  $account_key        Account key for Microsoft Azure.
	 * @param boolean $use_path_style_uri Use path-style URI's.
	 * @param string  $path               Path.
	 * @param string  $resource           Signed resource - container (c) - blob (b).
	 * @param string  $permissions        Signed permissions - read (r), write (w), delete (d) and list (l).
	 * @param string  $start              The time at which the Shared Access Signature becomes valid.
	 * @param string  $expiry             The time at which the Shared Access Signature becomes invalid.
	 * @param string  $identifier         Signed identifier.
	 *
	 * @deprecated 4.0
	 *
	 * @return string
	 */
	public static function createSharedAccessSignature(
		$account_name,
		$account_key,
		$use_path_style_uri,
		$path = '/',
		$resource = 'b',
		$permissions = 'r',
		$start = '',
		$expiry = '',
		$identifier = ''
	) {
		_deprecated_function( __FUNCTION__, '4.0' );
		$account_key = base64_decode( $account_key );
		// Determine path.
		if ( $use_path_style_uri ) {
			$path = substr( $path, strpos( $path, '/' ) );
		}

		// Add trailing slash to $path.
		if ( substr( $path, 0, 1 ) !== '/' ) {
			$path = '/' . $path;
		}

		// Build canonicalized resource string.
		$canonicalized_resource = '/' . $account_name;
		$canonicalized_resource .= $path;

		// Create string to sign.
		$string_to_sign   = array();
		$string_to_sign[] = $permissions;
		$string_to_sign[] = $start;
		$string_to_sign[] = $expiry;
		$string_to_sign[] = $canonicalized_resource;
		$string_to_sign[] = $identifier;

		$string_to_sign = implode( "\n", $string_to_sign );
		$signature      = base64_encode( hash_hmac( 'sha256', $string_to_sign, $account_key, true ) );

		return $signature;
	}

	/**
	 * Generate block id which can be base-64 encoded, the pre-encoded string must be 64
	 * bytes or less
	 *
	 * @param int $part Block number.
	 *
	 * @deprecated 4.0
	 *
	 * @return string Microsoft Azure Blob Storage block number
	 */
	protected static function _generateBlockId( $part = 0 ) {
		_deprecated_function( __FUNCTION__, '4.0' );
		$return_value = $part;
		while ( strlen( $return_value ) < 64 ) {
			$return_value = '0' . $return_value;
		}

		return $return_value;
	}

	/**
	 * Check if the user can take the specified action for Azure Storage.
	 *
	 * @since      3.0.0
	 * @see        user_can()
	 *
	 * @param string     $action Optional. The plugin's action to check. Default 'browse'.
	 *                           Allowed actions are: 'browse', 'insert', 'upload', 'create_container',
	 *                           'delete_single_blob', 'delete_all_blobs', and 'change_settings'.
	 * @param int|object $user   Optional. User ID or object. Default is current user ID.
	 *
	 * @deprecated 4.0
	 *
	 * @return bool Whether the action is permitted by the user.
	 */
	public static function check_action_permissions( $action = 'browse', $user = null ) {
		_deprecated_function( __FUNCTION__, '4.0' );
		if ( is_null( $user ) ) {
			$user = get_current_user_id();
		}

		/** @var array $action_map Maps our actions to user capabilities. */
		$action_map = array(
			'browse'           => 'upload_files',
			'insert'           => 'upload_files',
			'upload'           => 'upload_files',
			'create_container' => 'edit_files',
			'delete_blob'      => 'delete_others_posts',
			'delete_all_blobs' => 'edit_files',
			'change_settings'  => 'activate_plugins',
		);

		// Whitelist our actions.
		if ( ! array_key_exists( $action, $action_map ) ) {
			return false;
		}

		if ( user_can( $user, $action_map[ $action ] ) ) {
			return true;
		}

		return false;
	}
}
