<?php
/**
 * windows-azure-storage-settings.php
 * 
 * Shows various settings for Windows Azure Storage Plugin
 * 
 * Version: 1.0
 * 
 * Author: Microsoft
 * 
 * Author URI: http://www.microsoft.com/
 * 
 * License: New BSD License (BSD)
 * 
 * Copyright (c) 2010, Microsoft Corporation. All Rights Reserved.
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
 * @copyright 2010 Copyright © Microsoft Corporation. All Rights Reserved
 * @license   New BSD License (BSD)
 * @link      http://www.microsoft.com
 */

/**
 * Wordpress hook for displaying plugin options page 
 * 
 * @return void
 */
function windows_azure_storage_plugin_options_page()
{
?>
    <div class="wrap">
      <h2>
          <img src="../wp-content/plugins/windows-azure-storage/images/WindowsAzure.jpg" 
          width="32" height="32"/>Windows Azure Storage for WordPress</h2>

          This WordPress plugin allows you to use Windows Azure Storage Service to 
          host your media for your WordPress powered blog. Windows Azure provides 
          storage in the cloud with authenticated access and triple replication to 
          help keep your data safe. Applications work with data using REST conventions 
          and standard HTTP operations to identify and expose data using URIs. This 
          plugin allows you to easily upload, retrieve, and link to files stored on 
          Windows Azure Storage service from within WordPress. <br/><br/>

          For more details on Windows Azure Storage Services, please visit the 
          <a href="http://www.microsoft.com/azure/windowsazure.mspx">Windows Azure 
          Platform web-site</a>.<br/>

          <p>This plugin is developed in PHP and uses PHP SDK for Azure (<a 
          href="http://phpazure.codeplex.com/">http://phpazure.codeplex.com/</a>). </p>
          <b>Plugin Web Site:</b> 
          <a href="http://wordpress.org/extend/plugins/windows-azure-storage/">
          http://wordpress.org/extend/plugins/windows-azure-storage/</a><br/><br/>
    </div>

    <div>
      <table>
         <tr>
             <td>
                 <div id="icon-options-general" class="icon32"><br/></div>
                 <h2>Windows Azure Storage Settings</h2>

                 <p>If you do not have Windows Azure Storage Account, please 
                 <a href="http://go.microsoft.com/fwlink/?LinkID=129453">register
                 </a>for Windows Azure Services.</p>
                    <form method="post" action="options.php">
    <?php
    wp_nonce_field('update-options');
    show_windows_azure_storage_settings('admin');
    ?>
                        <input type="hidden" name="action" value="update" />
                        <input type="hidden" name="page_options" 
                        value="azure_storage_account_name,azure_storage_account_primary_access_key,default_azure_storage_account_container_name,cname,azure_storage_use_for_default_upload,http_proxy_host,http_proxy_port,http_proxy_credentials,azure_storage_allow_per_user_settings" />

                        <p class="submit">
                              <input type="submit" class="button-primary" value="<?php
    _e('Save Changes'); ?>" />
                          </p>
                      </form>
              </td>
         </tr>
      </table>
  </div>
<?php
}

/**
 * Render Windows Azure Storage Plugin Options Screen
 * 
 * @param string $mode mode for logged in user (admin/nonadmin)
 * 
 * @return void
 */
function show_windows_azure_storage_settings($mode)
{
?>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">
          <label for="storage_account_name" title="Windows Azure Storage Account Name">Store Account Name</label>
        </th>
        <td>
          <input type="text" name="azure_storage_account_name" title="Windows Azure Storage Account Name" value="<?php
    echo get_option('azure_storage_account_name'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="azure_storage_account_primary_access_key" title="Windows Azure Storage Account Primary Access Key">Primary Access Key</label>
        </th>
        <td>
          <input type="text" name="azure_storage_account_primary_access_key" title="Windows Azure Storage Account Primary Access Key" value="<?php
    echo get_option('azure_storage_account_primary_access_key'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="storage_container_name" title="Default container to be used for storing media files">Default Storage Container</label>
        </th>
        <td>
            <select name="default_azure_storage_account_container_name" title="Default container to be used for storing media files">
<?php
    try {
        // Storage Account Settings
        $blobStorageHostName = Microsoft_WindowsAzure_Storage::URL_CLOUD_BLOB;
        $azure_storage_account_name = WindowsAzureStorageUtil::getAccountName();
        $azure_storage_account_primary_access_key 
            = WindowsAzureStorageUtil::getAccountKey();
        
        if (!empty($azure_storage_account_name) 
            && !empty($azure_storage_account_primary_access_key)
        ) {
            $storageClient = WindowsAzureStorageUtil::getStorageClient();
            $containers = $storageClient->listContainers();
            foreach ($containers as $container) {
?>
                <option value="<?php
                echo $container->Name; ?>" 
                                 <?php
                echo ($container->Name == WindowsAzureStorageUtil::getDefaultContainer() ? 'selected="selected"' : '') ?> ><?php
                echo $container->Name; ?></option>
<?php
            }
        }
    }
    catch (Exception $ex) {
        // Ignore exception as account keys are not yet set
        
    }
?>
            </select>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="cname" title="Use CNAME insted of Windows Azure Blob URL">CNAME</label>
        </th>
        <td>
          <input type="text" name="cname" title="Use CNAME insted of Windows Azure Blob URL" value="<?php
    echo WindowsAzureStorageUtil::getCNAME(); ?>" />
            <br /><small>Note: Use this option if you would like to display image urls belonging to your domain like http://MyDomain.com/ 
                  instead of http://YourAccountName.blob.core.windows.net/.</small>
            <br /><small>This CNAME must start with http(s) and administrator will have to update DNS entries accordingly.</small>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="http_proxy_host" title="Use HTTP proxy server host name if web proxy server is configured">HTTP Proxy Host Name</label>
        </th>
        <td>
          <input type="text" name="http_proxy_host" title="Use HTTP proxy server host name if web proxy server is configured" value="<?php
    echo WindowsAzureStorageUtil::getHttpProxyHost(); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="http_proxy_port" title="Use HTTP proxy port if web proxy server is configured">HTTP Proxy Port Name</label>
        </th>
        <td>
          <input type="text" name="http_proxy_port" title="Use HTTP proxy port if web proxy server is configured" value="<?php
    echo WindowsAzureStorageUtil::getHttpProxyPort(); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="http_proxy_credentials" title="Use HTTP proxy credentials if web proxy server is configured">HTTP Proxy Credentials</label>
        </th>
        <td>
          <input type="text" name="http_proxy_credentials" title="Use HTTP proxy credentials if web proxy server is configured" value="<?php
    echo WindowsAzureStorageUtil::getHttpProxyCredentials(); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="azure_storage_use_for_default_upload" title="Use Windows Azure Storage for default upload">Use Windows Azure Storage for default upload</label>
        </th>
        <td>
            <input type="checkbox" name="azure_storage_use_for_default_upload" title="Use Windows Azure Storage for default upload" value="1" id="azure_storage_use_for_default_upload" 
                       <?php
    echo (get_option('azure_storage_use_for_default_upload') ? 'checked="checked" ' : ''); ?> />
            <label for="wp-uploads"> Use Windows Azure Storage when uploading via WordPress' upload tab.</label>
            <br /><small>Note: Uncheck this to revert back to using your own web host for storage at anytime.</small>
        </td>
      </tr>
    </table>
<?php
}
?>
