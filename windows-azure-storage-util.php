<?php
/**
 * windows-azure-storage-util.php
 * 
 * Various utility functions for accessing Windows Azure Storage
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
use WindowsAzure\Common\Internal\IServiceFilter;

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
class WindowsAzureStorageUtil
{
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
    public static function getHostName()
    {
        $storageAccountName = WindowsAzureStorageUtil::getAccountName();
        if ($storageAccountName == 'devstoreaccount1') {
            // Use development storage
            $hostName = Resources::EMULATOR_BLOB_URI;
        } else {
            // Use cloud storage
            $hostName = Resources::BLOB_BASE_DNS_NAME;
        }

        // Remove http/https from the beginning
        if (substr($hostName, 0, 4) == "http") {
            $parts = parse_url($hostName);
            $hostName = $parts["host"];
            if (!empty($parts["port"])) {
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
    public static function getAccountName()
    {
        return get_option('azure_storage_account_name');
    }
    
    /**
     * Get Windows Azure Storage account key defined in plugin settings
     * 
     * @return string Account Key
     */
    public static function getAccountKey()
    {
        return get_option('azure_storage_account_primary_access_key');
    }
    
    /**
     * Get default container name defined in plugin settings
     * 
     * @return string Default container name
     */
    public static function getDefaultContainer()
    {
        return get_option('default_azure_storage_account_container_name');
    }
    
    /**
     * Get CNAME to be used for blob URL instead of blob.windows.net
     * 
     * @return string CNAME
     */
    public static function getCNAME()
    {
        return get_option('cname');
    }
    
    /**
     * Get HTTP proxy host if the web server needs http proxy for internet
     * 
     * @return string HTTP proxy host name
     */
    public static function getHttpProxyHost()
    {
        return get_option('http_proxy_host');
    }
    
    /**
     * Get HTTP proxy port if the web server needs http proxy for internet
     * 
     * @return string HTTP proxy port number
     */
    public static function getHttpProxyPort()
    {
        return get_option('http_proxy_port');
    }
    
    /**
     * Get HTTP proxy user-name
     * 
     * @return string HTTP proxy user-name
     */
    public static function getHttpProxyUserName()
    {
        return get_option('http_proxy_username');
    }
    
    /**
     * Get HTTP proxy password
     *
     * @return string HTTP proxy password
     */
    public static function getHttpProxyPassword()
    {
        return get_option('http_proxy_password');
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
     * @return BlobRestProxy Blob storage client
     */
    public static function getStorageClient(
        $accountName = null, $accountKey = null, 
        $proxyHost = null, $proxyPort = null, 
        $proxyUserName = null, $proxyPassword = null
    ) {
        // Storage Account Settings from db
        $storageAccountName = WindowsAzureStorageUtil::getAccountName();
        $storageAccountKey = WindowsAzureStorageUtil::getAccountKey();
        $httpProxyHost = WindowsAzureStorageUtil::getHttpProxyHost();
        $httpProxyPort = WindowsAzureStorageUtil::getHttpProxyPort();
        $httpProxyUserName = WindowsAzureStorageUtil::getHttpProxyUserName();
        $httpProxyPassword = WindowsAzureStorageUtil::getHttpProxyPassword();
        // Parameters take precedence over settings in the db
        if ($accountName) {
            $storageAccountName = $accountName;
            $storageAccountKey = $accountKey;
            $httpProxyHost = $proxyHost;
            $httpProxyPort = $proxyPort;
            $httpProxyUserName = $proxyUserName;
            $httpProxyPassword = $proxyPassword;
        }

        $azureServiceConnectionString = null;
        if ($storageAccountName == 'devstoreaccount1') {
            // Use development storage
            $azureServiceConnectionString = "UseDevelopmentStorage=true";
        } else {
            // Use cloud storage         
            $azureServiceConnectionString = "DefaultEndpointsProtocol=http"
                . ";AccountName=" . $storageAccountName
                . ";AccountKey=" . $storageAccountKey;
        }
    
        $blobRestProxy = ServicesBuilder::getInstance()->createBlobService($azureServiceConnectionString);
        $httpProxyHost = $httpProxyHost;
        
        if (!empty($httpProxyHost)) {
          $proxyFilter = new WindowsAzureStorageProxyFilter($httpProxyHost,
              $httpProxyPort,
              $httpProxyUserName,
              $httpProxyPassword
          );
           
          $blobRestProxy = $blobRestProxy->withFilter($proxyFilter);
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
    public static function deleteBlob($containerName, $blobName)
    {
        $blobRestProxy = WindowsAzureStorageUtil::getStorageClient();
        if (self::blobExists($containerName, $blobName)) {
            $blobRestProxy->deleteBlob($containerName, $blobName);
        }
    }
    
    /**
     * Check if a blob exists
     *
     * @param string $containerName Name of the parent container
     *
     * @param string $blobName      Name of the blob to be checked
     *
     * @return boolean
     */
    public static function blobExists($containerName, $blobName)
    {
      try {
        $blobRestProxy = WindowsAzureStorageUtil::getStorageClient();
        $blobRestProxy->getBlobMetadata($containerName, $blobName);
      } catch(ServiceException $e){
            return false;
      }

      return true;
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
    public static function createPublicContainer($containerName, $storageClient = null)
    {
        $containerOptions = new CreateContainerOptions();
        $containerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
        $blobRestProxy = $null;
        try
        {
            if ($storageClient) {
                $blobRestProxy = $storageClient;
            } else {
                $blobRestProxy = WindowsAzureStorageUtil::getStorageClient();
            }
            $blobRestProxy->createContainer($containerName, $containerOptions);
        } catch(ServiceException $e){
            throw $e;
        }
    }

    /**
     * Get prefix for the blob URL
     * 
     * @param boolean $appendContainer Wheather to append container name at the end
     *  
     * @return string Prefix for the blob URL
     */
    public static function getStorageUrlPrefix($appendContainer = true)
    {
        $azure_storage_account_name = WindowsAzureStorageUtil::getAccountName();
        $default_azure_storage_account_container_name 
            = WindowsAzureStorageUtil::getDefaultContainer();
            
        // Get CNAME if defined
        $cname = WindowsAzureStorageUtil::getCNAME();
        if (!(empty($cname))) {
            if ($appendContainer) {
                return $cname . "/" . $default_azure_storage_account_container_name;
            } else {
                return $cname;
            }
        } else {
            $blobStorageHostName = WindowsAzureStorageUtil::getHostName();
            $storageAccountName = WindowsAzureStorageUtil::getAccountName();

            if ($storageAccountName == 'devstoreaccount1') {
                // Use development storage
                if ($appendContainer) {
                    return 'http://' . $blobStorageHostName 
                        . '/'. $azure_storage_account_name 
                        . '/' . $default_azure_storage_account_container_name;
                } else {
                    return 'http://' . $blobStorageHostName 
                        . '/'. $azure_storage_account_name;
                }
            } else {
                // Use cloud storage
                if ($appendContainer) {
                    return 'http://' . $azure_storage_account_name 
                        . '.' . $blobStorageHostName 
                        . '/' . $default_azure_storage_account_container_name;
                } else {
                    return 'http://' . $azure_storage_account_name 
                        . '.' . $blobStorageHostName;
                }
            }
        }
    }
    
    /**
     * Genarate a blob name that is unique for the given container.
     *
     * @param string $container The default Azure storage container
     * @param string $blobName The blob name
     *
     * @return string Unique blob name
     */
    public static function uniqueBlobName($container, $blobName)
    {
    	$info = pathinfo($blobName);
    	 
    	$uploadSubDir = ($info['dirname'] == '.')  ? '' : $info['dirname'];
    	$filename = sanitize_file_name($info['basename']);
    	 
    	// sanitized blob name
    	$blobName = ($uploadSubDir == '') ? $filename : $uploadSubDir. '/' .$filename;
    	 
    	$newInfo = pathinfo($blobName);
    	$ext = !empty($newInfo['extension']) ? '.' . $newInfo['extension'] : '';
    	 
    	$number = '';
    	 
    	// change '.ext' to lower case
    	if ( $ext && strtolower($ext) != $ext ) {
    		$ext2 = strtolower($ext);
    		$filename2 = preg_replace( '|' . preg_quote($ext) . '$|', $ext2, $filename );
    		$blobName2 = ($uploadSubDir == '') ? $filename2 : $uploadSubDir. '/' .$filename2;
    		 
    		// check for both lower and upper case extension or image sub-sizes may be overwritten
    		while ( WindowsAzureStorageUtil::blobExists($container, $blobName)
    				|| WindowsAzureStorageUtil::blobExists($container, $blobName2) ) {
    			$new_number = $number + 1;
    			$filename = str_replace( "$number$ext", "$new_number$ext", $filename );
    			$filename2 = str_replace( "$number$ext2", "$new_number$ext2", $filename2 );
    			$number = $new_number;
    			$blobName = ($uploadSubDir == '') ? $filename : $uploadSubDir. '/' .$filename;
    			$blobName2 = ($uploadSubDir == '') ? $filename2 : $uploadSubDir. '/' .$filename2;
    		}
    		 
    		return $blobName2;
    	}
    	 
    	while ( WindowsAzureStorageUtil::blobExists($container, $blobName) ) {
    		if ( '' == "$number$ext" ) {
    			$filename = $filename . ++$number . $ext;
    		}
    		else {
    			$filename = str_replace( "$number$ext", ++$number . $ext, $filename );
    		}
    		 
    		$blobName = ($uploadSubDir == '') ? $filename : $uploadSubDir. '/' .$filename;
    	}
    	 
    	return $blobName;
    }
    
    /**
     * Upload the given file to an azure storage container as a block blob.
     * Block blobs let us upload large blobs efficiently. Block blobs are comprised of blocks,
     * each of which is identified by a block ID. This allows create (or modify) a block blob
     * by writing a set of blocks and committing them by their block IDs.
     * If we are writing a block blob that is no more than 64 MB in size, you can upload it
     * in its entirety with a single write operation.
     * When you upload a block to a blob in your storage account, it is associated with the
     * specified block blob, but it does  not become part of the blob until you commit a list
     * of blocks that includes the new block's ID.
     *
     * @param string            $containerName      Container name
     *
     * @param string            $blobName           Blob name
     *
     * @param string            $localFileName      Path to local file to be uploaded
     *
     * @param string            $blobContentType    Content type of the blob
     *
     * @param array             $metadata           Array of metadata
     *
     * @return void
     *
     * @throws ServiceException
     */
    public static function putBlockBlob($containerName, $blobName, $localFileName, $blobContentType = null, $metadata = array())
    {
      $copyBlobResult = null;
      // Open file
      $handle = fopen($localFileName, 'r');
      if ($handle === false) {
        throw new Exception('Could not open the local file ' . localFileName);
      }
    
      $blobRestProxy = WindowsAzureStorageUtil::getStorageClient();
      try {
        if (filesize($localFileName) < self::MAX_BLOB_SIZE) {
          $createBlobOptions = new CreateBlobOptions();
          $createBlobOptions->setBlobContentType($blobContentType);
          $createBlobOptions->setMetadata($metadata);
          $blobRestProxy->createBlockBlob($containerName, $blobName, $handle, $createBlobOptions);
          fclose($handle);
        } else {
          // Determine number of page blocks
          $numberOfBlocks = ceil( filesize($localFileName) / self::MAX_BLOB_TRANSFER_SIZE );
    
          // Generate block id's
          $blocks = array();
          for ($i = 0; $i < $numberOfBlocks; $i++) {
            $blocks[$i] = new Block();
            $blocks[$i]->setBlockId(self::_generateBlockId($i));
            $blocks[$i]->setType(BlobBlockType::LATEST_TYPE);
          }
    
          // Upload blocks
          for ($i = 0; $i < $numberOfBlocks; $i++) {
            // Seek position in file
            fseek($handle, $i * self::MAX_BLOB_TRANSFER_SIZE);
            // Read contents
            $fileContents = fread($handle, self::MAX_BLOB_TRANSFER_SIZE);
            // Put block
            $blobRestProxy->createBlobBlock($containerName, $blobName, $blocks[$i]->getBlockId(), $fileContents);
            // Dispose file contents
            $fileContents = null;
            unset($fileContents);
          }
    
          // Close file
          fclose($handle);
          // Set Block Blob's content type and metadata
          $commitBlockBlobOptions = new CommitBlobBlocksOptions();
          $commitBlockBlobOptions->setBlobContentType($blobContentType);
          $commitBlockBlobOptions->setMetadata($metadata);
          // Commit the block list
          $blobRestProxy->commitBlobBlocks($containerName, $blobName, $blocks, $commitBlockBlobOptions);
        }
      } catch (ServiceException $exception) {
        if (!$handle) {
          fclose($handle);
        }
        throw $exception;
      }
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
      $accountKey = base64_decode($accountKey);
      // Determine path
      if ($usePathStyleUri) {
        $path = substr($path, strpos($path, '/'));
      }
        
      // Add trailing slash to $path
      if (substr($path, 0, 1) !== '/') {
        $path = '/' . $path;
      }
    
      // Build canonicalized resource string
      $canonicalizedResource  = '/' . $accountName;
      $canonicalizedResource .= $path;
    
      // Create string to sign
      $stringToSign   = array();
      $stringToSign[] = $permissions;
      $stringToSign[] = $start;
      $stringToSign[] = $expiry;
      $stringToSign[] = $canonicalizedResource;
      $stringToSign[] = $identifier;
    
      $stringToSign = implode("\n", $stringToSign);
      $signature    = base64_encode(hash_hmac('sha256', $stringToSign, $accountKey, true));
    
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
    protected static function _generateBlockId($part = 0)
    {
      $returnValue = $part;
      while (strlen($returnValue) < 64) {
        $returnValue = '0' . $returnValue;
      }
    
      return $returnValue;
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
class WindowsAzureStorageProxyFilter implements IServiceFilter
{
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
  public function __construct($host, $port, $username, $password) {
        $this->host = $host;
        $this->port = $port;
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
  public function handleRequest($request) {
    if ($this->host) {
        $request->setConfig('proxy_host', $this->host);
        if ($this->port) {
            $request->setConfig('proxy_port', $this->port);
            if ($this->username) {
                $request->setConfig('proxy_user', $this->username);
                if ($this->password) {
                    $request->setConfig('proxy_password', $this->password);
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
  public function handleResponse($request, $response) {
      return $response;
  }
}
?>
