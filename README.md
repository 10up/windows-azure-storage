# Windows Azure Storage for WordPress

Use the Windows Azure Storage service to host your website's media files.

**Contributors:** [msopentech](https://profiles.wordpress.org/msopentech), [10up](https://profiles.wordpress.org/10up), [morganestes](https://profiles.wordpress.org/morganestes), [stevegrunwell](https://profiles.wordpress.org/stevegrunwell), [lpawlik](https://profiles.wordpress.org/lpawlik), [rittesh.patel](https://profiles.wordpress.org/rittesh.patel)  
**Tags:** [Microsoft](https://wordpress.org/plugins/tags/microsoft), [Microsoft Open Technologies](https://wordpress.org/plugins/tags/microsoft-open-technologies), [Windows Azure](https://wordpress.org/plugins/tags/windows-azure), [Windows Azure Storage](https://wordpress.org/plugins/tags/windows-azure-storage), [Media Files](https://wordpress.org/plugins/tags/media-files), [Upload](https://wordpress.org/plugins/tags/upload), [CDN](https://wordpress.org/plugins/tags/cdn), [blob storage](https://wordpress.org/plugins/tags/blob-storage)  
**Requires at least:** 4.0  
**Tested up to:** 4.7.2  
**Stable tag:** 4.0.2  
**License:** [BSD 2-Clause](http://www.opensource.org/licenses/bsd-license.php)  

## Description ##

This WordPress plugin allows you to use Windows Azure Storage Service to host
your media and uploads for your WordPress powered website. Windows Azure Storage is an effective way
to infinitely scale storage of your site and leverage Azure's global infrastructure.

For more details on Windows Azure Storage, please visit the <a href="https://azure.microsoft.com/en-us/services/storage/">Microsoft Azure website</a>.

## Installation ##

1. Upload the plugin files to the `/wp-content/plugins/windows-azure-storage` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. Use the Settings->Windows Azure screen to configure the plugin.

## Changelog ##

### 4.0.2 ###
* Bug fix for 0-byte uploads.

### 4.0.1 ###
* Fix blob name while media file
* Show admin notice if can't access files directly

### 4.0.0 ###
* Removed old PHP SDK and use WordPress HTTP API based client library.
* Added compatibility with API version 2015-12-11.
* Added compatibility with PHP 5.3+.
* Improved overall performance.
* L10N/I18N: Round 2 of preparing strings for translation.
* Refactored code to match WordPress standards.
* Better UX by adding more feedback during long operations.
* Integrated Azure Blob browser into WordPress Media Library.
* Added option to keep local files after uploading them to Azure Blob.
* Exposed new developer filters.
* Deduplicated code functionality.
* Security: validate, sanitize, and escape (allthethings).

### 3.0.1 ###
* Fix browsing and uploading caused by broken nonce checks.
* Normalize the SDK path for activation checks.

### 3.0.0 ###
* Security: fixes a bug that could allow unauthorized deletion of remotely-stored media.
* Security: validate, sanitize, and escape (allthethings).
* Security: use `https://` URLs by default, and warn if an insecure CNAME is configured.
* Security: introduce permissions checks for specific actions within the plugin.
* L10N/I18N: Round 1 of preparing strings for translation.
* UI: Editor button more closely matches the WordPress Admin UI.
* Add `srcset` to images added through the Media Library when Azure is the default media handler. (Props @patricknami).

### 2.2 ###
* Fixed network activation bug in WordPress multisite.
* Fixed the issue with duplicate blob names in XML-RPC.

### 2.1 ###
* Fixed the issue with duplicate blob names.
* Fixed the bug in uploading video files to blob storage.
* Fixed the bug with forward slash in front of image thumbnail filenames.
* Fixed the bug with year and month getting trimmed for file system images.

### 2.0 ###
* Updated to use Windows Azure SDK for PHP from https://github.com/WindowsAzure/azure-sdk-for-php and fixed to work with WordPress 3.4.1

### 1.9 ###
* Fixed case sensitivity error in file names on Linux

### 1.8 ###
* Bug fixed in generating blob storage URL when using Windows Azure Storage emulator

### 1.7 ###
* Added support to upload video files to blob storage

### 1.5 ###
* Included Windows Azure SDK for PHP v4.1.0 with the plugin. Now setting mime-type for uploaded file to blob storage.

### 1.4 ###
* Included Windows Azure SDK for PHP v4.0.2 with the plugin.

### 1.3 ###
* Included Windows Azure SDK for PHP v4.0.1 with the plugin, so no need to install the SDK separetely. Also fixed thumbnail handling issue while uploading files when some specific theme is enabled.

### 1.2 ###
* This release is compatible with Windows Azure SDK for PHP v3.0.0. It also fixes issue with deleting media files when thumbnails are associated.

### 1.1 ###
* This release is compatible with Windows Azure SDK for PHP v2.1.0 and WordPress 3.1

### 1.0 ###
* First release of Windows Azure Storage plugin for WordPress


## Upgrade Notice ##

### 3.0.0 ###
This release features several security fixes and enhancements.
It is highly recommended that all users upgrade immediately.


## Known Issues ##

### Storage Account Versions ###
Storage accounts can be created via CLI, classic Azure portal, or the new Azure portal,
with varying results.

If a Storage account is created with the new Azure portal, authentication will fail,
resulting in the inability to view/add containers or files. Creating a Storage account
with the Azure CLI should allow the plugin to work with new Storage accounts.

### Responsive Images in WordPress 4.4 ###
Images uploaded to the Azure Storage service will not automatically receive responsive versions.
Images added through the WordPress Media Loader *should* get automatically converted to responsive
images when inserted into a post or page.
We are investigating options for full support of responsive images in the plugin.


