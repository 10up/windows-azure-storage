# Microsoft Azure Storage for WordPress

> Use the Microsoft Azure Storage service to host your website's media files.

[![Support Level](https://img.shields.io/badge/support-stable-blue.svg)](#support-level) [![Release Version](https://img.shields.io/github/release/10up/windows-azure-storage.svg)](https://github.com/10up/windows-azure-storage/releases/latest) ![WordPress tested up to version](https://img.shields.io/badge/WordPress-v5.3%20tested-success.svg) [![BSD 2-Clause License](https://img.shields.io/github/license/10up/windows-azure-storage.svg)](https://github.com/10up/windows-azure-storage/blob/master/LICENSE)

## Description

This WordPress plugin allows you to use Microsoft Azure Storage Service to host
your media and uploads for your WordPress powered website. Microsoft Azure Storage is an effective way
to infinitely scale storage of your site and leverage Azure's global infrastructure.

For more details on Microsoft Azure Storage, please visit the <a href="https://azure.microsoft.com/en-us/services/storage/">Microsoft Azure website</a>.

## Requirements

* PHP 5.6+
* [WordPress](http://wordpress.org/) 4.0+

## Installation

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


## Upgrade Notice
### 3.0.0
This release features several security fixes and enhancements.
It is highly recommended that all users upgrade immediately.

## Support Level
**Stable:** 10up is not planning to develop any new features for this, but will still respond to bug reports and security concerns.  We welcome PRs, but any that include new features should be small and easy to integrate and should not include breaking changes.  We otherwise intend to keep this tested up to the most recent version of WordPress.

## Known Issues

### Storage Account Versions
Storage accounts can be created via CLI, classic Azure portal, or the new Azure portal, with varying results.

If a Storage account is created with the new Azure portal, authentication will fail, resulting in the inability to view/add containers or files. Creating a Storage account with the Azure CLI should allow the plugin to work with new Storage accounts.

### Responsive Images in WordPress 4.4
Images uploaded to the Azure Storage service will not automatically receive responsive versions. Images added through the WordPress Media Loader *should* get automatically converted to responsive images when inserted into a post or page. We are investigating options for full support of responsive images in the plugin.

## Changelog

A complete listing of all notable changes to Microsoft Azure Storage are documented in [CHANGELOG.md](https://github.com/10up/windows-azure-storage/blob/develop/CHANGELOG.md).

## Contributing

Please read [CODE_OF_CONDUCT.md](https://github.com/10up/windows-azure-storage/blob/develop/CODE_OF_CONDUCT.md) for details on our code of conduct, [CONTRIBUTING.md](https://github.com/10up/windows-azure-storage/blob/develop/CONTRIBUTING.md) for details on the process for submitting pull requests to us, and [CREDITS.md](https://github.com/10up/windows-azure-storage/blob/develop/CREDITS.md) for a listing of maintainers of, contributors to, and libraries used by Microsoft Azure Storage.


## Like what you see?

<p align="center">
<a href="http://10up.com/contact/"><img src="https://10updotcom-wpengine.s3.amazonaws.com/uploads/2016/10/10up-Github-Banner.png" width="850"></a>
</p>
