# Changelog

All notable changes to this project will be documented in this file, per [the Keep a Changelog standard](http://keepachangelog.com/).

## [Unreleased] - TBD

## [4.3.4] - 2022-01-10
**Note that this release bumps the WordPress minimum version from 4.0 to 5.7 and the PHP minimum version from 5.6 to 7.4.**

### Added
- Add automated PHPCS scanning (props [@csloisel](https://github.com/csloisel), [@Sidsector9](https://github.com/Sidsector9) via [#169](https://github.com/10up/windows-azure-storage/pull/169)).

### Changed
- Bump minimum WordPress version from 4.0 to 5.7 (props [@csloisel](https://github.com/csloisel), [@Sidsector9](https://github.com/Sidsector9) via [#169](https://github.com/10up/windows-azure-storage/pull/169)).
- Bump minimum PHP version from 5.6 to 7.4 (props [@csloisel](https://github.com/csloisel), [@Sidsector9](https://github.com/Sidsector9) via [#169](https://github.com/10up/windows-azure-storage/pull/169)).
- Bump WordPress version "tested up to" 6.1 (props [@jayedul](https://github.com/jayedul), [@dkotter](https://github.com/dkotter) via [#172](https://github.com/10up/windows-azure-storage/issues/172)).

### Fixed
- Address some PHP 8.1 deprecations (props [@superpowered](https://github.com/superpowered), [@faisal-alvi](https://github.com/faisal-alvi) via [#169](https://github.com/10up/windows-azure-storage/pull/169)).

### Security
- Bump `minimatch` from 3.0.4 to 3.0.8 (props [@dependabot](https://github.com/apps/dependabot) via [#171](https://github.com/10up/windows-azure-storage/pull/171)).
- Bump `qs` from 6.6.0 to 6.11.0 (props [@dependabot](https://github.com/apps/dependabot) via [#173](https://github.com/10up/windows-azure-storage/pull/173)).

## [4.3.3] - 2022-06-30
### Added
- New [user guide](https://github.com/10up/windows-azure-storage/blob/develop/UserGuide.md) (props [@saltnpixels](https://github.com/saltnpixels) via [#139](https://github.com/10up/windows-azure-storage/issues/139)).
- Dependency security scanning (props [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#155](https://github.com/10up/windows-azure-storage/pull/155)).
- GitHub action to auto-create issue if WordPress latest doesn't match plugin's "tested up to" (props [@cadic](https://github.com/cadic), [@jeffpaul](https://github.com/jeffpaul) via [#160](https://github.com/10up/windows-azure-storage/pull/160)).

### Changed
- Improve path generation for intermediary media sizes in multisite environments (props [@Clorith](https://github.com/Clorith), [@dinhtungdu](https://github.com/dinhtungdu) via [#141](https://github.com/10up/windows-azure-storage/issues/141)).
- Update to use `media_buttons` instead of `media_buttons_context` hook (props [@debabratakarfa](https://github.com/debabratakarfa), [@colegeissinger](https://github.com/colegeissinger) via [#147](https://github.com/10up/windows-azure-storage/issues/147)).
- Bump WordPress version "tested up to" 6.0 (props [@sudip-10up](https://github.com/sudip-10up), [@jeffpaul](https://github.com/jeffpaul), [@cadic](https://github.com/cadic) via [#152](https://github.com/10up/windows-azure-storage/issues/152), [#162](https://github.com/10up/windows-azure-storage/issues/162)).

### Security
- Bump `lodash` from 4.17.19 to 4.17.21 (props [@dependabot](https://github.com/apps/dependabot) via [#137](https://github.com/10up/windows-azure-storage/pull/137)).
- Bump `grunt` from 1.0.4 to 1.5.3 (props [@dependabot](https://github.com/apps/dependabot) via [#138](https://github.com/10up/windows-azure-storage/pull/138), [#157](https://github.com/10up/windows-azure-storage/pull/157), [#158](https://github.com/10up/windows-azure-storage/pull/158)).
- Bump `path-parse` from 1.0.6 to 1.0.7 (props [@dependabot](https://github.com/apps/dependabot) via [#146](https://github.com/10up/windows-azure-storage/pull/146)).
- Bump `async` from 2.6.1 to 2.6.4 (props [@dependabot](https://github.com/apps/dependabot) via [#156](https://github.com/10up/windows-azure-storage/pull/156)).

## [4.3.2] - 2021-03-03
### Changed
- Bump WordPress version "tested up to" 5.6 (props [@davidegreenwald](https://github.com/davidegreenwald), [@ShahAaron](https://github.com/ShahAaron), [@lostfields](https://github.com/lostfields)).

### Fixed
- Image sizes when uploading to a post that is not in current month (props [@eflorea](https://github.com/eflorea), [@mmcachran](https://github.com/mmcachran), [@ShadowXVII](https://github.com/ShadowXVII) via [#118](https://github.com/10up/windows-azure-storage/pull/118)).
- Upload date in image metadata on back date posts (props [@colegeissinger](https://github.com/colegeissinger), [@rickalee](https://github.com/rickalee), [@cally423](https://github.com/cally423), [@FreuxF](https://github.com/FreuxF) via [#131](https://github.com/10up/windows-azure-storage/pull/131)).

### Security
- Bump `websocket-extensions` from 0.1.3 to 0.1.4 (props [@dependabot](https://github.com/apps/dependabot) via [#123](https://github.com/10up/windows-azure-storage/pull/123)).
- Bump `lodash` from 4.17.15 to 4.17.19 (props [@dependabot](https://github.com/apps/dependabot) via [#124](https://github.com/10up/windows-azure-storage/pull/124)).

## [4.3.1] - 2020-02-12
### Fixed
- Restore visual indicator of upload progress to Azure Blob Storage and account for `original_image` in count (props [@rickalee](https://github.com/rickalee), [@moraleida](https://github.com/moraleida) via [#110](https://github.com/10up/windows-azure-storage/pull/110), [#109](https://github.com/10up/windows-azure-storage/pull/109)).
- Ensure PDF thumbnails are offloaded with JPEG mimetype instead of PDF (props [@rickalee](https://github.com/rickalee) via [#110](https://github.com/10up/windows-azure-storage/pull/110))
- Normalize file paths on Windows Server (props [@nanasess](https://github.com/nanasess) via [#108](https://github.com/10up/windows-azure-storage/pull/108))

## [4.3.0] - 2019-11-18
### Added
- Support for Media Library upload process change introduced in WordPress 5.3.
- Offload `original_image` file introduced in WordPress 5.3.

### Fixed
- Issue with special characters in filenames with url encoding.
- Issue with media regeneration via WP CLI.

## [4.2.0] - 2018-05-04
### Added
- Ability to setup Azure settings using constants in `wp-config.php`.

### Fixed
- Images uploading issue in the multisite environment, now it doesn't strip `sites/{id}` from filename.
- `srcset` issue for images uploaded before 4.2.0 version.
- Issue with special characters in filenames with url encoding.

## [4.1.1] - 2018-01-31
### Changed
- Updated authors list.

### Removed
- Build tools from the plugin repository.

## [4.1.0] - 2017-11-22
### Added
- Error message when SimpleXML library is not found.
- Ability to enter Cache-Control property.

### Changed
- Renamed plugin to be Microsoft Azure Storage for WordPress.

### Fixed
- Trailing slash issue which led to double slashes in URLs.
- Minor warnings.

## [4.0.3] - 2017-10-19
### Added
- Added POT file and loaded text domain.

### Fixed
- Uploading issue when year/month based folders are not used.
- CNAME issue in the srcset attribute when yar/month based folders are not used.

## [4.0.2] - 2017-03-02
### Fixed
- Bug fix for 0-byte uploads.

## [4.0.1] - 2017-01-03
### Fixed
- Blob name while media file.
- Show admin notice if can't access files directly.

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
- Upload nonce checks.
- Media: fix the AYS checks on browse.

## [3.0.0] - 2016-02-03
### Added
- L10N/I18N: Round 1 of preparing strings for translation.
- `srcset` to images added through the Media Library when Azure is the default media handler. (Props [@patrickebates](https://github.com/patrickebates)).

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
- Updated to use [Microsoft Azure SDK for PHP](https://github.com/WindowsAzure/azure-sdk-for-php).

### Fixed
- Compatibility with WordPress 3.4.1.

## [1.9.0] - 2012-01-06
### Fixed
- Case sensitivity error in file names on Linux.

## [1.8.0] - 2012-01-06
### Fixed
- Bug in generating blob storage URL when using Microsoft Azure Storage emulator.

## [1.7.0] - 2012-01-05
### Added
- Support to upload video files to blob storage.

## [1.6.0] - 2012-01-05
- Added support to upload video files to blob storage.

## [1.5.0] - 2012-01-04
### Added
- Included Microsoft Azure SDK for PHP v4.1.0 with the plugin. Now setting mime-type for uploaded file to blob storage.

## [1.4.0] - 2011-08-26
### Added
- Included Microsoft Azure SDK for PHP v4.0.2 with the plugin.

## [1.3.0] - 2011-08-16
### Added
- Included Microsoft Azure SDK for PHP v4.0.1 with the plugin, so no need to install the SDK separetely.

### Fixed
- Thumbnail handling issue while uploading files when some specific theme is enabled.

## [1.2.0] - 2011-06-03
### Added
- Compatibility with Microsoft Azure SDK for PHP v3.0.0.

### Fixed
- Issue with deleting media files when thumbnails are associated.

## [1.1.0] - 2011-03-03
### Added
- Compatibility with Microsoft Azure SDK for PHP v2.1.0 and WordPress 3.1.

## [1.0.0] - 2010-05-20
- First release of Microsoft Azure Storage plugin for WordPress.

[Unreleased]: https://github.com/10up/windows-azure-storage/compare/trunk...develop
[4.3.4]: https://github.com/10up/windows-azure-storage/compare/4.3.3...4.3.4
[4.3.3]: https://github.com/10up/windows-azure-storage/compare/4.3.2...4.3.3
[4.3.2]: https://github.com/10up/windows-azure-storage/compare/4.3.1...4.3.2
[4.3.1]: https://github.com/10up/windows-azure-storage/compare/4.3.0...4.3.1
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
[1.9.0]: https://plugins.trac.wordpress.org/changeset/485888/windows-azure-storage
[1.8.0]: https://plugins.trac.wordpress.org/changeset/485513/windows-azure-storage
[1.7.0]: https://plugins.trac.wordpress.org/changeset/484894/windows-azure-storage
[1.6.0]: https://plugins.trac.wordpress.org/changeset/484891/windows-azure-storage
[1.5.0]: https://plugins.trac.wordpress.org/changeset/484791/windows-azure-storage
[1.4.0]: https://plugins.trac.wordpress.org/changeset/428894/windows-azure-storage
[1.3.0]: https://plugins.trac.wordpress.org/changeset/424458/windows-azure-storage
[1.2.0]: https://plugins.trac.wordpress.org/changeset/392854/windows-azure-storage
[1.1.0]: https://plugins.trac.wordpress.org/changeset/354932/windows-azure-storage
[1.0.0]: https://plugins.trac.wordpress.org/changeset/243465/windows-azure-storage
