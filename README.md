# Microsoft Azure Storage for WordPress

This WordPress plugin allows you to use Microsoft Azure Storage Service to host your media and uploads for your WordPress powered website. Microsoft Azure Storage is an effective way to infinitely scale storage of your site and leverage Azure's global infrastructure.

For more details on Microsoft Azure Storage, please visit the <a href="https://azure.microsoft.com/en-us/services/storage/">Microsoft Azure website</a>.

## Known Issues ##

### Storage Account Versions ###
Storage accounts can be created via CLI, classic Azure portal, or the new Azure portal, with varying results.

If a Storage account is created with the new Azure portal, authentication will fail, resulting in the inability to view/add containers or files. Creating a Storage account with the Azure CLI should allow the plugin to work with new Storage accounts.

### Responsive Images in WordPress 4.4 ###
Images uploaded to the Azure Storage service will not automatically receive responsive versions. Images added through the WordPress Media Loader *should* get automatically converted to responsive images when inserted into a post or page. We are investigating options for full support of responsive images in the plugin.

## License ##

This plugin is free software licensed under the [BSD 2-Clause](http://www.opensource.org/licenses/bsd-license.php) license.

<p align="center">
<a href="http://10up.com/contact/"><img src="https://10updotcom-wpengine.s3.amazonaws.com/uploads/2016/10/10up-Github-Banner.png" width="850"></a>
</p>

## Installation ##

1. Upload the plugin files to the `/wp-content/plugins/windows-azure-storage` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. Use the Settings->Microsoft Azure screen to configure the plugin.
