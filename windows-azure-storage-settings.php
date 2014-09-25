<?php
/**
 * windows-azure-storage-settings.php
 * 
 * Shows various settings for Windows Azure Storage Plugin
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
 
use WindowsAzure\Blob\Models\PublicAccessType;

/**
 * Wordpress hook for displaying plugin options page 
 * 
 * @return void
 */
function windows_azure_storage_plugin_options_page()
{
?>
  <script type="text/javascript">
    function createContainer(url)
    {
        var htmlForm = document.getElementsByName("SettingsForm")[0];
        var action = document.getElementsByName("action")[0];
        if (typeof action !== "undefined") {
            action.name = 'action2';
        }

        htmlForm.action = url;
        htmlForm.submit();
    }

    function onContainerSelectionChanged(show)
    {
        var htmlForm = document.getElementsByName("SettingsForm")[0];
        var divCreateContainer = document.getElementById("divCreateContainer");
        if (htmlForm.elements["default_azure_storage_account_container_name"].value === "<Create New Container>") {
            divCreateContainer.style.display = "block";
            htmlForm.elements["submitButton"].disabled = true;
        
        } else {
            if (show) {
                divCreateContainer.style.display = "block";
            } else {
                divCreateContainer.style.display = "none";
            }

            htmlForm.elements["submitButton"].disabled = false;
        }
    }

  </script>
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

          <p>This plugin uses Windows Azure SDK for PHP (<a 
          href="https://github.com/WindowsAzure/azure-sdk-for-php/">https://github.com/WindowsAzure/azure-sdk-for-php/</a>). </p>
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
                    <form method="post" name="SettingsForm" action="options.php">
                        <?php
                            settings_fields('windows-azure-storage-settings-group');
                            show_windows_azure_storage_settings('admin');
                        ?>
                        <p class="submit">
                            <input type="submit" name="submitButton" class="button-primary" 
                                value="<?php _e('Save Changes'); ?>" />
                        </p>
                      </form>
              </td>
         </tr>
      </table>
  </div>
<?php
}

/**
 * Register custom settings for Windows Azure Storage Plugin
 * 
 * @return void
 */
function windows_azure_storage_plugin_register_settings()
{
    register_setting('windows-azure-storage-settings-group', 'azure_storage_account_name');
    register_setting('windows-azure-storage-settings-group', 'azure_storage_account_primary_access_key');
    register_setting('windows-azure-storage-settings-group', 'default_azure_storage_account_container_name');
    register_setting('windows-azure-storage-settings-group', 'cname');
    register_setting('windows-azure-storage-settings-group', 'azure_storage_use_for_default_upload');
    register_setting('windows-azure-storage-settings-group', 'http_proxy_host');
    register_setting('windows-azure-storage-settings-group', 'http_proxy_port');
    register_setting('windows-azure-storage-settings-group', 'http_proxy_username');
    register_setting('windows-azure-storage-settings-group', 'http_proxy_password');
    register_setting('windows-azure-storage-settings-group', 'azure_storage_allow_per_user_settings');
}

/**
 * Try to create a container.
 * 
 * @param boolean $success True if the operation succeeded, false otherwise.
 *
 * @return string The message to displayed
 */
function createContainerIfRequired(&$success)
{
    $success = true;
    if (array_key_exists("newcontainer", $_POST)) {
        if (!empty($_POST["newcontainer"])) {
          if (empty($_POST["azure_storage_account_name"]) || empty($_POST["azure_storage_account_primary_access_key"])) {
            $success = false;
            return '<FONT COLOR="red">Please specify Storage Account Name and Primary Access Key to create container</FONT>';
          }

          try
          {
              $storageClient = WindowsAzureStorageUtil::getStorageClient(
              $_POST["azure_storage_account_name"],
              $_POST["azure_storage_account_primary_access_key"],
              $_POST["http_proxy_host"],
              $_POST["http_proxy_port"],
              $_POST["http_proxy_username"],
              $_POST["http_proxy_password"]);
              WindowsAzureStorageUtil::createPublicContainer($_POST["newcontainer"], $storageClient);
              return '<FONT COLOR="green">The container \'' . $_POST["newcontainer"] . '\' successfully created <br/>'. 
                  'To use this container as default container, select it from the above drop down and click \'Save Changes\'</FONT>';
          } catch (Exception $e) {
              $success = false;
              return '<FONT COLOR="red">Container creation failed, Error: ' . $e->getMessage() . '</FONT>';
          }
      }

      $success = false;
      return '<FONT COLOR="red">Please specify name of the container to create</FONT>';
  }

  return null;
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
   $containerCreationStatus = true;
   $message = createContainerIfRequired($containerCreationStatus);
   // Storage Account Settings from db if already set
   $storageAccountName = WindowsAzureStorageUtil::getAccountName();
   $storageAccountKey = WindowsAzureStorageUtil::getAccountKey();
   $httpProxyHost = WindowsAzureStorageUtil::getHttpProxyHost();
   $httpProxyPort = WindowsAzureStorageUtil::getHttpProxyPort();
   $httpProxyUserName = WindowsAzureStorageUtil::getHttpProxyUserName();
   $httpProxyPassword = WindowsAzureStorageUtil::getHttpProxyPassword();
   $newContainerName = null;
   // Use the account settings in the $_POST if this page load is 
   // a result of container creation operation.
   if (array_key_exists("azure_storage_account_name", $_POST)) {
       $storageAccountName = $_POST["azure_storage_account_name"];
   }
   
   if (array_key_exists("azure_storage_account_primary_access_key", $_POST)) {
       $storageAccountKey = $_POST["azure_storage_account_primary_access_key"];
   }
   
   if (array_key_exists("http_proxy_host", $_POST)) {
       $httpProxyHost = $_POST["http_proxy_host"];
   }
   
   if (array_key_exists("http_proxy_port", $_POST)) {
       $httpProxyPort = $_POST["http_proxy_port"];
   }
   
   if (array_key_exists("http_proxy_host", $_POST)) {
       $httpProxyUserName = $_POST["http_proxy_host"];
   }
   
   if (array_key_exists("http_proxy_password", $_POST)) {
       $httpProxyPassword = $_POST["http_proxy_password"];
   }

   // We need to show the container name if the request for 
   // container creation fails.
   if (!$containerCreationStatus) {
       $newContainerName = $_POST["newcontainer"];
   }
   
    $ContainerResult = null;
    try 
    {
        if (!empty($storageAccountName) 
            && !empty($storageAccountKey)
        ) {
            $storageClient = WindowsAzureStorageUtil::getStorageClient(
                $storageAccountName,
                $storageAccountKey,
                $httpProxyHost,
                $httpProxyPort,
                $httpProxyUserName,
                $httpProxyPassword
            );
            $ContainerResult = $storageClient->listContainers();
            $defaultContainer = WindowsAzureStorageUtil::getDefaultContainer();
            $privateContainerWarning = null;
            if (!empty($defaultContainer)) {
                $getContainerAclResult = $storageClient->getContainerAcl($defaultContainer);
                $containerAcl = $getContainerAclResult->getContainerAcl();
                if ($containerAcl->getPublicAccess() == PublicAccessType::NONE) {
                    $privateContainerWarning = "<p style=\"margin: 10px; color: red;\">Warning: The container '$defaultContainer' you set as default is a private container. Plugin supports only public container, please set a public container as default</p>";
                }
            }
        }
    } catch (Exception $ex) {
        // Ignore exception as account keys are not yet set
    }
    echo $privateContainerWarning;
?>
    <table class="form-table" border="0">
      <tr valign="top">
        <th scope="row">
          <label for="storage_account_name" title="Windows Azure Storage Account Name">Store Account Name</label>
        </th>
        <td>
          <input type="text" name="azure_storage_account_name" title="Windows Azure Storage Account Name" value="<?php
    echo $storageAccountName; ?>" />
        </td>
        <td></td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="azure_storage_account_primary_access_key" title="Windows Azure Storage Account Primary Access Key">Primary Access Key</label>
        </th>
        <td>
          <input type="text" name="azure_storage_account_primary_access_key" title="Windows Azure Storage Account Primary Access Key" value="<?php
    echo $storageAccountKey; ?>" />
        </td>
        <td></td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="storage_container_name" title="Default container to be used for storing media files">Default Storage Container</label>
        </th>
        <td WIDTH="80px">
            <select name="default_azure_storage_account_container_name" title="Default container to be used for storing media files" onChange="onContainerSelectionChanged(false)">
<?php
            if (!empty($ContainerResult) && (count($ContainerResult->getContainers()) > 0)) {
                foreach ($ContainerResult->getContainers() as $container) {
?>
                    <option value="<?php echo $container->getName(); ?>"
                    <?php echo ($container->getName() == $defaultContainer ? 'selected="selected"' : '') ?> >
                    <?php echo $container->getName(); ?>
                    </option>
<?php
                }
?>
                <option value="<Create New Container>">&lt;Create New Container&gt;</option>
<?php
            }
?>
      </select>
    </td>
    <td>
    <div id="divCreateContainer" name="divCreateContainer" style="display:none;">
    <table style="border:1px solid black;">
    <tr>
      <td><label for="newcontainer" title="Name of the new container to create">Create New Container: </label></td>
      <td>
        <input type="text" name="newcontainer" title="Name of the new container to create" value="<?php echo $newContainerName; ?>" />
        <input type="button" class="button-primary" value="<?php _e('Create'); ?>" <?php echo "onclick=\"createContainer('" . $_SERVER['REQUEST_URI'] . "')\"" ?>/>
      </td>
    </tr>
    </table>
    </dv>
    </td>
    </tr>
    <tr valign="top">
        <td colspan="3" WIDTH="300" align="center"><?php echo  $message; ?></td>
    </tr>
      <tr valign="top">
        <th scope="row">
          <label for="cname" title="Use CNAME insted of Windows Azure Blob URL">CNAME</label>
        </th>
        <td colspan="2">
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
    echo $httpProxyHost; ?>" />
        </td>
    <td></td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="http_proxy_port" title="Use HTTP proxy port if web proxy server is configured">HTTP Proxy Port Name</label>
        </th>
        <td>
          <input type="text" name="http_proxy_port" title="Use HTTP proxy port if web proxy server is configured" value="<?php
    echo $httpProxyPort; ?>" />
        </td>
    <td></td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="http_proxy_username" title="Use HTTP proxy user name if credential is required to access web proxy server">HTTP Proxy User Name</label>
        </th>
        <td>
          <input type="text" name="http_proxy_username" title="Use HTTP proxy user name if credential is required to access web proxy server" value="<?php
    echo $httpProxyUserName; ?>" />
        </td>
        <td></td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="http_proxy_password" title="Use HTTP proxy password if credential is required to access web proxy server">HTTP Proxy Password</label>
        </th>
        <td>
          <input type="text" name="http_proxy_password" title="Use HTTP proxy password if credential is required to access web proxy server" value="<?php
    echo $httpProxyPassword; ?>" />
        </td>
      <td></td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="azure_storage_use_for_default_upload" title="Use Windows Azure Storage for default upload">Use Windows Azure Storage for default upload</label>
        </th>
        <td colspan="2">
            <input type="checkbox" name="azure_storage_use_for_default_upload" title="Use Windows Azure Storage for default upload" value="1" id="azure_storage_use_for_default_upload" 
                       <?php
    echo (get_option('azure_storage_use_for_default_upload') ? 'checked="checked" ' : ''); ?> />
            <label for="wp-uploads"> Use Windows Azure Storage when uploading via WordPress' upload tab.</label>
            <br /><small>Note: Uncheck this to revert back to using your own web host for storage at anytime.</small>
        </td>
      </tr>
    </table>
<?php
    if (empty($ContainerResult) || !$containerCreationStatus || count($ContainerResult->getContainers()) === 0) {
    // 1. If $containerResult object is null means the storage account is not yet set
    // show the create container div
?>
    <script type="text/javascript">
         onContainerSelectionChanged(true);
    </script>

<?php
    }
}
?>
