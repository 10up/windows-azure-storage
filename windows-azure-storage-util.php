<?php
/**
 * windows-azure-storage-util.php
 * 
 * Various utility functions for accessing Windows Azure Storage
 * 
 * Version: 1.3
 * 
 * Author: Microsoft
 * 
 * Author URI: http://www.microsoft.com/
 * 
 * License: New BSD License (BSD)
 * 
 * Copyright (c) 2011, Microsoft Corporation. All Rights Reserved.
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this 
 * list of conditions and the following disclaimer.
 * 
 * Redistributions in binary form must reproduce the above copyright notice, this 
 * list of conditions and the following disclaimer in the documentation and/or 
 * other materials provided with the distribution.
 * 
 * Neither the name of Persistent Systems Ltd. nor the names of its contributors 
 * may be used to endorse or promote products derived from this software without 
 * specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE 
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR 
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS 
 * OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY 
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING 
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, 
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE. 
 * 
 * PHP Version 5
 * 
 * @category  WordPress_Plugin
 * @package   Windows_Azure_Storage_For_WordPress
 * @author    Satish Nikam <v-sanika@microsoft.com>
 * @copyright 2011 Copyright © Microsoft Corporation. All Rights Reserved
 * @license   New BSD License (BSD)
 * @link      http://www.microsoft.com
 */

require_once 'Microsoft/WindowsAzure/Storage/Blob.php';

/**
 * Used for performing operations on Windows Azure Blob Storage
 *
 * @category  WordPress_Plugin
 * @package   Windows_Azure_Storage_For_WordPress
 * @author    Satish Nikam <v-sanika@microsoft.com>
 * @copyright 2011 Copyright © Microsoft Corporation. All Rights Reserved
 * @license   New BSD License (BSD)
 * @link      http://www.microsoft.com
 */
class WindowsAzureStorageUtil
{
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
            return Microsoft_WindowsAzure_Storage::URL_DEV_BLOB;
        } else {
            // Use cloud storage
            return Microsoft_WindowsAzure_Storage::URL_CLOUD_BLOB;
        }
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
     * Get HTTP proxy credentials
     * 
     * @return string HTTP proxy credentials
     */
    public static function getHttpProxyCredentials()
    {
        return get_option('http_proxy_credentials');
    }
    
    /**
     * Create blob storage client using Azure SDK for PHP
     * 
     * @return Microsoft_WindowsAzure_Storage_Blob Blob storage client
     */
    public static function getStorageClient()
    {
        // Storage Account Settings
        $storageAccountName = WindowsAzureStorageUtil::getAccountName();
        if ($storageAccountName == 'devstoreaccount1') {
            // Use development storage
            $storageClient = new Microsoft_WindowsAzure_Storage_Blob();
        } else {
            // Use cloud storage
            $storageClient = new Microsoft_WindowsAzure_Storage_Blob(
                Microsoft_WindowsAzure_Storage::URL_CLOUD_BLOB, 
                WindowsAzureStorageUtil::getAccountName(), 
                WindowsAzureStorageUtil::getAccountKey()
            );
            
            // Set optional HTTP proxy
            $httpProxyHost = WindowsAzureStorageUtil::getHttpProxyHost();
            
            if (!empty($httpProxyHost)) {
                $storageClient->setProxy(
                    true, $httpProxyHost, 
                    WindowsAzureStorageUtil::getHttpProxyPort(), 
                    WindowsAzureStorageUtil::getHttpProxyCredentials()
                );
            }
        }
        return $storageClient;
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
        $storageClient = WindowsAzureStorageUtil::getStorageClient();
        if ($storageClient->blobExists($containerName, $blobName)) {
            $storageClient->deleteBlob($containerName, $blobName);
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
}
?>
