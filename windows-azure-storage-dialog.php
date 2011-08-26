<?php
/**
 * windows-azure-storage-dialog.php
 * 
 * Shows popup dialog when clicked on the Windows Azure Toolbar 
 * 
 * Version: 1.4
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
require_once 'Microsoft/WindowsAzure/Credentials/SharedAccessSignature.php';

/**
 * Setup javascripts and css needed by the Windows Azure Storage popup dialog
 * 
 * @return void
 */
function windows_azure_storage_dialog_scripts()
{
?>
    <script type="text/javascript" 
        src="../wp-content/plugins/windows-azure-storage/js/windows-azure-storage.js">
    </script>
    <link rel="stylesheet" 
        href="../wp-content/plugins/windows-azure-storage/styles/styles.css" 
        type="text/css" />
<?php
}

/**
 * Add browse, search and upload tab to the array needed by wordpress hook
 * 
 * @return array $content Tab array needed by wordpress hook
 */
function windows_azure_storage_dialog_add_tab()
{
    $content = array();
    $content["browse"] = __("Browse");
    $content["search"] = __("Search");
    $content["upload"] = __("Upload");
    
    return $content;
}

/**
 * Render Browse Tab in the Windows Azure Storage popup dialog
 * 
 * @return void
 */
function windows_azure_storage_dialog_browse_tab()
{
    // remove all registerd filters for the tabs
    unset($GLOBALS['wp_filter']['media_upload_tabs']);
    
    // register our filter for the tabs
    add_filter("media_upload_tabs", "windows_azure_storage_dialog_add_tab");
    
    media_upload_header();
    
    $storageClient = WindowsAzureStorageUtil::getStorageClient();
    $azure_storage_account_name = WindowsAzureStorageUtil::getAccountName();
    $azure_storage_account_primary_access_key 
        = WindowsAzureStorageUtil::getAccountKey();
        
    $default_azure_storage_account_container_name 
        = WindowsAzureStorageUtil::getDefaultContainer();
    if (empty($azure_storage_account_name) 
        || empty($azure_storage_account_primary_access_key)
    ) {
        echo '<h3 style="margin: 10px;">'
            . 'Azure Storage Account not yet configured</h3>';
        echo '<p style="margin: 10px;">'
            . 'Please configure the account in Windows Azure Settings Tab.</p>';
    } else {
        // Set selected container. If none, then use default container
        $selected_container_name = $default_azure_storage_account_container_name;
        if (!empty($_POST['selected_container'])) {
            $selected_container_name = $_POST['selected_container'];
        } else if (!empty($_GET['selected_container'])) {
            $selected_container_name = $_GET['selected_container'];
        }

        // Check if blob has to be deleted
        if (!empty($_GET['deleteBlob'])) {
            deleteBlob(
                $storageClient, 
                $selected_container_name, 
                $_GET['deleteBlob']
            );
        }

        if (!empty($_POST['ModifyContainerACL'])) {
            if ($_POST['ModifyContainerACL'] == 'true') {
                $newACL = Microsoft_WindowsAzure_Storage_Blob::ACL_PUBLIC;
            } else {
                $newACL = Microsoft_WindowsAzure_Storage_Blob::ACL_PRIVATE;
            }

            $storageClient->setContainerAcl($selected_container_name, $newACL);
        } else if ((!empty($_POST['DeleteAllBlobs'])) && ($_POST['DeleteAllBlobs'] == 'true')) {
            // Get list of blobs in specified container
            $blobs = $storageClient->listBlobs($selected_container_name);

            // Delete each blob in specified container
            foreach ($blobs as $blob) {
                deleteBlob($storageClient, $selected_container_name, $blob->Name);
            }

            echo '<p style="margin: 10px; color: red;">' 
                . 'Deleted all files in Windows Azure Storage Container "' 
                . $selected_container_name . '"</p><br/>';
        }

        // Handle file search
        if ((!empty($_POST['action'])) && ($_POST["action"] == "Search")) {
            try {
                $fileTagFilter = $_POST["searchFileTag"];
                $fileNameFilter = $_POST["searchFileName"];
                $fileTypeFilter = $_POST["searchFileType"];
                $searchContainer = $_POST["searchContainer"];
                
                if (empty($fileTagFilter) 
                    && empty($fileNameFilter) 
                    && empty($fileTypeFilter)
                ) {
                    echo '<p style="margin: 10px;">'
                        . 'Search criteria not specified.</p><br/>';
                } else {
                    $criteria = array();
                    if (!empty($fileNameFilter)) {
                        $criteria[] = "file name like " . $fileNameFilter;
                    }
                    if (!empty($fileTypeFilter)) {
                        $criteria[] = "file type like " . $fileTypeFilter;
                    }
                    if (!empty($fileTagFilter)) {
                        $criteria[] = "tag like '" . $fileTagFilter . "'";
                    }

                    $searchResult = Array();
                    if ($searchContainer == "ALL_CONTAINERS") {
                        $criteria[] = "in 'all containers'";
                        $containers = $storageClient->listContainers();
                        foreach ($containers as $container) {
                            // Get list of blobs in specified container
                            $blobs = $storageClient->listBlobs($container->Name);

                            foreach ($blobs as $blob) {
                                if (!empty($fileNameFilter)) {
                                    if (stripos($blob->Name, $fileNameFilter) === false) {
                                        continue;
                                    }
                                }
                                
                                $metadata = $storageClient->getBlobMetadata(
                                    $container->Name, $blob->Name
                                );
                                if (!empty($fileTypeFilter)) {
                                    if (stripos($metadata["mimetype"], $fileTypeFilter) === false) {
                                        continue;
                                    }
                                }
                                if (!empty($fileTagFilter)) {
                                    if (stripos($metadata["tag"], $fileTagFilter) === false) {
                                        continue;
                                    }
                                }
                                
                                $searchResult[] = WindowsAzureStorageUtil::getStorageUrlPrefix(false) . "/$container->Name/$blob->Name";
                            }
                        }
                    } else {
                        $criteria[] = "in container '" . $searchContainer . "'";
                        
                        // Get list of blobs in specified container
                        $blobs = $storageClient->listBlobs($searchContainer);

                        foreach ($blobs as $blob) {
                            if (!empty($fileNameFilter)) {
                                if (stripos($blob->Name, $fileNameFilter) === false) {
                                    continue;
                                }
                            }

                            $metadata = $storageClient->getBlobMetadata($searchContainer, $blob->Name);
                            if (!empty($fileTypeFilter)) {
                                if (stripos($metadata["mimetype"], $fileTypeFilter) === false) {
                                    continue;
                                }
                            }

                            if (!empty($fileTagFilter)) {
                                if (stripos($metadata["tag"], $fileTagFilter) === false) {
                                    continue;
                                }
                            }

                            $searchResult[] = WindowsAzureStorageUtil::getStorageUrlPrefix(false) . "/$searchContainer/$blob->Name";
                        }
                    }

                    echo '<h3 style="margin: 10px;">Search Result</h3>';

                    if (empty($searchResult)) {
                        echo '<p style="margin: 10px;">No file found matching specified criteria (' . implode(', ', $criteria) . ')</p><br/>';
                    } else {
                        echo '<p style="margin: 10px;">Found ' . count($searchResult) . ' file(s) matching specified criteria (' . implode(', ', $criteria) . ')</p><br/>';
                        foreach ($searchResult as $url) {
                            echo "<img style='margin: 10px;' src=\"$url\" width=\"32\" height=\"32\"";
                            echo "onmouseover=\"this.height = 50;this.width = 50; this.style.border = '3px solid yellow';\" onmouseout=\"this.height = 32;this.width = 32; this.style.border = '0px solid black'\" onclick=\"return insertImageTag('$url');\" /> ";
                        }
                    }
                    echo "<hr/>";
                }
            }
            catch (Exception $e) {
                echo '<p style="margin: 10px; color: red;">Error in searching files: ' . $e->getMessage() . "</p><br/>";
            }
        }
        $first_container_name = "";
?>      
        <form name="SelectContainerForm" style="margin: 10px;" method="post" action="<?php
        echo $_SERVER['REQUEST_URI']; ?>">
          <table style="margin: 10px; border-width: 2px;border-color: black;" >
            <tr>
                <td><b>Container Name:</b></td>
                <td>
                  <select name="selected_container" title="Stoarge container to be used for storing media files" onChange="document.SelectContainerForm.submit()">
    <?php
        try {
            $storageClient = WindowsAzureStorageUtil::getStorageClient();
            $containers = $storageClient->listContainers();
            foreach ($containers as $container) {
                if (empty($first_container_name)) {
                    $first_container_name = $container->Name;
                }
?>
                      <option value="<?php
                echo $container->Name ?>"
                        <?php
                echo ($container->Name == $selected_container_name ? 'selected="selected"' : '') ?> ><?php
                echo $container->Name ?>
                      </option>
    <?php
            }
        }
        catch (Exception $ex) {
            // Ignore exception as account keys are not yet set
            
        }
?>
                  </select>
                </td>
            </tr>
          </table>
        </form>

        <table style="margin: 10px; border-width: 2px;border-color: black;">
        <tr>
            <td>
        <?php
        try {
            if (empty($selected_container_name)) {
                echo '<p style="margin: 10px; color: red;">Default Azure Storage Container name is not yet configured. Please configure it in the Windows Azure Settings Tab.</p>';
                $selected_container_name = $first_container_name;
            }
            
            // Modify Container ACL
            if ($storageClient->getContainerAcl($selected_container_name) == Microsoft_WindowsAzure_Storage_Blob::ACL_PRIVATE) {
                echo "<p style='margin: 10px; color: red'>This is private container. " . "Please set the expiration duration for files that are not visible.</p>";
                $modifyContainerACLFormTitle = "Make '$selected_container_name' Container Public";
                $newACL = "true";
            } else {
                $modifyContainerACLFormTitle = "Make '$selected_container_name' Container Private";
                $newACL = "false";
            }
?>
                <form name="ModifyContainerACLForm" style="margin: 10px;" method="post" action="<?php
            echo $_SERVER['REQUEST_URI']; ?>">
                    <input type='hidden' name='ModifyContainerACL' value='<?php
            echo $newACL; ?>' />
                    <input type='hidden' name='selected_container' value='<?php
            echo $selected_container_name; ?>' />
                    <u onMouseOver="style.cursor='hand'" onclick='document.ModifyContainerACLForm.submit()' style="color: red;"><?php
            echo $modifyContainerACLFormTitle; ?></u>
                </form>
<?php
            // Get list of blobs in specified container
            $blobs = $storageClient->listBlobs($selected_container_name);
            
            if (empty($blobs)) {
                echo "<p style='margin: 10px;'>No items in container '$selected_container_name'.</p>";
            } else {
                echo '<p style="margin: 10px;">Note: Click on the image to insert image URL into the blog!</p><br/>';
                foreach ($blobs as $blob) {
                    $url = WindowsAzureStorageUtil::getStorageUrlPrefix(false) . "/$selected_container_name/$blob->Name";
                    $containsSignature = "false";

                    $metadata = $storageClient->getBlobMetadata($selected_container_name, $blob->Name);
                    if (!empty($metadata["signature"])) {
                        if ($storageClient->getContainerAcl($selected_container_name) == Microsoft_WindowsAzure_Storage_Blob::ACL_PRIVATE) {
                            $url = $url . '?' . $metadata['signature'];
                            $containsSignature = "true";
                        }
                    }

                    $fileExt = substr(strrchr($blob->Name, '.'), 1);
                    switch (strtolower($fileExt)) {
                    case "jpg":
                    case "jpeg":
                    case "gif":
                    case "bmp":
                    case "png":
                    case "tiff":
                        echo "<img style='margin: 10px;' src=\"$url\" width=\"32\" height=\"32\"";
                        echo "onmouseover=\"this.height = 50;this.width = 50; this.style.border = '3px solid yellow';\" onmouseout=\"this.height = 32;this.width = 32; this.style.border = '0px solid black'\" onclick=\"return insertImageTag('$url', '$containsSignature');\"/>";
                        break;

                    default:
                        echo "<a style='margin: 10px;' href=\"$url\"";
                        echo "onclick=\"return insertImageTag('$url', '$containsSignature');\">" . $blob->Name . "<a/>";
                        break;
                    }
                    $deleteLink = 'media-upload.php?post_id=0&tab=browse&deleteBlob=' . urlencode($blob->Name) . '&selected_container=' . urlencode($selected_container_name);
                    echo "<a style='color: red;' href=\"" . $deleteLink . "\">x</a> ";
                }
            }
        }
        catch (Exception $e) {
            echo '<p style="margin: 10px; color: red;">Error in listing storage containers: ' . $e->getMessage() . "</p><br/>";
        }
?>
            </td>
        </tr>
        </table>

<?php
        if (!empty($blobs)) {
?>    
        <form name="DeleteAllBlobsForm" style="margin: 20px;" method="post" action=""<?php
            echo $_SERVER['REQUEST_URI']; ?>">
            <input type='hidden' name='DeleteAllBlobs' value='true' />
            <input type='hidden' name='selected_container' value='<?php
            echo $selected_container_name; ?>' />
            <u onMouseOver="style.cursor='hand'" onclick='document.DeleteAllBlobsForm.submit()' style="color: red;">Delete All Files</u>
        </form>
<?php
        }
    }
}

/**
 * Render Search Tab in the Windows Azure Storage popup dialog
 * 
 * @return void
 */
function windows_azure_storage_dialog_search_tab()
{
    // remove all registerd filters for the tabs
    unset($GLOBALS['wp_filter']['media_upload_tabs']);
    
    // register our filter for the tabs
    add_filter("media_upload_tabs", "windows_azure_storage_dialog_add_tab");
    
    media_upload_header();
    
    $storageClient = WindowsAzureStorageUtil::getStorageClient();
    $azure_storage_account_name = WindowsAzureStorageUtil::getAccountName();
    $azure_storage_account_primary_access_key = WindowsAzureStorageUtil::getAccountKey();
    $default_azure_storage_account_container_name = WindowsAzureStorageUtil::getDefaultContainer();
    
    if (empty($azure_storage_account_name) || empty($azure_storage_account_primary_access_key)) {
        echo '<h3 style="margin: 10px;">Azure Storage Account not yet configured</h3>';
        echo '<p style="margin: 10px;">Please configure the account in Windows Azure Settings Tab.</p>';
    } else {
        // Set selected container. If none, then use default container
        $selected_container_name = $default_azure_storage_account_container_name;
        if (!empty($_POST['selected_container'])) {
            $selected_container_name = $_POST['selected_container'];
        }
        if (!empty($_GET['selected_container'])) {
            $selected_container_name = $_GET['selected_container'];
        }
        $browseUrl = str_replace('search', 'browse', $_SERVER['REQUEST_URI']);
?>
        <h3 style="margin: 10px;">Search Files</h3>
        <div id="search-form">
            <form style="margin: 10px;" method="post" action="<?php
        echo $browseUrl; ?>">
                <table class="form-table">
                  <tr valign="top">
                    <th scope="row">
                      <label for="searchFileTag">Tag:</label>
                    </th>
                    <td>
                      <input type="text" name="searchFileTag" value="" />
                    </td>
                  </tr>

                  <tr valign="top">
                    <th scope="row">
                      <label for="searchFileName">File Name:</label>
                    </th>
                    <td>
                      <input type="text" name="searchFileName" value="" />
                    </td>
                  </tr>

                  <tr valign="top">
                    <th scope="row">
                      <label for="searchFileType">File Type:</label>
                    </th>
                    <td>
                      <input type="text" name="searchFileType" value="" />
                    </td>
                  </tr>

                  <tr valign="top">
                    <th scope="row">
                      <label for="ContainerName">Container Name:</label>
                    </th>
                    <td>
                       <select name="searchContainer" title="Search within this container">
            <?php
        try {
            $storageClient = WindowsAzureStorageUtil::getStorageClient();
            $containers = $storageClient->listContainers();

            foreach ($containers as $container) {
?>
                              <option value="<?php
                echo $container->Name ?>"
                                <?php
                echo ($container->Name == $selected_container_name ? 'selected="selected"' : '') ?> ><?php
                echo $container->Name ?>
                              </option>
            <?php
            }

            echo '<option value="ALL_CONTAINERS">All Containers</option>';
        }
        catch (Exception $ex) {
            // Ignore exception as account keys are not yet set
            
        }
?>
                          </select>
                        </td>
                    </tr>
                </table>

                <input type='hidden' name='action' value='Search' />
                <p class="submit">
                    <input type="submit" class="button-primary" value="Search" />
                </p>
            </form>
        </div>
        <hr/>
<?php
    }
}

/**
 * Render Upload Tab in the Windows Azure Storage popup dialog
 * 
 * @return void
 */
function windows_azure_storage_dialog_upload_tab()
{
    // remove all registerd filters for the tabs
    unset($GLOBALS['wp_filter']['media_upload_tabs']);
    
    // register our filter for the tabs
    add_filter("media_upload_tabs", "windows_azure_storage_dialog_add_tab");
    
    media_upload_header();
    $storageClient = WindowsAzureStorageUtil::getStorageClient();
    $azure_storage_account_name = WindowsAzureStorageUtil::getAccountName();
    $azure_storage_account_primary_access_key = WindowsAzureStorageUtil::getAccountKey();
    $default_azure_storage_account_container_name = WindowsAzureStorageUtil::getDefaultContainer();
    
    if (empty($azure_storage_account_name) || empty($azure_storage_account_primary_access_key)) {
        echo '<h3 style="margin: 10px;">Azure Storage Account not yet configured</h3>';
        echo '<p style="margin: 10px;">Please configure the account in Windows Azure Settings Tab.</p>';
    } else {
        // Set selected container. If none, then use default container
        $selected_container_name = $default_azure_storage_account_container_name;
        
        if (!empty($_POST['selected_container'])) {
            $selected_container_name = $_POST['selected_container'];
        } else if (!empty($_GET['selected_container'])) {
            $selected_container_name = $_GET['selected_container'];
        }
        
        if (empty($selected_container_name)) {
            echo '<p style="margin: 10px; color: red;">Default Azure Storage Container name is not yet configured. Please configure it in the Windows Azure Settings Tab.</p>';
            $selected_container_name = $first_container_name;
        }
        
        // Handle file upload
        if ((!empty($_POST['action'])) && ($_POST["action"] == "Upload")) {
            if ($_FILES["uploadFileName"]["error"] == 0) {
                if (!file_exists($_FILES['uploadFileName']['tmp_name'])) {
                    echo "<p>Uploaded file " . $_FILES['uploadFileName']['tmp_name'] . " does not exist</p><br/>";
                } else {
                    $metaData = array('mimetype' => $_FILES['uploadFileName']['type']);
                    if (!empty($_POST["uploadFileTag"])) {
                        $metaData["tag"] = $_POST["uploadFileTag"];
                    }
                    if (!empty($_POST['expiryTime'])) {
                        $start = time();
                        $end = $start + $_POST['expiryTime'] * 60;
                        $credentials = new Microsoft_WindowsAzure_SharedAccessSignatureCredentials(WindowsAzureStorageUtil::getAccountName(), WindowsAzureStorageUtil::getAccountKey(), false);
                        $signature = $credentials->createSignature($selected_container_name . "/" . $_FILES['uploadFileName']['name'], 'b', 'r', isoDate($start), isoDate($end));
                        $signatureURL = "st=" . urlencode(isoDate($start)) . "&se=" . urlencode(isoDate($end));
                        $signatureURL = $signatureURL . "&sr=b&sp=r&sig=" . urlencode($signature);
                        $metaData['signature'] = $signatureURL;
                    }
                    try {
                        $storageClient->putBlob($selected_container_name, $_FILES['uploadFileName']['name'], $_FILES['uploadFileName']['tmp_name'], $metaData);
                        $uploadMessage = "Successfully uploaded file '" . $_FILES['uploadFileName']['name'] . "' to the container '" . $selected_container_name . "'.";
                    }
                    catch (Exception $e) {
                        $uploadMessage = "Error in uploading file '" . $_FILES['uploadFileName']['name'] . "', Error: " . $e->getMessage();
                    }
                }
            }
        }
?>
        <h3 style="margin: 10px;">Upload New File</h3>
        <div id="upload-form">
            <form name="UploadNewFileForm" style="margin: 10px;" method="post" enctype="multipart/form-data" action="<?php
        echo $_SERVER['REQUEST_URI']; ?>">
                <table class="form-table">
                  <tr valign="top">
                    <th scope="row">
                      <label for="selected_container">Container Name :</label>
                    </th>
                    <td>
                      <select name="selected_container" title="Stoarge container to be used for uploading media files" onChange="document.UploadNewFileForm.submit()">
<?php
        try {
            $storageClient = WindowsAzureStorageUtil::getStorageClient();
            $containers = $storageClient->listContainers();
            foreach ($containers as $container) {
                if (empty($selected_container_name)) {
                    $selected_container_name = $container->Name;
                }
?>
                        <option value="<?php
                echo $container->Name; ?>"
                          <?php
                echo ($container->Name == $selected_container_name ? 'selected="selected"' : '') ?> ><?php
                echo $container->Name ?></option>
<?php
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
                      <label for="uploadFileTag">Tag:</label>
                    </th>
                    <td>
                      <input type="text" name="uploadFileTag" value="" />
                    </td>
                  </tr>

                  <tr valign="top">
                    <th scope="row">
                      <label for="uploadFileName">File Name:</label>
                    </th>
                    <td>
                      <input type="file" name="uploadFileName" />
                    </td>
                  </tr>

                  <tr valign="top">
                    <th scope="row">
                      <label for="expiryTime">Expiration Duration (minutes) :</label>
                    </th>
                    <td>
                      <input type="text" name="expiryTime"
        <?php
        if ($storageClient->getContainerAcl($selected_container_name) == Microsoft_WindowsAzure_Storage_Blob::ACL_PUBLIC) {
            echo 'disabled="disabled" style="background-color: gray;"/>';
            echo '<br/><small> Note: Files in public container will never expire, hence disabled.</small>';
        } else {
            echo ' />';
        }
        ?>
                    </td>
                  </tr>   
                  
                </table>
                
                <input type='hidden' name='action' value='Upload' />
                <p class="submit">
                    <input type="submit" class="button-primary" value="Upload" />
                </p>
            </form>
        </div>
<?php
        if (!empty($uploadMessage)) {
            echo '<p style="margin: 10px; color: red;">' 
                . $uploadMessage . "</p><br/>";
        }
    }
}

/**
 * Delete a blob from specified container
 * 
 * @param Microsoft_WindowsAzure_Storage_Blob $storageClient Storage Client
 * 
 * @param string $containerName Name of the parent container
 * 
 * @param string $blobName Name of the blob to be deleted
 * 
 * @return void
 */
function deleteBlob($storageClient, $containerName, $blobName)
{
    try {
        if ($storageClient->blobExists($containerName, $blobName)) {
            remove_query_arg('deleteBlob', $_SERVER['REQUEST_URI']);
            $storageClient->deleteBlob($containerName, $blobName);
        }
    }
    catch (Exception $e) {
        echo '<p style="margin: 10px; color: red;">' 
        . 'Error in deleting blob $blobName from container $containerName : ' 
        . $e->getMessage() . "</p><br/>";
    }
}

/**
 * Generate ISO 8601 compliant date string in UTC time zone
 *
 * @param int $timestamp input timestamp for conversion
 * 
 * @return string
 */
function isoDate($timestamp = null)
{
    $tz = @date_default_timezone_get();
    @date_default_timezone_set('UTC');
    if (is_null($timestamp)) {
        $timestamp = time();
    }
    
    $returnValue = str_replace('+00:00', 'Z', @date('c', $timestamp));
    @date_default_timezone_set($tz);
    
    return $returnValue;
}
?>
