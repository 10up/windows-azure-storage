<?php
/**
 * windows-azure-storage-util.php
 *
 * Various utility functions for accessing Windows Azure Storage
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

// import namepaces required for consuming Azure Blob Storage
use WindowsAzure\Blob\Models\BlobBlockType;
use WindowsAzure\Blob\Models\Block;
use WindowsAzure\Blob\Models\CommitBlobBlocksOptions;
use WindowsAzure\Blob\Models\CreateBlobOptions;
use WindowsAzure\Blob\Models\CreateContainerOptions;
use WindowsAzure\Blob\Models\ListContainersOptions;
use WindowsAzure\Blob\Models\PublicAccessType;
use WindowsAzure\Common\Internal\IServiceFilter;
use windowsazure\common\Internal\Resources;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\Common\ServicesBuilder;

/**
 * Used for performing operations on Windows Azure Blob Storage
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
	 * Get Windows Azure Storage host name defined as per plugin settings
	 *
	 * @return string host Name
	 */
	public static function getHostName() {
		$storageAccountName = WindowsAzureStorageUtil::getAccountName();
		if ( 'devstoreaccount1' === $storageAccountName ) {
			// Use development storage
			$hostName = Resources::EMULATOR_BLOB_URI;
		} else {
			// Use cloud storage
			$hostName = Resources::BLOB_BASE_DNS_NAME;
		}

		// Remove http/https from the beginning
		if ( 'http' === substr( $hostName, 0, 4 ) ) {
			$parts    = parse_url( $hostName );
			$hostName = $parts["host"];
			if ( ! empty( $parts["port"] ) ) {
				$hostName = $hostName . ":" . $parts["port"];
			}
		}

		return $hostName;
	}

	/**
	 * Get Windows Azure Storage account name defined in plugin settings
	 *
	 * @return string Account Name
	 */
	public static function getAccountName() {
		return get_option( 'azure_storage_account_name' );
	}

	/**
	 * Get Windows Azure Storage account key defined in plugin settings
	 *
	 * @return string Account Key
	 */
	public static function getAccountKey() {
		return get_option( 'azure_storage_account_primary_access_key' );
	}

	/**
	 * Get default container name defined in plugin settings
	 *
	 * @return string Default container name
	 */
	public static function getDefaultContainer() {
		return get_option( 'default_azure_storage_account_container_name' );
	}

	/**
	 * Get CNAME to be used for the base URL instead of the domain from Azure.
	 *
	 * @since 1.0.0
	 * @since 3.0.0 Return a (maybe) filtered URL.
	 *
	 * @return string CNAME to use for media URLs.
	 */
	public static function getCNAME() {
		return untrailingslashit( strtolower( self::_maybe_rewrite_cname( get_option( 'cname' ) ) ) );
	}

	/**
	 * Get HTTP proxy host if the web server needs http proxy for internet
	 *
	 * @return string HTTP proxy host name
	 */
	public static function getHttpProxyHost() {
		return get_option( 'http_proxy_host' );
	}

	/**
	 * Get HTTP proxy port if the web server needs http proxy for internet
	 *
	 * @return string HTTP proxy port number
	 */
	public static function getHttpProxyPort() {
		return get_option( 'http_proxy_port' );
	}

	/**
	 * Get HTTP proxy user-name
	 *
	 * @return string HTTP proxy user-name
	 */
	public static function getHttpProxyUserName() {
		return get_option( 'http_proxy_username' );
	}

	/**
	 * Get HTTP proxy password
	 *
	 * @return string HTTP proxy password
	 */
	public static function getHttpProxyPassword() {
		return get_option( 'http_proxy_password' );
	}

	/**
	 * Create blob storage client using Azure SDK for PHP
	 *
	 * @param string $accountName   Windows Azure Storage account name
	 *
	 * @param string $accountKey    Windows Azure Storage account primary key
	 *
	 * @param string $proxyHost     Http proxy host
	 *
	 * @param string $proxyPort     Http proxy port
	 *
	 * @param string $proxyUserName Http proxy user name
	 *
	 * @param string $proxyPassword Http proxy password
	 *
	 * @return WindowsAzure\Blob\BlobRestProxy Blob storage client
	 */
	public static function getStorageClient(
		$accountName = null, $accountKey = null,
		$proxyHost = null, $proxyPort = null,
		$proxyUserName = null, $proxyPassword = null
	) {
		// Storage Account Settings from db
		$storageAccountName = WindowsAzureStorageUtil::getAccountName();
		$storageAccountKey  = WindowsAzureStorageUtil::getAccountKey();
		$httpProxyHost      = WindowsAzureStorageUtil::getHttpProxyHost();
		$httpProxyPort      = WindowsAzureStorageUtil::getHttpProxyPort();
		$httpProxyUserName  = WindowsAzureStorageUtil::getHttpProxyUserName();
		$httpProxyPassword  = WindowsAzureStorageUtil::getHttpProxyPassword();
		// Parameters take precedence over settings in the db
		if ( $accountName ) {
			$storageAccountName = $accountName;
			$storageAccountKey  = $accountKey;
			$httpProxyHost      = $proxyHost;
			$httpProxyPort      = $proxyPort;
			$httpProxyUserName  = $proxyUserName;
			$httpProxyPassword  = $proxyPassword;
		}

		$azureServiceConnectionString = null;
		if ( 'devstoreaccount1' === $storageAccountName ) {
			// Use development storage
			$azureServiceConnectionString = "UseDevelopmentStorage=true";
		} else {
			// Use cloud storage
			$azureServiceConnectionString = "DefaultEndpointsProtocol=http"
			                                . ";AccountName=" . $storageAccountName
			                                . ";AccountKey=" . $storageAccountKey;
		}

		$blobRestProxy = ServicesBuilder::getInstance()->createBlobService( $azureServiceConnectionString );
		$httpProxyHost = $httpProxyHost;

		if ( ! empty( $httpProxyHost ) ) {
			$proxyFilter = new WindowsAzureStorageProxyFilter( $httpProxyHost,
				$httpProxyPort,
				$httpProxyUserName,
				$httpProxyPassword
			);

			$blobRestProxy = $blobRestProxy->withFilter( $proxyFilter );
		}

		return $blobRestProxy;
	}

	/**
	 * Delete a blob from specified container
	 *
	 * @param string $containerName Name of the parent container
	 *
	 * @param string $blobName      Name of the blob to be deleted
	 *
	 * @return void
	 */
	public static function deleteBlob( $containerName, $blobName ) {
		$blobRestProxy = WindowsAzureStorageUtil::getStorageClient();
		if ( self::blobExists( $containerName, $blobName ) ) {
			$blobRestProxy->deleteBlob( $containerName, $blobName );
		}
	}

	/**
	 * Check if a blob exists
	 *
	 * @since Unknown
	 * @since 3.0.0 Wrapper for blob_exists_in_container().
	 * @see   WindowsAzureStorageUtil::blob_exists_in_container()
	 *
	 * @param string $containerName Name of the parent container
	 * @param string $blobName      Name of the blob to be checked
	 * @return boolean
	 */
	public static function blobExists( $containerName, $blobName ) {
		_deprecated_function( __FUNCTION__, '3.0.0', 'WindowsAzureStorageUtil::blob_exists_in_container()' );

		return self::blob_exists_in_container( $blobName, $containerName );
	}

	/**
	 * Creates a public container
	 *
	 * @param string        $containerName Name of the container to create
	 *
	 * @param BlobRestProxy $storageClient Reference of storage client to use
	 *
	 * @throws ServiceException
	 */
	public static function createPublicContainer( $containerName, $storageClient = null ) {
		$containerOptions = new CreateContainerOptions();
		$containerOptions->setPublicAccess( PublicAccessType::CONTAINER_AND_BLOBS );
		$blobRestProxy = $null;
		try {
			if ( $storageClient ) {
				$blobRestProxy = $storageClient;
			} else {
				$blobRestProxy = WindowsAzureStorageUtil::getStorageClient();
			}
			$blobRestProxy->createContainer( $containerName, $containerOptions );
		} catch ( ServiceException $e ) {
			throw $e;
		}
	}

	/**
	 * Modifies the CNAME protocol if needed.
	 *
	 * If the CNAME is configured different than what Azure supports or the current site's protocol,
	 * this will modify it to match, based on the filter's value.
	 *
	 * @since 3.0.0
	 *
	 * @param string $cname The CNAME value set in the plugin options.
	 * @return string The (maybe) new CNAME with the filtered protocol.
	 */
	protected static function _maybe_rewrite_cname( $cname ) {
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
	 * @return string|WP_Error The base blob URL for an account, or an error if one can't be found/created.
	 */
	public static function get_storage_url_base( $append_container = true ) {
		$azure_storage_account_name                   = self::getAccountName();
		$default_azure_storage_account_container_name = self::getDefaultContainer();

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

		// Get CNAME if defined
		$cname = self::getCNAME();
		if ( ! ( empty( $cname ) ) ) {
			$url = sprintf( '%1$s/%2$s',
				$cname,
				$append_container ? $default_azure_storage_account_container_name : ''
			);
		} else {
			$blob_storage_host_name = self::getHostName();
			$storage_account_name   = self::getAccountName();

			if ( Resources::DEV_STORE_NAME === $storage_account_name ) {
				// Use development storage
				$url = sprintf( '%1$s%2%s/%3$s/%4$s',
					$protocol,
					$blob_storage_host_name,
					$azure_storage_account_name,
					$append_container ? $default_azure_storage_account_container_name : ''
				);
			} else {
				// Use cloud storage
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
	 * @param string $container The default Azure storage container
	 * @param string $blobName  The blob name
	 *
	 * @return string Unique blob name
	 */
	public static function uniqueBlobName( $container, $blobName ) {
		$info = pathinfo( $blobName );

		$uploadSubDir = ( '.' === $info['dirname'] ) ? '' : $info['dirname'];
		$filename     = sanitize_file_name( $info['basename'] );

		// sanitized blob name
		$blobName = ( '' === $uploadSubDir ) ? $filename : $uploadSubDir . '/' . $filename;

		$newInfo = pathinfo( $blobName );
		$ext     = ! empty( $newInfo['extension'] ) ? '.' . $newInfo['extension'] : '';

		$number = '';

		// change '.ext' to lower case
		if ( $ext && strtolower( $ext ) != $ext ) {
			$ext2      = strtolower( $ext );
			$filename2 = preg_replace( '|' . preg_quote( $ext ) . '$|', $ext2, $filename );
			$blobName2 = ( '' === $uploadSubDir ) ? $filename2 : $uploadSubDir . '/' . $filename2;

			// check for both lower and upper case extension or image sub-sizes may be overwritten
			while ( WindowsAzureStorageUtil::blobExists( $container, $blobName )
			        || WindowsAzureStorageUtil::blobExists( $container, $blobName2 ) ) {
				$new_number = $number + 1;
				$filename   = str_replace( "$number$ext", "$new_number$ext", $filename );
				$filename2  = str_replace( "$number$ext2", "$new_number$ext2", $filename2 );
				$number     = $new_number;
				$blobName   = ( '' === $uploadSubDir ) ? $filename : $uploadSubDir . '/' . $filename;
				$blobName2  = ( '' === $uploadSubDir ) ? $filename2 : $uploadSubDir . '/' . $filename2;
			}

			return $blobName2;
		}

		while ( WindowsAzureStorageUtil::blobExists( $container, $blobName ) ) {
			if ( '' === "$number$ext" ) {
				$filename = $filename . ++ $number . $ext;
			} else {
				$filename = str_replace( "$number$ext", ++ $number . $ext, $filename );
			}

			$blobName = ( '' === $uploadSubDir ) ? $filename : $uploadSubDir . '/' . $filename;
		}

		return $blobName;
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
	 * @param string $containerName   The container to add the blob to.
	 * @param string $blobName        The name of the blob to upload.
	 * @param string $localFileName   The full path to local file to be uploaded.
	 * @param string $blobContentType Optional. Content type of the blob.
	 * @param array  $metadata        Optional. Metadata to describe the blob.
	 *
	 * @throws \Exception|ServiceException Exception if local file can't be read;
	 *                                     ServiceException if response code is incorrect.
	 */
	public static function putBlockBlob( $containerName, $blobName, $localFileName, $blobContentType = null, $metadata = array() ) {
		$copyBlobResult = null;
		$is_large_file  = false;
		// Open file
		$handle = fopen( $localFileName, 'r' );
		if ( $handle === false ) {
			throw new Exception( 'Could not open the local file ' . $localFileName );
		}

		/** @var \WindowsAzure\Blob\BlobRestProxy $blobRestProxy */
		$blobRestProxy = WindowsAzureStorageUtil::getStorageClient();
		try {
			if ( filesize( $localFileName ) < self::MAX_BLOB_SIZE ) {
				$createBlobOptions = new CreateBlobOptions();
				$createBlobOptions->setBlobContentType( $blobContentType );
				$createBlobOptions->setMetadata( $metadata );
				$blobRestProxy->createBlockBlob( $containerName, $blobName, $handle, $createBlobOptions );
				fclose( $handle );
			} else {
				$is_large_file = true;
				// Determine number of page blocks
				$numberOfBlocks = ceil( filesize( $localFileName ) / self::MAX_BLOB_TRANSFER_SIZE );

				// Generate block id's
				$blocks = array();

				for ( $i = 0; $i < $numberOfBlocks; $i ++ ) {
					/** @var WindowsAzure\Blob\Models\Block */
					$block = new Block();

					$block->setBlockId( self::_generateBlockId( $i ) );
					$block->setType( BlobBlockType::LATEST_TYPE );

					// Seek position in file
					fseek( $handle, $i * self::MAX_BLOB_TRANSFER_SIZE );
					// Read contents
					$fileContents = fread( $handle, self::MAX_BLOB_TRANSFER_SIZE );
					// Put block
					$blobRestProxy->createBlobBlock( $containerName, $blobName, $block->getBlockId(), $fileContents );

					// Save it for later
					$blocks[ $i ] = $block;
				}

				// Close file
				fclose( $handle );
				// Set Block Blob's content type and metadata
				$commitBlockBlobOptions = new CommitBlobBlocksOptions();
				$commitBlockBlobOptions->setBlobContentType( $blobContentType );
				$commitBlockBlobOptions->setMetadata( $metadata );
				// Commit the block list
				$blobRestProxy->commitBlobBlocks( $containerName, $blobName, $blocks, $commitBlockBlobOptions );

				if ( $is_large_file ) {
					// Delete large temp files when we're done
					try {
						//TODO: add option to keep this file if so desired
						if ( self::blob_exists_in_container( $blobName, $containerName ) ) {
							wp_delete_file( $localFileName );
							// Dispose file contents
							$fileContents = null;
							unset( $fileContents );
						} else {
							throw new Exception(
								sprintf(
									__( 'The blob %1$2 was not uploaded to container %2$2. Please try again.', 'windows-azure-storage' ),
									$blobName,
									$containerName
								)
							);
						}
					} catch ( Exception $ex ) {
						echo '<p class="notice">' . esc_html( $ex->getMessage() ) . '</p>';
					}
				}
			}
		} catch ( ServiceException $exception ) {
			if ( ! $handle ) {
				fclose( $handle );
			}
			throw $exception;
		}
	}

	/**
	 * Verify if a blob exists in the Storage container.
	 *
	 * @since 3.0.0
	 *
	 * @param string $blob_name      The blob to check.
	 * @param string $container_name Optional. The container to check. Defaults to default container in settings.
	 * @return bool|WP_Error True if blob exists, false if not; WP_Error if container doesn't exist.
	 */
	public static function blob_exists_in_container( $blob_name, $container_name = '' ) {
		/** @var WindowsAzure\Blob\BlobRestProxy $client */
		$client = self::getStorageClient();

		if ( empty( $container_name ) ) {
			$container_name = self::getDefaultContainer();
		}

		if ( ! self::container_exists_in_storage( $container_name ) ) {
			return new WP_Error( __( 'invalid_container', 'windows-azure-storage' ),
				__( 'The container specified does not exist in this account.', 'windows-azure-storage' ),
				array(
					'container' => $container_name,
					'blob'      => $blob_name,
				)
			);
		}
		$result = false;
		try {
			$blob_properties = $client->getBlobProperties( $container_name, $blob_name );
			if ( $blob_properties instanceof \WindowsAzure\Blob\Models\GetBlobPropertiesResult ) {
				$result = true;
			}
		} catch ( \Exception $exception ) {
			error_log( $exception->getMessage(), E_USER_NOTICE );
		}

		return $result;
	}

	/**
	 * Check if a container exists in the current account.
	 *
	 * @since 3.0.0
	 * @link  https://goo.gl/6XsKAJ Official SDK example for checking containers.
	 *
	 * @param string $container_name The container name to check.
	 * @return bool True if the container exists in the account, false if not.
	 */
	public static function container_exists_in_storage( $container_name ) {
		/** @var WindowsAzure\Blob\BlobRestProxy $client */
		$client           = self::getStorageClient();
		$container_exists = false;

		$options = new ListContainersOptions();
		$options->setPrefix( $container_name );

		//TODO: check cache for containers list and use it if present.
		$result     = $client->listContainers( $options );
		$containers = $result->getContainers();

		//TODO: Cache the containers list.

		/** @var WindowsAzure\Blob\Models\Container $container */
		foreach ( $containers as $container ) {
			if ( $container->getName() === $container_name ) {
				$container_exists = true;
				break;
			}
		}

		return $container_exists;
	}

	/**
	 * Create signature
	 *
	 * @param string  $accountName     Account name for Windows Azure
	 *
	 * @param string  $accountKey      Account key for Windows Azure
	 *
	 * @param boolean $usePathStyleUri Use path-style URI's
	 *
	 * @param string  $path            Path for the
	 *
	 * @param string  $resource        Signed resource - container (c) - blob (b)
	 *
	 * @param string  $permissions     Signed permissions - read (r), write (w), delete (d) and list (l)
	 *
	 * @param string  $start           The time at which the Shared Access Signature becomes valid.
	 *
	 * @param string  $expiry          The time at which the Shared Access Signature becomes invalid.
	 *
	 * @param string  $identifier      Signed identifier
	 *
	 * @return string
	 */
	public static function createSharedAccessSignature(
		$accountName,
		$accountKey,
		$usePathStyleUri,
		$path = '/',
		$resource = 'b',
		$permissions = 'r',
		$start = '',
		$expiry = '',
		$identifier = ''
	) {
		$accountKey = base64_decode( $accountKey );
		// Determine path
		if ( $usePathStyleUri ) {
			$path = substr( $path, strpos( $path, '/' ) );
		}

		// Add trailing slash to $path
		if ( substr( $path, 0, 1 ) !== '/' ) {
			$path = '/' . $path;
		}

		// Build canonicalized resource string
		$canonicalizedResource = '/' . $accountName;
		$canonicalizedResource .= $path;

		// Create string to sign
		$stringToSign   = array();
		$stringToSign[] = $permissions;
		$stringToSign[] = $start;
		$stringToSign[] = $expiry;
		$stringToSign[] = $canonicalizedResource;
		$stringToSign[] = $identifier;

		$stringToSign = implode( "\n", $stringToSign );
		$signature    = base64_encode( hash_hmac( 'sha256', $stringToSign, $accountKey, true ) );

		return $signature;
	}

	/**
	 * Generate block id which can be base-64 encoded, the pre-encoded string must be 64
	 * bytes or less
	 *
	 * @param int $part Block number
	 *
	 * @return string Windows Azure Blob Storage block number
	 */
	protected static function _generateBlockId( $part = 0 ) {
		$returnValue = $part;
		while ( strlen( $returnValue ) < 64 ) {
			$returnValue = '0' . $returnValue;
		}

		return $returnValue;
	}

	/**
	 * Check if the user can take the specified action for Azure Storage.
	 *
	 * @since 3.0.0
	 * @see   user_can()
	 *
	 * @param string     $action Optional. The plugin's action to check. Default 'browse'.
	 *                           Allowed actions are: 'browse', 'insert', 'upload', 'create_container',
	 *                           'delete_single_blob', 'delete_all_blobs', and 'change_settings'.
	 * @param int|object $user   Optional. User ID or object. Default is current user ID.
	 * @return bool Whether the action is permitted by the user.
	 */
	public static function check_action_permissions( $action = 'browse', $user = null ) {
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

/**
 * Internal class used for redirecting request-response for Http proxy
 *
 * @category  WordPress_Plugin
 * @package   Windows_Azure_Storage_For_WordPress
 * @author    Microsoft Open Technologies, Inc. <msopentech@microsoft.com>
 * @copyright Microsoft Open Technologies, Inc.
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @link      http://www.microsoft.com
 */
class WindowsAzureStorageProxyFilter implements IServiceFilter {
	/**
	 * Proxy host.
	 */
	protected $host;

	/**
	 * Proxy port.
	 */
	protected $port;

	/**
	 * Proxy username.
	 */
	protected $username;

	/**
	 * Proxy password.
	 */
	protected $password;

	/**
	 * Create a new instance of WindowsAzureStorageProxyFilter
	 *
	 * @param string $host     HTTP porxy host.
	 *
	 * @param string $port     HTTP proxy port.
	 *
	 * @param string $username HTTP proxy username.
	 *
	 * @param string $password HTTP proxy password.
	 */
	public function __construct( $host, $port, $username, $password ) {
		$this->host     = $host;
		$this->port     = $port;
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * Hook to processes HTTP request before send.
	 *
	 * @param mix $request HTTP request object.
	 *
	 * @return mix processed HTTP request object.
	 */
	public function handleRequest( $request ) {
		if ( $this->host ) {
			$request->setConfig( 'proxy_host', $this->host );
			if ( $this->port ) {
				$request->setConfig( 'proxy_port', $this->port );
				if ( $this->username ) {
					$request->setConfig( 'proxy_user', $this->username );
					if ( $this->password ) {
						$request->setConfig( 'proxy_password', $this->password );
					}
				}
			}
		}

		return $request;
	}

	/**
	 * Hook to processes HTTP response after send.
	 *
	 * @param mix $request  HTTP request object.
	 * @param mix $response HTTP response object.
	 *
	 * @return mix processed HTTP response object.
	 */
	public function handleResponse( $request, $response ) {
		return $response;
	}

}
