<?php
/**
 * deprecated.php
 *
 * Contains deprecated functions.
 *
 * Version: 4.0.0
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

/**
 * Render Windows Azure Storage Plugin Options Screen
 *
 * @param string $mode mode for logged in user (admin/nonadmin)
 *
 * @deprecated 4.0
 *
 * @return void
 */
function show_windows_azure_storage_settings( $mode ) {
	_deprecated_function( __METHOD__, '4.0' );
	$container_creation_status = true;
	$message                 = createContainerIfRequired( $container_creation_status );
	// Storage Account Settings from db if already set
	//TODO: check POST values first and use these for fallbacks
	$storage_account_name = Windows_Azure_Helper::get_account_name();
	$storage_account_key  = Windows_Azure_Helper::get_account_key();
	$http_proxy_host      = Windows_Azure_Helper::get_http_proxy_host();
	$http_proxy_port      = Windows_Azure_Helper::get_http_proxy_port();
	$http_proxy_username  = Windows_Azure_Helper::get_http_proxy_username();
	$http_proxy_password  = Windows_Azure_Helper::get_http_proxy_password();
	$default_container    = Windows_Azure_Helper::get_default_container();
	$new_container_name   = null;
	// Use the account settings in the $_POST if this page load is
	// a result of container creation operation.
	if (
		isset( $_REQUEST['_wpnonce'] ) &&
		wp_verify_nonce( $_REQUEST['_wpnonce'], 'windows-azure-storage-settings-group-options' ) &&
		isset( $_POST['action2'] ) && 'update' === $_POST['action2']
	) {
		//TODO sanitize and set from a loop instead of a bunch of if…then statements
		if ( array_key_exists( "azure_storage_account_name", $_POST ) ) {
			$storage_account_name = sanitize_text_field( $_POST["azure_storage_account_name"] );
		}

		if ( array_key_exists( "azure_storage_account_primary_access_key", $_POST ) ) {
			$storage_account_key = sanitize_text_field( $_POST["azure_storage_account_primary_access_key"] );
		}

		if ( array_key_exists( "http_proxy_host", $_POST ) ) {
			$http_proxy_host = sanitize_text_field( $_POST["http_proxy_host"] );
		}

		if ( array_key_exists( "http_proxy_port", $_POST ) ) {
			$http_proxy_port = absint( $_POST["http_proxy_port"] );
		}

		if ( array_key_exists( "http_proxy_username", $_POST ) ) {
			$http_proxy_username = sanitize_text_field( $_POST["http_proxy_username"] );
		}

		if ( array_key_exists( "http_proxy_password", $_POST ) ) {
			$http_proxy_password = sanitize_text_field( $_POST["http_proxy_password"] );
		}
	}

	// We need to show the container name if the request for
	// container creation fails.
	if ( ! $container_creation_status ) {
		$new_container_name = sanitize_text_field( $_POST["newcontainer"] );
	}

	try {
		if ( ! empty( $storage_account_name ) && ! empty( $storage_account_key ) ) {
			$containers_list         = Windows_Azure_Helper::list_containers();
			$private_container_warning = null;
			if ( ! empty( $default_container ) ) {
				$container_acl_result = Windows_Azure_Helper::get_container_acl( $default_container );

				if ( is_wp_error( $container_acl_result ) ) {

					$private_container_warning = $container_acl_result->get_error_message();

				} else if ( $container_acl_result === Windows_Azure_Rest_Api_Client::CONTAINER_VISIBILITY_PRIVATE ) {
					/* translators: %s is the container name and is used twice */
					$private_container_warning = sprintf(
						__(
							'Warning: The container "%1$s" is set to "private" and cannot be used.' .
							'Please choose a public container as the default, or set the "%1$s" container to ' .
							'"public" in your Azure Storage settings.',
							MSFT_AZURE_PLUGIN_DOMAIN_NAME
						),
						$default_container
					);
				}
			}
			if ( is_wp_error( $containers_list ) ) {
				$private_container_warning .= sprintf(
					__( 'Unable to fetch containers list. Reason: %s', MSFT_AZURE_PLUGIN_DOMAIN_NAME ),
					$containers_list->get_error_message()
				);
			}
			if ( ! is_null( $private_container_warning ) ) {
				printf( '<p style="margin: 10px; color: red;">%s</p>', esc_html( $private_container_warning ) );
			}
		}
	} catch ( Exception $ex ) {
		// Fires if account keys are not yet set
		error_log( $ex->getMessage(), E_USER_WARNING );
		printf( '<p style="margin: 10px; color: red;">%s</p>', esc_html( $ex->getMessage() ) );
	}
	?>
	<table class="form-table" border="0">
		<tr valign="top">
			<th scope="row">
				<label for="storage_account_name" title="Windows Azure Storage Account Name">Store Account Name</label>
			</th>
			<td>
				<input type="text" name="azure_storage_account_name" title="Windows Azure Storage Account Name" value="<?php
				echo esc_attr( $storage_account_name ); ?>" />
			</td>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="azure_storage_account_primary_access_key" title="Windows Azure Storage Account Primary Access Key">Primary Access Key</label>
			</th>
			<td>
				<input type="text" name="azure_storage_account_primary_access_key" title="Windows Azure Storage Account Primary Access Key" value="<?php echo esc_attr( $storage_account_key ); ?>" />
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
					if ( ! is_wp_error( $containers_list ) ) {
						foreach ( $containers_list as $container ) {
							?>
							<option value="<?php echo esc_attr( $container['Name'] ); ?>"
								<?php selected( $container['Name'], $default_container ); ?>>
								<?php echo esc_html( $container['Name'] ); ?>
							</option>
							<?php
						}
						if ( current_user_can( 'manage_options' ) ) {
							?>
							<option value="__newContainer__">&mdash;&thinsp;<?php esc_html_e( 'Create New Container', 'windows-azure-storage' ); ?>&thinsp;&mdash;</option>
							<?php
						}
					}
					?>
				</select>
			</td>
			<?php if ( current_user_can( 'manage_options' ) ) :
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
									<input type="text" name="newcontainer" title="Name of the new container to create" value="<?php echo esc_attr( $new_container_name ); ?>" />
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
				<input type="url" name="cname" title="Use CNAME instead of Windows Azure Blob URL" value="<?php echo esc_attr( \Windows_Azure_Helper::get_cname() ); ?>" />
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
				echo esc_attr( $http_proxy_host ); ?>" />
			</td>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="http_proxy_port" title="Use HTTP proxy port if web proxy server is configured">HTTP Proxy Port</label>
			</th>
			<td>
				<input type="number" name="http_proxy_port" title="Use HTTP proxy port if web proxy server is configured" value="<?php echo esc_attr( $http_proxy_port ); ?>" />
			</td>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="http_proxy_username" title="Use HTTP proxy user name if credential is required to access web proxy server">HTTP Proxy User Name</label>
			</th>
			<td>
				<input type="text" name="http_proxy_username" title="Use HTTP proxy user name if credential is required to access web proxy server" value="<?php
				echo esc_attr( $http_proxy_username ); ?>" />
			</td>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="http_proxy_password" title="Use HTTP proxy password if credential is required to access web proxy server">HTTP Proxy Password</label>
			</th>
			<td>
				<input type="text" name="http_proxy_password" title="Use HTTP proxy password if credential is required to access web proxy server" value="<?php
				echo esc_attr( $http_proxy_password ); ?>" />
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
	<?php if ( is_wp_error( $containers_list ) || ! $container_creation_status || 0 === iterator_count( $containers_list ) ) : ?>
		<script type="text/javascript">
			function onContainerSelectionChanged( show ) {
				var htmlForm = document.getElementsByName( 'SettingsForm' )[0];
				var divCreateContainer = document.getElementById( 'divCreateContainer' );
				if ( '__newContainer__' === htmlForm.elements['default_azure_storage_account_container_name'].value ) {
					divCreateContainer.style.display = 'block';
					htmlForm.elements['submitButton'].disabled = true;

				} else {
					if ( show ) {
						divCreateContainer.style.display = 'block';
					} else {
						divCreateContainer.style.display = 'none';
					}

					htmlForm.elements['submitButton'].disabled = false;
				}
			}
			onContainerSelectionChanged( true );
		</script>

	<?php endif;
}

/**
 * Try to create a container.
 *
 * @param boolean $success True if the operation succeeded, false otherwise.
 *
 * @deprecated 4.0 Use create_container_if_required()
 *
 * @return string The message to displayed
 */
function createContainerIfRequired( &$success ) {
	_deprecated_function( __METHOD__, '4.0', 'create_container_if_required()' );
	create_container_if_required( $success );
}
