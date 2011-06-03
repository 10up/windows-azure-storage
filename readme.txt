=== Windows Azure Storage for WordPress ===
Contributors: Microsoft
Tags: Microsoft, Windows Azure, Windows Azure Storage, WordPress, Media Files, Upload
Requires at least: 2.8.0
Tested up to: 3.1.3

Stable tag: 1.2

This WordPress plugin allows you to use Windows Azure Storage Service to host your media for your WordPress powered blog.

== Description ==

This WordPress plugin allows you to use Windows Azure Storage Service to host 
your media for your WordPress powered blog. Windows Azure Storage is an effective way 
to scale storage of your site without having to go through the expense of setting up the 
infrastructure for a content delivery.

Please refer UserGuide.pdf for learning more about the plugin.

For more details on Windows Azure Storage Services, please visit the 
<a href="http://www.microsoft.com/azure/windowsazure.mspx">Windows Azure Platform web-site</a>.

Related Links:
*<a href="http://wordpress.org/extend/plugins/windows-azure-storage/" title="Windows Azure Storage for WordPress">Plugin Homepage</a>*

== Installation ==
1. Download <a href="http://phpazure.codeplex.com/releases/view/66558#DownloadId=240721">Windows Azure SDK for PHP v3.0.0</a>
and extract the zip file on the server where WordPress is installed. 
e.g. Extract the zip file in "/usr/local/PHPAzureSDK/" folder.

1. Modify the php.ini file from PHP installation and add path to the PHPAzureSDK library to the include_path variable. 
If PHP Azure SDK is installed in the folder "/usr/local/PHPAzureSDK/", then add "/usr/local/PHPAzureSDK/library" path 
to the include_path variable.
include_path = ".:/php/includes:/var/www/html/phpdataservices/framework:/usr/local/PHPAzureSDK/library"

1. If user wants to install this plugin on WordPress instance hosted on Windows Azure, then no need to modify the 
php.ini file. User can edit following line within the windows-azure-storage.php file and adjust path at the end. 
If PHP SDK for Azure in installed at the WebRole root, then no need to modify this setting. Otherwise append 
appropriate folder name after "$_SERVER["APPL_PHYSICAL_PATH"]" in the following line.
get_include_path() .  PATH_SEPARATOR . $_SERVER["APPL_PHYSICAL_PATH"]

1. Extract the Windows Azure Storage Plugin windows-azure-storage.zip to the plugins directory of the WordPress installation. 
e.g. if WordPress is installed in "/var/www/html/wordpress" directory, extract the windows-azure-storage.zip file into directory "/var/www/html/wordpress/wp-content/plugins".

1. To activate the plugin, log in into the WordPress as administrator and navigate to list of plugins. Then check the associated checkbox for the plugin and click on "Activate" link.

== Changelog ==
= 1.2 =
* This release is compatible with Windows Azure SDK for PHP v3.0.0. It also fixes issue with deleting media files when thumbnails are associated.

= 1.1 =
* This release is compatible with Windows Azure SDK for PHP v2.1.0 and WordPress 3.1

= 1.0 =
* First release of Windows Azure Storage plugin for WordPress

== License ==
This code released under the terms of the New BSD License (BSD).
