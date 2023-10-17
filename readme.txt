=== Microsoft Azure Storage for WordPress ===
Contributors:      msopentech, 10up, morganestes, stevegrunwell, lpawlik, ritteshpatel, johnwatkins0, rickalee, eflorea, phyrax, ravichandra, jeffpaul
Tags:              Microsoft, Microsoft Open Technologies, Microsoft Azure, Microsoft Azure Storage, Media Files, Upload, CDN, blob storage
Requires at least: 5.7
Tested up to:      6.3
Requires PHP:      7.4
Stable tag:        4.4.0
License:           BSD 2-Clause
License URI:       http://www.opensource.org/licenses/bsd-license.php

Use the Microsoft Azure Storage service to host your website's media files.

== Description ==

This WordPress plugin allows you to use Microsoft Azure Storage Service to host your media and uploads for your WordPress powered website. Microsoft Azure Storage is an effective way to infinitely scale storage of your site and leverage Azure's global infrastructure.

For more details on Microsoft Azure Storage, please visit the [Microsoft Azure website](https://azure.microsoft.com/en-us/services/storage/).

For more details on configuring a Microsoft Azure Storage account and on using the plugin with the Block Editor or Classic Editor, please visit the [user guide](https://github.com/10up/windows-azure-storage/blob/develop/UserGuide.md).

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/windows-azure-storage` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. Use the Settings->Microsoft Azure screen to configure the plugin.

For multisites or to enforce Azure Blob Storage settings, you can define the following constants in wp-config.php:

* MICROSOFT_AZURE_ACCOUNT_NAME - Account Name
* MICROSOFT_AZURE_ACCOUNT_KEY - Account Primary Access Key
* MICROSOFT_AZURE_CONTAINER - Azure Blob Container
* MICROSOFT_AZURE_CNAME - Domain: must start with http(s)://
* MICROSOFT_AZURE_USE_FOR_DEFAULT_UPLOAD - boolean (default false)

See Settings->Microsoft Azure for more information.

== Changelog ==

= 4.4.0 - 2023-10-17 =
**Note that this release bumps the minimum PHP version from 7.4 to 8.0**

* **Added:** Check for minimum required PHP version before loading the plugin (props [@bmarshall511](https://github.com/bmarshall511), [@iamdharmesh](https://github.com/iamdharmesh) via [#200](https://github.com/10up/windows-azure-storage/pull/200)).
* **Changed:** Update to the `2020-04-08` version of the Azure Blob Storage API (props [@thrijith](https://github.com/thrijith), [@colegeissinger](https://github.com/colegeissinger) via [#136](https://github.com/10up/windows-azure-storage/pull/136)).
* **Changed:** Bump minimum PHP version from 7.4 to 8.0 (props [@thrijith](https://github.com/thrijith), [@colegeissinger](https://github.com/colegeissinger) via [#136](https://github.com/10up/windows-azure-storage/pull/136)).
* **Changed:** Bump WordPress version "tested up to" 6.3 (props [@QAharshalkadu](https://github.com/QAharshalkadu), [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#192](https://github.com/10up/windows-azure-storage/pull/192), [#198](https://github.com/10up/windows-azure-storage/pull/198)).
* **Changed:** Bump `cypress` from 10.11.0 to 13.1.0, `@10up/cypress-wp-utils` from 0.1.0 to 0.2.0 and `@wordpress/env` from 5.13.0 to 8.7.0, to ensure E2E tests work on the latest version of WordPress (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9) via [#199](https://github.com/10up/windows-azure-storage/pull/199)).

= 4.3.5 - 2023-07-10 =
* **Added:** Update docs with information on migrating to a new Azure Storage Account and compatibility issues with Media Library editing tools (props [@gioamato](https://github.com/gioamato), [@jeffpaul](https://github.com/jeffpaul), [@rickalee](https://github.com/rickalee), [@colegeissinger](https://github.com/colegeissinger) via [#163](https://github.com/10up/windows-azure-storage/issues/163)).
* **Added:** Setup E2E tests on Cypress (props [@barneyjeffries](https://github.com/barneyjeffries), [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#179](https://github.com/10up/windows-azure-storage/issues/179)).
* **Changed:** Bump WordPress version "tested up to" 6.2 (props [@Sidsector9](https://github.com/Sidsector9), [@jeffpaul](https://github.com/jeffpaul) via [#183](https://github.com/10up/windows-azure-storage/pull/183)).
* **Fixed:** Ensure things work properly on PHP 7.4 (props [@dkotter](https://github.com/dkotter), [@Sidsector9](https://github.com/Sidsector9), [@faisal-alvi](https://github.com/faisal-alvi) via [#177](https://github.com/10up/windows-azure-storage/pull/177)).

= 4.3.4 - 2023-01-12 =
* **Note that this release bumps the WordPress minimum version from 4.0 to 5.7 and the PHP minimum version from 5.6 to 7.4.**

* **Added:** Add automated PHPCS scanning (props [@csloisel](https://github.com/csloisel), [@Sidsector9](https://github.com/Sidsector9) via [#170](https://github.com/10up/windows-azure-storage/pull/170)).
* **Changed:** Bump minimum WordPress version from 4.0 to 5.7 (props [@csloisel](https://github.com/csloisel), [@Sidsector9](https://github.com/Sidsector9) via [#170](https://github.com/10up/windows-azure-storage/pull/170)).
* **Changed:** Bump minimum PHP version from 5.6 to 7.4 (props [@csloisel](https://github.com/csloisel), [@Sidsector9](https://github.com/Sidsector9) via [#170](https://github.com/10up/windows-azure-storage/pull/170)).
* **Changed:** Bump WordPress version "tested up to" 6.1 (props [@jayedul](https://github.com/jayedul), [@dkotter](https://github.com/dkotter) via [#172](https://github.com/10up/windows-azure-storage/issues/172)).
* **Fixed:** Address some PHP 8.1 deprecations (props [@superpowered](https://github.com/superpowered), [@faisal-alvi](https://github.com/faisal-alvi) via [#169](https://github.com/10up/windows-azure-storage/pull/169)).
* **Security:** Bump `minimatch` from 3.0.4 to 3.0.8 (props [@dependabot](https://github.com/apps/dependabot) via [#171](https://github.com/10up/windows-azure-storage/pull/171)).
* **Security:** Bump `qs` from 6.6.0 to 6.11.0 (props [@dependabot](https://github.com/apps/dependabot) via [#173](https://github.com/10up/windows-azure-storage/pull/173)).

= 4.3.3 - 2022-06-30 =
* **Added:** New [user guide](https://github.com/10up/windows-azure-storage/blob/develop/UserGuide.md) (props [@saltnpixels](https://github.com/saltnpixels) via [#139](https://github.com/10up/windows-azure-storage/issues/139)).
* **Added:** Dependency security scanning (props [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#155](https://github.com/10up/windows-azure-storage/pull/155)).
* **Added:** GitHub action to auto-create issue if WordPress latest doesn't match plugin's "tested up to" (props [@cadic](https://github.com/cadic), [@jeffpaul](https://github.com/jeffpaul) via [#160](https://github.com/10up/windows-azure-storage/pull/160)).
* **Changed:** Improve path generation for intermediary media sizes in multisite environments (props [@Clorith](https://github.com/Clorith), [@dinhtungdu](https://github.com/dinhtungdu) via [#141](https://github.com/10up/windows-azure-storage/issues/141)).
* **Changed:** Update to use `media_buttons` instead of `media_buttons_context` hook (props [@debabratakarfa](https://github.com/debabratakarfa), [@colegeissinger](https://github.com/colegeissinger) via [#147](https://github.com/10up/windows-azure-storage/issues/147)).
* **Changed:** Bump WordPress version "tested up to" 6.0 (props [@sudip-10up](https://github.com/sudip-10up), [@jeffpaul](https://github.com/jeffpaul), [@cadic](https://github.com/cadic) via [#152](https://github.com/10up/windows-azure-storage/issues/152), [#162](https://github.com/10up/windows-azure-storage/issues/162)).
* **Security:** Bump `lodash` from 4.17.19 to 4.17.21 (props [@dependabot](https://github.com/apps/dependabot) via [#137](https://github.com/10up/windows-azure-storage/pull/137)).
* **Security:** Bump `grunt` from 1.0.4 to 1.5.3 (props [@dependabot](https://github.com/apps/dependabot) via [#138](https://github.com/10up/windows-azure-storage/pull/138), [#157](https://github.com/10up/windows-azure-storage/pull/157), [#158](https://github.com/10up/windows-azure-storage/pull/158)).
* **Security:** Bump `path-parse` from 1.0.6 to 1.0.7 (props [@dependabot](https://github.com/apps/dependabot) via [#146](https://github.com/10up/windows-azure-storage/pull/146)).
* **Security:** Bump `async` from 2.6.1 to 2.6.4 (props [@dependabot](https://github.com/apps/dependabot) via [#156](https://github.com/10up/windows-azure-storage/pull/156)).

= 4.3.2 - 2021-03-03 =
* **Changed:** Bump WordPress version "tested up to" 5.6 (props [@davidegreenwald](https://profiles.wordpress.org/davidegreenwald/), [@shahaaron](https://profiles.wordpress.org/shahaaron/), [@lostfields](https://github.com/lostfields)).
* **Fixed:** Image sizes when uploading to a post that is not in current month (props [@eflorea](https://profiles.wordpress.org/eflorea/), [@mmcachran](https://profiles.wordpress.org/mmcachran/), [@shadowxvii](https://profiles.wordpress.org/shadowxvii/)).
* **Fixed:** Upload date in image metadata on back date posts (props [@brainfestation](https://profiles.wordpress.org/brainfestation/), [@rickalee](https://profiles.wordpress.org/rickalee/), [@cally423](https://github.com/cally423), [@FreuxF](https://github.com/FreuxF)).
* **Security:** Bump `websocket-extensions` from 0.1.3 to 0.1.4 (props [@dependabot](https://github.com/apps/dependabot)).
* **Security:** Bump `lodash` from 4.17.15 to 4.17.19 (props [@dependabot](https://github.com/apps/dependabot)).

= 4.3.1 - 2020-02-12 =
* **Fixed:** Restore visual indicator of upload progress to Azure Blob Storage and account for `original_image` in count (props [@rickalee](https://profiles.wordpress.org/rickalee/), [@moraleidame](https://profiles.wordpress.org/moraleidame/)).
* **Fixed:** Ensure PDF thumbnails are offloaded with JPEG mimetype instead of PDF (props [@rickalee](https://profiles.wordpress.org/rickalee/))
* **Fixed:** Normalize file paths on Windows Server (props [@nanasess](https://profiles.wordpress.org/nanasess/))

= 4.3.0 - 2019-11-18 =
* **Added:** Support for Media Library upload process change introduced in WordPress 5.3.
* **Added:** Offload `original_image` file introduced in WordPress 5.3.
* **Fixed:** Issue with special characters in filenames with url encoding.
* **Fixed:** Issue with media regeneration via WP CLI.

= 4.2.0 - 2018-05-04 =
* **Added:** Ability to setup Azure settings using constants in `wp-config.php`.
* **Fixed:** Images uploading issue in the multisite environment, now it doesn't strip `sites/{id}` from filename.
* **Fixed:** `srcset` issue for images uploaded before 4.2.0 version.
* **Fixed:** Issue with special characters in filenames with url encoding.

= 4.1.1 - 2018-01-31 =
* **Changed:** Updated authors list.
* **Removed:** Build tools from the plugin repository.

= 4.1.0 - 2017-11-22 =
* **Added:** Error message when SimpleXML library is not found.
* **Added:** Ability to enter Cache-Control property.
* **Changed:** Renamed plugin to be Microsoft Azure Storage for WordPress.
* **Fixed:** Trailing slash issue which led to double slashes in URLs.
* **Fixed:** Minor warnings.

= 4.0.3 - 2017-10-19 =
* **Added:** POT file and loaded text domain.
* **Fixed:** Uploading issue when year/month based folders are not used.
* **Fixed:** CNAME issue in the srcset attribute when yar/month based folders are not used.

= 4.0.2 - 2017-03-02 =
* **Fixed:** Bug for 0-byte uploads.

= 4.0.1 - 2017-01-03 =
* **Fixed:** Blob name while media file.
* **Fixed:** Show admin notice if can't access files directly.

= 4.0.0 - 2016-11-10 =
* **Added:** Compatibility with API version 2015-12-11.
* **Added:** Compatibility with PHP 5.3+.
* **Added:** L10N/I18N: Round 2 of preparing strings for translation.
* **Added:** Integrated Azure Blob browser into WordPress Media Library.
* **Added:** Option to keep local files after uploading them to Azure Blob.
* **Added:** Introduced filter `azure_blob_operation_timeout` which defines REST operation timeout.
* **Added:** Introduced filter `azure_blob_list_containers_max_results` which defines max size of containers listing per one request.
* **Added:** Introduced filter `azure_blob_list_blobs_max_results` which defines max size of blobs listing per one request.
* **Added:** Introduced filter `azure_blob_put_blob_headers` which defines headers used for creating new blob.
* **Added:** Introduced filter `azure_blob_append_blob_headers` which defines headers used for appending created blob.
* **Changed:** Removed old PHP SDK and use WordPress HTTP API based client library.
* **Changed:** Improved overall performance.
* **Changed:** Refactored code to match WordPress standards.
* **Changed:** Better UX by adding more feedback during long operations.
* **Changed:** Deduplicated code functionality.
* **Security:** Validate, sanitize, and escape (allthethings).

= 3.0.1 - 2016-03-01 =
* **Fixed:** Upload nonce checks.
* **Fixed:** Media: AYS checks on browse.

= 3.0.0 - 2016-02-03 =
* **Added:** L10N/I18N: Round 1 of preparing strings for translation.
* **Added:** `srcset` to images added through the Media Library when Azure is the default media handler. (Props [@patricknami](https://profiles.wordpress.org/patricknami/)).
* **Changed:** UI: Editor button more closely matches the WordPress Admin UI.
* **Security:** Fixes a bug that could allow unauthorized deletion of remotely-stored media.
* **Security:** Validate, sanitize, and escape (allthethings).
* **Security:** Use `https://` URLs by default, and warn if an insecure CNAME is configured.
* **Security:** Introduce permissions checks for specific actions within the plugin.

= 2.2.0 - 2016-02-02 =
* **Fixed:** Network activation bug in WordPress multisite.
* **Fixed:** Issue with duplicate blob names in XML-RPC.

= 2.1.0 - 2014-07-03 =
* **Fixed:** Issue with duplicate blob names.
* **Fixed:** Bug in uploading video files to blob storage.
* **Fixed:** Bug with forward slash in front of image thumbnail filenames.
* **Fixed:** Bug with year and month getting trimmed for file system images.

= 2.0.0 - 2014-07-03 =
* **Changed:** Updated to use [Microsoft Azure SDK for PHP](https://github.com/WindowsAzure/azure-sdk-for-php).
* **Fixed:** Compatibility with WordPress 3.4.1.

= 1.9.0 - 2012-01-06 =
* **Fixed:** Case sensitivity error in file names on Linux.

= 1.8.0 - 2012-01-06 =
* **Fixed:** Bug in generating blob storage URL when using Microsoft Azure Storage emulator.

= 1.7.0 - 2012-01-05 =
* **Added:** Support to upload video files to blob storage.

= 1.6.0 - 2012-01-05 =
* **Added:** support to upload video files to blob storage.

= 1.5.0 - 2012-01-04 =
* **Added:** Included Microsoft Azure SDK for PHP v4.1.0 with the plugin. Now setting mime-type for uploaded file to blob storage.

= 1.4.0 - 2011-08-26 =
* **Added:** Included Microsoft Azure SDK for PHP v4.0.2 with the plugin.

= 1.3.0 - 2011-08-16 =
* **Added:** Included Microsoft Azure SDK for PHP v4.0.1 with the plugin, so no need to install the SDK separetely.
* **Fixed:** Thumbnail handling issue while uploading files when some specific theme is enabled.

= 1.2.0 - 2011-06-03 =
* **Added:** Compatibility with Microsoft Azure SDK for PHP v3.0.0.
* **Fixed:** Issue with deleting media files when thumbnails are associated.

= 1.1.0 - 2011-03-03 =
* **Added:** Compatibility with Microsoft Azure SDK for PHP v2.1.0 and WordPress 3.1.

= 1.0.0 - 2010-05-20 =
* First release of Microsoft Azure Storage plugin for WordPress.

== Upgrade Notice ==

= 4.4.0 =
Note that this version bumps the minimum PHP version from 7.4 to 8.0.

= 4.3.4 =

Note that this version bumps the minimum WordPress version from 4.0 to 5.7 and the minimum PHP version from 5.6 to 7.4.

= 3.0.0 =
This release features several security fixes and enhancements.
It is highly recommended that all users upgrade immediately.

== Known Issues ==

= Storage Account Versions =
Storage accounts can be created via CLI, classic Azure portal, or the new Azure portal,
with varying results.

If a Storage account is created with the new Azure portal, authentication will fail,
resulting in the inability to view/add containers or files. Creating a Storage account
with the Azure CLI should allow the plugin to work with new Storage accounts.

= Responsive Images in WordPress 4.4 =
Images uploaded to the Azure Storage service will not automatically receive responsive versions.
Images added through the WordPress Media Loader *should* get automatically converted to responsive
images when inserted into a post or page.
We are investigating options for full support of responsive images in the plugin.
