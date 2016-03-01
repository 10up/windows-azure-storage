<?php
/**
 * windows-azure-storage-settings.php
 *
 * Shows various settings for Windows Azure Storage Plugin
 *
 * Version: 3.0.1
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
function windows_azure_storage_plugin_options_page() {
	//TODO implement with the Settings API
	?>
	<div class="wrap">
		<h2>
			<img src="<?php echo esc_url( MSFT_AZURE_PLUGIN_URL . 'images/WindowsAzure.jpg' ); ?>"
			     width="32" height="32" />Windows Azure Storage for WordPress</h2>

		This WordPress plugin allows you to use Windows Azure Storage Service to
		host your media for your WordPress powered blog. Windows Azure provides
		storage in the cloud with authenticated access and triple replication to
		help keep your data safe. Applications work with data using REST conventions
		and standard HTTP operations to identify and expose data using URIs. This
		plugin allows you to easily upload, retrieve, and link to files stored on
		Windows Azure Storage service from within WordPress. <br /><br />

		For more details on Windows Azure Storage Services, please visit the
		<a href="http://www.microsoft.com/azure/windowsazure.mspx">Windows Azure
			Platform web-site</a>.<br />

		<p>This plugin uses Windows Azure SDK for PHP (<a
				href="https://github.com/WindowsAzure/azure-sdk-for-php/">https://github.com/WindowsAzure/azure-sdk-for-php/</a>).
		</p>
		<b>Plugin Web Site:</b>
		<a href="http://wordpress.org/extend/plugins/windows-azure-storage/">
			http://wordpress.org/extend/plugins/windows-azure-storage/</a><br /><br />
	</div>

	<div>
		<table>
			<tr>
				<td>
					<div id="icon-options-general" class="icon32"><br /></div>
					<h2>Windows Azure Storage Settings</h2>
					<p>If you do not have Windows Azure Storage Account, please
						<a href="http://go.microsoft.com/fwlink/?LinkID=129453">register
						</a>for Windows Azure Services.</p>
					<form method="post" name="SettingsForm" action="options.php">
						<?php
						settings_fields( 'windows-azure-storage-settings-group' );
						show_windows_azure_storage_settings( 'admin' );
						submit_button( __( 'Save Changes', 'windows-azure-storage' ), 'submit primary', 'submitButton', true );
						?>
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
function windows_azure_storage_plugin_register_settings() {
	register_setting( 'windows-azure-storage-settings-group', 'azure_storage_account_name', 'sanitize_text_field' );
	register_setting( 'windows-azure-storage-settings-group', 'azure_storage_account_primary_access_key', 'sanitize_text_field' );
	register_setting( 'windows-azure-storage-settings-group', 'default_azure_storage_account_container_name', 'sanitize_text_field' );
	register_setting( 'windows-azure-storage-settings-group', 'cname', 'esc_url_raw' );
	register_setting( 'windows-azure-storage-settings-group', 'azure_storage_use_for_default_upload', 'wp_validate_boolean' );
	register_setting( 'windows-azure-storage-settings-group', 'http_proxy_host', 'esc_url_raw' );
	register_setting( 'windows-azure-storage-settings-group', 'http_proxy_port', 'absint' );
	register_setting( 'windows-azure-storage-settings-group', 'http_proxy_username', 'sanitize_text_field' );
	register_setting( 'windows-azure-storage-settings-group', 'http_proxy_password', 'sanitize_text_field' );
	register_setting( 'windows-azure-storage-settings-group', 'azure_storage_allow_per_user_settings', 'wp_validate_boolean' );
}

/**
 * Try to create a container.
 *
 * @param boolean $success True if the operation succeeded, false otherwise.
 *
 * @return string The message to displayed
 */
function createContainerIfRequired( &$success ) {
	//TODO: remove HTML from returned message string
	//TODO: return message type ('error', 'warning', 'success') with message
	$success = true;
	if ( isset( $_POST['newcontainer'] ) &&
	     WindowsAzureStorageUtil::check_action_permissions( 'create_container' ) &&
	     check_admin_referer( 'create_container', 'create_new_container_settings' )
	) {
		if ( ! empty( $_POST["newcontainer"] ) ) {
			if ( empty( $_POST["azure_storage_account_name"] ) || empty( $_POST["azure_storage_account_primary_access_key"] ) ) {
				$success = false;

				return '<FONT COLOR="red">Please specify Storage Account Name and Primary Access Key to create container</FONT>';
			}

			try {
				$storageClient = WindowsAzureStorageUtil::getStorageClient(
					sanitize_text_field( $_POST["azure_storage_account_name"] ),
					sanitize_text_field( $_POST["azure_storage_account_primary_access_key"] ),
					sanitize_text_field( $_POST["http_proxy_host"] ),
					absint( $_POST["http_proxy_port"] ),
					sanitize_text_field( $_POST["http_proxy_username"] ),
					sanitize_text_field( $_POST["http_proxy_password"] )
				);
				WindowsAzureStorageUtil::createPublicContainer( sanitize_text_field( $_POST['newcontainer'] ), $storageClient );

				return '<FONT COLOR="green">The container \'' . sanitize_text_field( $_POST["newcontainer"] ) . '\' successfully created <br/>' .
				       'To use this container as default container, select it from the above drop down and click \'Save Changes\'</FONT>';
			} catch ( Exception $e ) {
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
function show_windows_azure_storage_settings( $mode ) {
	$containerCreationStatus = true;
	$message                 = createContainerIfRequired( $containerCreationStatus );
	// Storage Account Settings from db if already set
	//TODO: check POST values first and use these for fallbacks
	$storageAccountName = WindowsAzureStorageUtil::getAccountName();
	$storageAccountKey  = WindowsAzureStorageUtil::getAccountKey();
	$httpProxyHost      = WindowsAzureStorageUtil::getHttpProxyHost();
	$httpProxyPort      = WindowsAzureStorageUtil::getHttpProxyPort();
	$httpProxyUserName  = WindowsAzureStorageUtil::getHttpProxyUserName();
	$httpProxyPassword  = WindowsAzureStorageUtil::getHttpProxyPassword();
	$defaultContainer   = WindowsAzureStorageUtil::getDefaultContainer();
	$newContainerName   = null;
	// Use the account settings in the $_POST if this page load is
	// a result of container creation operation.
	if (
		wp_verify_nonce( $_REQUEST['_wpnonce'], 'windows-azure-storage-settings-group-options' ) &&
		isset( $_POST['action2'] ) && 'update' === $_POST['action2']
	) {
		//TODO sanitize and set from a loop instead of a bunch of if…then statements
		if ( array_key_exists( "azure_storage_account_name", $_POST ) ) {
			$storageAccountName = sanitize_text_field( $_POST["azure_storage_account_name"] );
		}

		if ( array_key_exists( "azure_storage_account_primary_access_key", $_POST ) ) {
			$storageAccountKey = sanitize_text_field( $_POST["azure_storage_account_primary_access_key"] );
		}

		if ( array_key_exists( "http_proxy_host", $_POST ) ) {
			$httpProxyHost = sanitize_text_field( $_POST["http_proxy_host"] );
		}

		if ( array_key_exists( "http_proxy_port", $_POST ) ) {
			$httpProxyPort = absint( $_POST["http_proxy_port"] );
		}

		if ( array_key_exists( "http_proxy_username", $_POST ) ) {
			$httpProxyUserName = sanitize_text_field( $_POST["http_proxy_username"] );
		}

		if ( array_key_exists( "http_proxy_password", $_POST ) ) {
			$httpProxyPassword = sanitize_text_field( $_POST["http_proxy_password"] );
		}
	}

	// We need to show the container name if the request for
	// container creation fails.
	if ( ! $containerCreationStatus ) {
		$newContainerName = sanitize_text_field( $_POST["newcontainer"] );
	}

	$ContainerResult = null;
	try {
		if ( ! empty( $storageAccountName ) && ! empty( $storageAccountKey ) ) {
			//TODO: store the connection string and use it instead of always generating the client connection this way
			$storageClient           = WindowsAzureStorageUtil::getStorageClient(
				$storageAccountName,
				$storageAccountKey,
				$httpProxyHost,
				$httpProxyPort,
				$httpProxyUserName,
				$httpProxyPassword
			);
			$ContainerResult         = $storageClient->listContainers();
			$privateContainerWarning = null;
			if ( ! empty( $defaultContainer ) ) {
				$getContainerAclResult = $storageClient->getContainerAcl( $defaultContainer );
				$containerAcl          = $getContainerAclResult->getContainerAcl();
				if ( $containerAcl->getPublicAccess() === PublicAccessType::NONE ) {
					/* translators: %s is the container name and is used twice */
					$privateContainerWarning = sprintf(
						__(
							'Warning: The container "%1$s" is set to "private" and cannot be used.' .
							'Please choose a public container as the default, or set the "%1$s" container to ' .
							'"public" in your Azure Storage settings.',
							'windows-azure-storage'
						),
						$defaultContainer
					);
				}
			}
			if ( ! is_null( $privateContainerWarning ) ) {
				printf( '<p style="margin: 10px; color: red;">%s</p>', esc_html( $privateContainerWarning ) );
			}
		}
	} catch ( Exception $ex ) {
		// Fires if account keys are not yet set
		error_log( $ex->getMessage(), E_USER_WARNING );
	}
	?>
	<table class="form-table" border="0">
		<tr valign="top">
			<th scope="row">
				<label for="storage_account_name" title="Windows Azure Storage Account Name">Store Account Name</label>
			</th>
			<td>
				<input type="text" name="azure_storage_account_name" title="Windows Azure Storage Account Name" value="<?php
				echo esc_attr( $storageAccountName ); ?>" />
			</td>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="azure_storage_account_primary_access_key" title="Windows Azure Storage Account Primary Access Key">Primary Access Key</label>
			</th>
			<td>
				<input type="text" name="azure_storage_account_primary_access_key" title="Windows Azure Storage Account Primary Access Key" value="<?php echo esc_attr( $storageAccountKey ); ?>" />
			</td>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="storage_container_name" title="Default container to be used for storing media files">Default Storage Container</label>
			</th>
			<td WIDTH="80px">
				<select name="default_azure_storage_account_container_name" title="Default container to be used for storing media files" onChange="<?php echo esc_js( 'onContainerSelectionChanged( false );' ); ?>">
					<?php
					if ( ! empty( $ContainerResult ) && ( count( $ContainerResult->getContainers() ) > 0 ) ) {
						foreach ( $ContainerResult->getContainers() as $container ) {
							?>
							<option value="<?php echo esc_attr( $container->getName() ); ?>"
								<?php selected( $container->getName(), $defaultContainer ); ?>>
								<?php echo esc_html( $container->getName() ); ?>
							</option>
							<?php
						}
						if ( WindowsAzureStorageUtil::check_action_permissions( 'create_container' ) ) {
							?>
							<option value="__newContainer__">&mdash;&thinsp;<?php esc_html_e( 'Create New Container', 'windows-azure-storage' ); ?>&thinsp;&mdash;</option>
							<?php
						}
					}
					?>
				</select>
			</td>
			<?php if ( WindowsAzureStorageUtil::check_action_permissions( 'create_container' ) ) :
				wp_nonce_field( 'create_container', 'create_new_container_settings' );
				?>
				<td>
					<div id="divCreateContainer" name="divCreateContainer" style="display:none;">
						<table style="border:1px solid black;">
							<tr>
								<td>
									<label for="newcontainer" title="Name of the new container to create">Create New Container: </label>
								</td>
								<td>
									<input type="text" name="newcontainer" title="Name of the new container to create" value="<?php echo esc_attr( $newContainerName ); ?>" />
									<input type="button" class="button-primary" value="<?php esc_attr_e( 'Create', 'windows-azure-storage' ); ?>" onclick="<?php echo esc_js( sprintf( 'createContainer("%s");', esc_url( $_SERVER['REQUEST_URI'] ) ) ); ?>" />
								</td>
							</tr>
						</table>
					</div>
				</td>
			<?php endif; ?>
		</tr>
		<tr valign="top">
			<td colspan="3" WIDTH="300" align="center"><?php echo wp_kses_post( $message ); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="cname" title="Use CNAME instead of Windows Azure Blob URL">CNAME</label>
			</th>
			<td colspan="2">
				<input type="url" name="cname" title="Use CNAME instead of Windows Azure Blob URL" value="<?php echo esc_attr( WindowsAzureStorageUtil::getCNAME() ); ?>" />
				<p class="field-description">
					<?php
					$notice = __(
						'Note: Use this option if you would like to display image URLs belonging to your domain like <samp>http://MyDomain.com/</samp> instead of <samp>http://YourAccountName.blob.core.windows.net/</samp>.',
						'windows-azure-storage'
					);
					echo wp_kses( $notice, array( 'samp' => array() ) );
					?></p>
				<div id="cname-notice">
					<?php if ( is_ssl() ) : ?>
						<h4><?php echo esc_html_x( 'Notice', 'verb', 'windows-azure-storage' ); ?></h4>
						<p><?php
							//TODO: add a different notice if 'https' is set, regardless of is_ssl.
							$notice = sprintf(
							/* translators: 1: link URL should not be translated, 2: link title is safe for translation  */
								__(
									'Windows Azure Storage <a href="%1$s" title="%2$s">does not currently support ' .
									'SSL certificates for custom domain names</a>. ' .
									'Since this WordPress site is configured to serve content over HTTPS, ' .
									'it\'s recommended that you use the default Azure storage endpoint to avoid ' .
									'mixed-content warnings for your visitors.',
									'windows-azure-storage'
								),
								esc_url( 'https://feedback.azure.com/forums/217298-storage/suggestions/3007732-make-it-possible-to-use-ssl-on-blob-storage-using' ),
								esc_html__( 'How can we improve Azure Storage? on Azure Forums', 'windows-azure-storage' )
							);
							echo wp_kses( $notice, array(
								'a' => array(
									'href'  => array(),
									'title' => array(),
								),
							) );
							?></p>
					<?php else : ?>
						<p><?php
							$notice = sprintf(
							/* translators: the abbreviation "DNS" should remain, but the title can be translated */
								__(
									'This CNAME must start with <samp>http://</samp> and the administrator will have to update <abbr title="%s">DNS</abbr>
 entries accordingly.',
									'windows-azure-storage'
								),
								_x( 'Domain Name System', 'The proper name of the Internet name resolution system',
									'windows-azure-storage' )
							);
							echo wp_kses( $notice, array(
								'samp' => array(),
								'abbr' => array(
									'title' => array(),
								),
							) );
							?></p>
					<?php endif; ?>
				</div>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="http_proxy_host" title="Use HTTP proxy server host name if web proxy server is configured">HTTP Proxy Host Name</label>
			</th>
			<td>
				<input type="text" name="http_proxy_host" title="Use HTTP proxy server host name if web proxy server is configured" value="<?php
				echo esc_attr( $httpProxyHost ); ?>" />
			</td>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="http_proxy_port" title="Use HTTP proxy port if web proxy server is configured">HTTP Proxy Port</label>
			</th>
			<td>
				<input type="number" name="http_proxy_port" title="Use HTTP proxy port if web proxy server is configured" value="<?php echo esc_attr( $httpProxyPort ); ?>" />
			</td>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="http_proxy_username" title="Use HTTP proxy user name if credential is required to access web proxy server">HTTP Proxy User Name</label>
			</th>
			<td>
				<input type="text" name="http_proxy_username" title="Use HTTP proxy user name if credential is required to access web proxy server" value="<?php
				echo esc_attr( $httpProxyUserName ); ?>" />
			</td>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="http_proxy_password" title="Use HTTP proxy password if credential is required to access web proxy server">HTTP Proxy Password</label>
			</th>
			<td>
				<input type="text" name="http_proxy_password" title="Use HTTP proxy password if credential is required to access web proxy server" value="<?php
				echo esc_attr( $httpProxyPassword ); ?>" />
			</td>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="azure_storage_use_for_default_upload" title="Use Windows Azure Storage for default upload">Use Windows Azure Storage for default upload</label>
			</th>
			<td colspan="2">
				<input type="checkbox" name="azure_storage_use_for_default_upload" title="Use Windows Azure Storage for default upload" value="1" id="azure_storage_use_for_default_upload"
					<?php checked( (bool) get_option( 'azure_storage_use_for_default_upload' ) ); ?> />
				<label for="wp-uploads"> Use Windows Azure Storage when uploading via WordPress' upload tab.</label>
				<br />
				<small>Note: Uncheck this to revert back to using your own web host for storage at anytime.</small>
			</td>
		</tr>
	</table>
	<?php
	if ( empty( $ContainerResult ) || ! $containerCreationStatus || 0 === count( $ContainerResult->getContainers() ) ) {
		// 1. If $containerResult object is null means the storage account is not yet set
		// show the create container div
		?>
		<script type="text/javascript">
			onContainerSelectionChanged( true );
		</script>

		<?php
	}
}
