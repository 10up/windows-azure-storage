=== Microsoft Azure Storage for WordPress ===
Contributors:      msopentech, 10up, morganestes, stevegrunwell, lpawlik, ritteshpatel, johnwatkins0, rickalee, eflorea, phyrax, ravichandra, jeffpaul
Tags:              Microsoft Azure Storage, Media Files, Upload, CDN, blob storage
Tested up to:      6.6
Stable tag:        4.5.1
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
* MICROSOFT_AZURE_OVERRIDE_CONTAINER_PATH - Override Container name in the Image URL , can be just "/"

See Settings->Microsoft Azure for more information.

== Changelog ==

= 4.5.1 - 2024-07-17 =
* **Fixed:** Fix path issue that duplicates the container name in URL paths (props [@hugosolar](https://github.com/hugosolar), [@cally423](https://github.com/cally423), [@Besdima](https://github.com/Besdima), [@ms2oo8](https://github.com/ms2oo8), [@BCornelissen](https://github.com/BCornelissen), [@dkotter](https://github.com/dkotter) via [#246](https://github.com/10up/windows-azure-storage/pull/246)).

= 4.5.0 - 2024-07-15 =
* **Added:** Feature to replace images at the blob storage level (props [@hugosolar](https://github.com/hugosolar), [@rickalee](https://github.com/rickalee), [@dkotter](https://github.com/dkotter) via [#230](https://github.com/10up/windows-azure-storage/pull/230)).
* **Added:** Constant for `MICROSOFT_AZURE_OVERRIDE_CONTAINER_PATH` (props [@rickalee](https://github.com/rickalee), [@engrshakirali](https://github.com/engrshakirali), [@hugosolar](https://github.com/hugosolar) via [#240](https://github.com/10up/windows-azure-storage/pull/240)).
* **Changed:** Bump WordPress "tested up to" version 6.6 (props [@hugosolar](https://github.com/hugosolar), [@jeffpaul](https://github.com/jeffpaul) via [#242](https://github.com/10up/windows-azure-storage/pull/242)).
* **Changed:** Update WordPress minimum supported version to 6.4 (props [@hugosolar](https://github.com/hugosolar), [@jeffpaul](https://github.com/jeffpaul) via [#242](https://github.com/10up/windows-azure-storage/pull/242)).
* **Removed:** Image with special character which isn't needed anymore (props [@hugosolar](https://github.com/hugosolar), [@dkotter](https://github.com/dkotter), [@rickalee](https://github.com/rickalee), [@jeffpaul](https://github.com/jeffpaul) via [#234](https://github.com/10up/windows-azure-storage/pull/234)).
* **Fixed:** Issue with the use of `array_flip` and not ensuring it was an actual array (props [@hugosolar](https://github.com/hugosolar), [@rickalee](https://github.com/rickalee), [@dkotter](https://github.com/dkotter) via [#230](https://github.com/10up/windows-azure-storage/pull/230)).
* **Fixed:** `webp` compatibility when uploading original images (props [@hugosolar](https://github.com/hugosolar), [@rickalee](https://github.com/rickalee), [@ali-awwad](https://github.com/ali-awwad) via [#231](https://github.com/10up/windows-azure-storage/pull/231)).
* **Fixed:** Issue with unchecking year/month option under Settings > Media causes intermediate images not being uploaded to the container (props [@hugosolar](https://github.com/hugosolar), [@rickalee](https://github.com/rickalee), [@glowmedia](https://github.com/glowmedia) via [#232](https://github.com/10up/windows-azure-storage/pull/232)).
* **Fixed:** Media uploader title "Uploading to Azure..." stuck after image is uploaded (props [@hugosolar](https://github.com/hugosolar), [@rickalee](https://github.com/rickalee), [@eflorea](https://github.com/eflorea) via [#233](https://github.com/10up/windows-azure-storage/pull/233)).
* **Fixed:** Issue with enqueuing admin script in the footer (props [@hugosolar](https://github.com/hugosolar), [@rickalee](https://github.com/rickalee) via [#238](https://github.com/10up/windows-azure-storage/pull/238)).

= 4.4.2 - 2024-05-06 =
**Note that this release bumps the minimum WordPress version from 5.7 to 6.3.**

* **Added:** New feature to replace PDF files at the blob storage level (props [@hugosolar](https://github.com/hugosolar), [@rickalee](https://github.com/rickalee) via [#220](https://github.com/10up/windows-azure-storage/pull/220)).
* **Changed:** Bump WordPress "tested up to" version 6.5 (props [@QAharshalkadu](https://github.com/QAharshalkadu), [@jeffpaul](https://github.com/jeffpaul) via [#223](https://github.com/10up/windows-azure-storage/pull/223)).
* **Changed:** Bump WordPress minimum from 5.7 to 6.3 (props [@QAharshalkadu](https://github.com/QAharshalkadu), [@jeffpaul](https://github.com/jeffpaul) via [#223](https://github.com/10up/windows-azure-storage/pull/223)).
* **Changed:** Replaced [lee-dohm/no-response](https://github.com/lee-dohm/no-response) with [actions/stale](https://github.com/actions/stale) to help with closing no-response/stale issues (props [@jeffpaul](https://github.com/jeffpaul) via [#218](https://github.com/10up/windows-azure-storage/pull/218)).
* **Fixed:** Issue with the transient generated for displaying progress (props [@hugosolar](https://github.com/hugosolar), [@rickalee](https://github.com/rickalee) via [#220](https://github.com/10up/windows-azure-storage/pull/220)).
* **Fixed:** Ensure we send the proper content type when creating the Block Blob in the container (props [@hugosolar](https://github.com/hugosolar), [@rickalee](https://github.com/rickalee), [@MWaser](https://github.com/MWaser), [@okadots](https://github.com/okadots), [@sarahannnicholson](https://github.com/sarahannnicholson), [@nicoladj77](https://github.com/nicoladj77) via [#224](https://github.com/10up/windows-azure-storage/pull/224)).

= 4.4.1 - 2024-01-08 =
* **Added:** Support for the WordPress.org plugin preview (props [@dkotter](https://github.com/dkotter), [@jeffpaul](https://github.com/jeffpaul) via [#212](https://github.com/10up/windows-azure-storage/pull/212)).
* **Changed:** Bump WordPress version "tested up to" 6.4 (props [@QAharshalkadu](https://github.com/QAharshalkadu), [@jeffpaul](https://github.com/jeffpaul) via [#208](https://github.com/10up/windows-azure-storage/pull/208), [#209](https://github.com/10up/windows-azure-storage/pull/209)).
* **Changed:** Align our PHP minimum version checks to use new helper method (props [@radeno](https://github.com/radeno), [@ravinderk](https://github.com/ravinderk) via [#202](https://github.com/10up/windows-azure-storage/pull/202)).
* **Fixed:** Remove urlencode from srcset calculation function (props [@hugosolar](https://github.com/hugosolar), [@rickalee](https://github.com/rickalee), [@Sidsector9](https://github.com/Sidsector9) via [#211](https://github.com/10up/windows-azure-storage/pull/211)).

= 4.4.0 - 2023-10-17 =
**Note that this release bumps the minimum PHP version from 7.4 to 8.0**

* **Added:** Check for minimum required PHP version before loading the plugin (props [@bmarshall511](https://github.com/bmarshall511), [@iamdharmesh](https://github.com/iamdharmesh) via [#200](https://github.com/10up/windows-azure-storage/pull/200)).
* **Changed:** Update to the `2020-04-08` version of the Azure Blob Storage API (props [@thrijith](https://github.com/thrijith), [@colegeissinger](https://github.com/colegeissinger) via [#136](https://github.com/10up/windows-azure-storage/pull/136)).
* **Changed:** Bump minimum PHP version from 7.4 to 8.0 (props [@thrijith](https://github.com/thrijith), [@colegeissinger](https://github.com/colegeissinger) via [#136](https://github.com/10up/windows-azure-storage/pull/136)).
* **Changed:** Bump WordPress version "tested up to" 6.3 (props [@QAharshalkadu](https://github.com/QAharshalkadu), [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#192](https://github.com/10up/windows-azure-storage/pull/192), [#198](https://github.com/10up/windows-azure-storage/pull/198)).
* **Changed:** Bump `cypress` from 10.11.0 to 13.1.0, `@10up/cypress-wp-utils` from 0.1.0 to 0.2.0 and `@wordpress/env` from 5.13.0 to 8.7.0, to ensure E2E tests work on the latest version of WordPress (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9) via [#199](https://github.com/10up/windows-azure-storage/pull/199)).

[View historical changelog details here](https://github.com/10up/windows-azure-storage/blob/develop/CHANGELOG.md).

== Upgrade Notice ==

= 4.4.2 =
Note that this release bumps the minimum WordPress version from 5.7 to 6.3.

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
