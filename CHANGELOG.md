# Changelog

All notable changes to this project will be documented in this file, per [the Keep a Changelog standard](http://keepachangelog.com/).

## [Unreleased] - TBD

## [4.3.1] - 2019-12-12
### Fixed
- Restore visual indicator of upload progress to Azure Blob Storage and account for "original_image" in count

## [4.3.0] - 2019-11-18
### Added
- Support for Media Library upload process change introduced in WordPress 5.3
- Offload "original_image" file introduced in WordPress 5.3

### Fixed
- Issue with special characters in filenames with url encoding
- Issue with media regeneration via WP CLI

## [4.2.0] - 2018-05-04
### Added
- Ability to setup Azure settings using constants in wp-config.php file

### Fixed
- Images uploading issue in the multisite environment, now it doesn't strip "sites/{id}" from filename
- srcset issue for images uploaded before 4.2.0 version
- Issue with special characters in filenames with url encoding

## [4.1.1] - 2018-01-31
### Changed
- Updated authors list

### Removed
- Build tools from the plugin repository

## [4.1.0] - 2017-11-22
### Added
- Error message when SimpleXML library is not found
- Ability to enter Cache-Control property

### Changed
- Renamed plugin to be Microsoft Azure Storage for WordPress

### Fixed
- Trailing slash issue which led to double slashes in URLs
- Minor warnings

## [4.0.3] - 2017-10-19
### Added
- Added POT file and loaded text domain

### Fixed
- Uploading issue when year/month based folders are not used
- CNAME issue in the srcset attribute when yar/month based folders are not used

## [4.0.2] - 2017-03-02
### Fixed
- Bug fix for 0-byte uploads.

## [4.0.1] - 2017-01-03
### Fixed
- Blob name while media file
- Show admin notice if can't access files directly

## [4.0.0] - 2016-11-10
### Added
- Compatibility with API version 2015-12-11.
- Compatibility with PHP 5.3+.
- L10N/I18N: Round 2 of preparing strings for translation.
- Integrated Azure Blob browser into WordPress Media Library.
- Option to keep local files after uploading them to Azure Blob.
- Introduced filter `azure_blob_operation_timeout` which defines REST operation timeout.
- Introduced filter `azure_blob_list_containers_max_results` which defines max size of containers listing per one request.
- Introduced filter `azure_blob_list_blobs_max_results` which defines max size of blobs listing per one request.
- Introduced filter `azure_blob_put_blob_headers` which defines headers used for creating new blob.
- Introduced filter `azure_blob_append_blob_headers` which defines headers used for appending created blob.
  
### Changed
- Removed old PHP SDK and use WordPress HTTP API based client library.
- Improved overall performance.
- Refactored code to match WordPress standards.
- Better UX by adding more feedback during long operations.
- Deduplicated code functionality.

### Security
- Validate, sanitize, and escape (allthethings).

## [3.0.1] - 2016-03-01
### Fixed
- Upload nonce checks
- Media: fix the AYS checks on browse

## [3.0.0] - 2016-02-03
### Added
- L10N/I18N: Round 1 of preparing strings for translation.
- `srcset` to images added through the Media Library when Azure is the default media handler. (Props @patricknami).

### Changed
- UI: Editor button more closely matches the WordPress Admin UI.

### Security
- Fixes a bug that could allow unauthorized deletion of remotely-stored media.
- Validate, sanitize, and escape (allthethings).
- Use `https://` URLs by default, and warn if an insecure CNAME is configured.
- Introduce permissions checks for specific actions within the plugin.

## [2.2.0] - 2016-02-02
### Fixed
- Network activation bug in WordPress multisite.
- Issue with duplicate blob names in XML-RPC.

## [2.1.0] - 2014-07-03
### Fixed
- Issue with duplicate blob names.
- Bug in uploading video files to blob storage.
- Bug with forward slash in front of image thumbnail filenames.
- Bug with year and month getting trimmed for file system images.

## [2.0.0] - 2014-07-03
### Changed
- Updated to use [Microsoft Azure SDK for PHP](https://github.com/WindowsAzure/azure-sdk-for-php)

### Fixed
- Compatibility with WordPress 3.4.1

## [1.9] - 2012-01-06
### Fixed
- Case sensitivity error in file names on Linux

## [1.8] - 2012-01-06
### Fixed
- Bug in generating blob storage URL when using Microsoft Azure Storage emulator

## [1.7] - 2012-01-05
### Added
- Support to upload video files to blob storage

## [1.6] - 2012-01-05

## [1.5] - 2012-01-04
### Added
- Included Microsoft Azure SDK for PHP v4.1.0 with the plugin. Now setting mime-type for uploaded file to blob storage.

## [1.4] - 2011-08-26
### Added
- Included Microsoft Azure SDK for PHP v4.0.2 with the plugin.

## [1.3] - 2011-08-16
### Added
- Included Microsoft Azure SDK for PHP v4.0.1 with the plugin, so no need to install the SDK separetely. Also fixed thumbnail handling issue while uploading files when some specific theme is enabled.

## [1.2] - 2011-06-03
### Added
- Compatibility with Microsoft Azure SDK for PHP v3.0.0. It also fixes issue with deleting media files when thumbnails are associated.

## [1.1] - 2011-03-03
### Added
- Compatibility with Microsoft Azure SDK for PHP v2.1.0 and WordPress 3.1

## [1.0] - 2010-05-20
- First release of Microsoft Azure Storage plugin for WordPress

[Unreleased]: https://github.com/10up/windows-azure-storage/compare/4.3.0...master
[4.3.0]: https://github.com/10up/windows-azure-storage/compare/013bb82...4.3.0
[4.2.0]: https://github.com/10up/windows-azure-storage/compare/69fb174...013bb82
[4.1.1]: https://github.com/10up/windows-azure-storage/compare/4.1.0...69fb174
[4.1.0]: https://github.com/10up/windows-azure-storage/compare/4.0.3...4.1.0
[4.0.3]: https://github.com/10up/windows-azure-storage/releases/tag/4.0.3
[4.0.2]: https://plugins.trac.wordpress.org/changeset/1606680/windows-azure-storage
[4.0.1]: https://github.com/10up/windows-azure-storage/compare/4.0.0...4.0.1
[4.0.0]: https://github.com/10up/windows-azure-storage/compare/3.0.1...4.0.0
[3.0.1]: https://github.com/10up/windows-azure-storage/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/10up/windows-azure-storage/compare/2.2.0...3.0.0
[2.2.0]: https://github.com/10up/windows-azure-storage/compare/2.1.0...2.2.0
[2.1.0]: https://github.com/10up/windows-azure-storage/compare/2.0.0...2.1.0
[2.0.0]: https://github.com/10up/windows-azure-storage/releases/tag/2.0.0
[1.9]: https://plugins.trac.wordpress.org/changeset/485888/windows-azure-storage
[1.8]: https://plugins.trac.wordpress.org/changeset/485513/windows-azure-storage
[1.7]: https://plugins.trac.wordpress.org/changeset/484894/windows-azure-storage
[1.6]: https://plugins.trac.wordpress.org/changeset/484891/windows-azure-storage
[1.5]: https://plugins.trac.wordpress.org/changeset/484791/windows-azure-storage
[1.4]: https://plugins.trac.wordpress.org/changeset/428894/windows-azure-storage
[1.3]: https://plugins.trac.wordpress.org/changeset/424458/windows-azure-storage
[1.2]: https://plugins.trac.wordpress.org/changeset/392854/windows-azure-storage
[1.1]: https://plugins.trac.wordpress.org/changeset/354932/windows-azure-storage
[1.0]: https://plugins.trac.wordpress.org/changeset/243465/windows-azure-storage
