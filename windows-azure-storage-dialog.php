<?php
/**
 * windows-azure-storage-dialog.php
 *
 * Shows popup dialog when clicked on the Windows Azure Toolbar
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


/**
 * Enqueue JavaScript and CSS needed by the settings page dialog.
 *
 * @internal Callback for 'admin_enqueue_scripts'.
 * @since    3.0.0 Moved to a callback for 'admin_enqueue_scripts' instead of 'admin_print_scripts'.
 *
 * @param string $hook_suffix The hook of the current admin page.
 */
function windows_azure_storage_dialog_scripts( $hook_suffix ) {
	// TODO split into 'settings' and 'editor' and enqueue separately
	wp_enqueue_script( 'windows-azure-storage', MSFT_AZURE_PLUGIN_URL . 'js/windows-azure-storage.js', array(), MSFT_AZURE_PLUGIN_VERSION );
	wp_localize_script( 'windows-azure-storage', 'windowsAzureStorageSettings', array(
		'l10n' => array(
			'upload' => _x( 'Upload', 'verb', 'windows-azure-storage' ),
			'create' => _x( 'Create', 'verb', 'windows-azure-storage' ),
		),
	) );

	wp_enqueue_script( 'windows-azure-storage', MSFT_AZURE_PLUGIN_URL . 'js/windows-azure-storage.js', array(), MSFT_AZURE_PLUGIN_VERSION );

	wp_enqueue_style( 'windows-azure-storage-style', MSFT_AZURE_PLUGIN_URL . 'css/windows-azure-storage.css', array(), MSFT_AZURE_PLUGIN_VERSION );
}

add_action( 'admin_enqueue_scripts', 'windows_azure_storage_dialog_scripts' );

/**
 * Add Azure Storage tabs to the legacy media loader.
 *
 * @since    1.0.0
 * @since    3.0.0 Updated with callback parameters.
 * @internal Callback for 'media_upload_tabs' filter.
 *
 * @param array $tabs The default legacy media uploader tabs.
 * @return array $tabs The filtered array with only our tabs.
 */
function windows_azure_storage_dialog_add_tab( $tabs ) {
	return array(
		'browse' => __( 'Browse', 'windows-azure-storage' ),
		'search' => __( 'Search', 'windows-azure-storage' ),
		'upload' => __( 'Upload', 'windows-azure-storage' ),
	);
}

/**
 * Render Browse Tab in the Windows Azure Storage popup dialog
 *
 * @since 3.0.0 Add nonce checks and reformat HTML.
 * @since 3.0.1 Fix nonce checks on initial browse.
 *
 * @return void
 */
function windows_azure_storage_dialog_browse_tab() {
	// remove all registerd filters for the tabs
	//TODO: switch to remove_all_filters('media_upload_tabs') and only call it once
	unset( $GLOBALS['wp_filter']['media_upload_tabs'] );

	// register our filter for the tabs
	add_filter( "media_upload_tabs", "windows_azure_storage_dialog_add_tab" );

	media_upload_header();

	/**
	 * The post ID of the originating editor page.
	 *
	 * Passed via $_GET from the post being edited when the iframe is loaded.
	 * If iframe is accessed outside an originating editor, this will be 0 and
	 * nonces will fail. :)
	 *
	 * @var int $post_id
	 */
	$post_id = isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0;

	$azure_storage_account_name                   = WindowsAzureStorageUtil::getAccountName();
	$azure_storage_account_primary_access_key     = WindowsAzureStorageUtil::getAccountKey();
	$default_azure_storage_account_container_name = WindowsAzureStorageUtil::getDefaultContainer();

	if ( empty( $azure_storage_account_name ) || empty( $azure_storage_account_primary_access_key ) ) {
		echo '<h3 style="margin: 10px;">Azure Storage Account not yet configured</h3>';
		echo '<p style="margin: 10px;">Please configure the account in Windows Azure Settings Tab.</p>';
	} else {
		$storageClient = WindowsAzureStorageUtil::getStorageClient();
		// Set selected container. If none, then use default container
		$selected_container_name = $default_azure_storage_account_container_name;
		//TODO: can we just check $_REQUEST here?
		if (
			! empty( $_POST['selected_container'] ) &&
			check_admin_referer( 'browse_select_container_' . $post_id, 'browse_select_container_nonce' )
		) {
			$selected_container_name = sanitize_text_field( $_POST['selected_container'] );
		} elseif (
			! empty( $_GET['selected_container'] ) &&
			check_admin_referer( 'browse_select_container_' . $post_id, 'browse_select_container_nonce' )
		) {
			$selected_container_name = sanitize_text_field( $_GET['selected_container'] );
		}

		// Check if blob has to be deleted
		if ( isset( $_GET['delete_blob'] ) && check_admin_referer( 'delete_blob_' . $post_id, 'delete_blob' ) ) {
			if ( ! WindowsAzureStorageUtil::check_action_permissions( 'delete_blob' ) ) {
				//TODO: extract notices into a function that can be customized
				?>
				<div class="notice notice-error is-dismissible" role="banner">
					<p role="alert">
						<?php
						/* translators: %s is the file name */
						printf(
							esc_html__( 'Sorry, "%s" could not be deleted.', 'windows-azure-storage' ),
							esc_html( sanitize_text_field( $_GET['filename'] ) )
						);
						?>
					</p>
					<p role="status">
						<?php esc_html_e( 'You do not have permission to delete files from this container.', 'windows-azure-storage' ); ?>
					</p>
				</div>
				<?php
			} else {
				deleteBlob( $selected_container_name, sanitize_text_field( $_GET['filename'] ) );
			}
		} // delete_blob

		// Check if all blobs are to be deleted
		if (
			isset ( $_POST['delete_all_blobs'] ) &&
			check_admin_referer( 'delete_all_blobs_' . $post_id, 'delete_all_blobs_nonce' )
		) {
			if ( ! WindowsAzureStorageUtil::check_action_permissions( 'delete_all_blobs' ) ) {
				?>
				<div class="notice notice-error is-dismissible" role="banner">
					<p role="alert">
						<?php esc_html_e( 'Sorry, these files could not be deleted.', 'windows-azure-storage' ); ?>
					</p>
					<p role="status">
						<?php esc_html_e( 'You do not have permission to delete files from this container.', 'windows-azure-storage' ); ?>
					</p>
				</div>
				<?php
			} else {
				// Get list of blobs in specified container
				$listBlobResult = $storageClient->listBlobs( $selected_container_name );
				// Delete each blob in specified container
				foreach ( $listBlobResult->getBlobs() as $blob ) {
					deleteBlob( $selected_container_name, $blob->getName() );
				}

				echo '<p style="margin: 10px; color: red;">'
				     . 'Deleted all files in Windows Azure Storage Container "'
				     . esc_html( $selected_container_name ) . '"</p><br/>';
			}
		}

		// Handle file search
		if ( isset( $_POST['action'] ) && 'search_azure_storage' === $_POST['action'] ) {
			try {
				if ( false === check_admin_referer( 'search_' . $post_id, 'search_nonce' ) ) {
					throw new Exception( __(
						'The security check failed. Please try again, or contact the site administrator for assistance.',
						'windows-azure-storage'
					) );
				}

				$fileTagFilter   = sanitize_text_field( $_POST["searchFileTag"] );
				$fileNameFilter  = sanitize_text_field( $_POST["searchFileName"] );
				$fileTypeFilter  = sanitize_text_field( $_POST["searchFileType"] );
				$searchContainer = sanitize_text_field( $_POST["searchContainer"] );

				if ( empty( $fileTagFilter ) &&
				     empty( $fileNameFilter ) &&
				     empty( $fileTypeFilter )
				) {
					echo '<p style="margin: 10px;">Search criteria not specified.</p><br/>';
				} else {
					$criteria = array();
					if ( ! empty( $fileNameFilter ) ) {
						$criteria[] = "file name like " . $fileNameFilter;
					}
					if ( ! empty( $fileTypeFilter ) ) {
						$criteria[] = "file type like " . $fileTypeFilter;
					}
					if ( ! empty( $fileTagFilter ) ) {
						$criteria[] = "tag like '" . $fileTagFilter . "'";
					}

					$searchResult = array();
					if ( 'ALL_CONTAINERS' === $searchContainer ) {
						$criteria[]          = "in 'all containers'";
						$listContainerResult = $storageClient->listContainers();
						foreach ( $listContainerResult->getContainers() as $container ) {
							// Get list of blobs in specified container
							$listBlobResult = $storageClient->listBlobs( $container->getName() );
							foreach ( $listBlobResult->getBlobs() as $blob ) {
								if ( ! empty( $fileNameFilter ) ) {
									if ( stripos( $blob->getName(), $fileNameFilter ) === false ) {
										continue;
									}
								}

								// TODO This is a temporary fix (replacing space with %20) will be removed once fixed in the core
								$blobName              = str_replace( " ", "%20", $blob->getName() );
								$getBlobMetadataResult = $storageClient->getBlobMetadata( $container->getName(), $blobName );
								$metadata              = $getBlobMetadataResult->getMetadata();

								if ( ! empty( $fileTypeFilter ) ) {
									if ( stripos( $metadata["mimetype"], $fileTypeFilter ) === false ) {
										continue;
									}
								}
								if ( ! empty( $fileTagFilter ) ) {
									if ( stripos( $metadata["tag"], $fileTagFilter ) === false ) {
										continue;
									}
								}

								$searchResult[] = sprintf( '%1$s/%2$s/%3$s',
									untrailingslashit( WindowsAzureStorageUtil::get_storage_url_base( false ) ),
									$container->getName(),
									$blob->getName()
								);
							}
						}
					} else {
						$criteria[] = "in container '" . $searchContainer . "'";

						// Get list of blobs in specified container
						$listBlobResult = $storageClient->listBlobs( $searchContainer );
						foreach ( $listBlobResult->getBlobs() as $blob ) {
							if ( ! empty( $fileNameFilter ) ) {
								if ( stripos( $blob->getName(), $fileNameFilter ) === false ) {
									continue;
								}
							}

							// TODO This is a temporary fix (replacing space with %20) will be removed once fixed in the core
							$blobName              = str_replace( " ", "%20", $blob->getName() );
							$getBlobMetadataResult = $storageClient->getBlobMetadata( $searchContainer, $blobName );
							$metadata              = $getBlobMetadataResult->getMetadata();
							if ( ! empty( $fileTypeFilter ) ) {
								if ( stripos( $metadata["mimetype"], $fileTypeFilter ) === false ) {
									continue;
								}
							}

							if ( ! empty( $fileTagFilter ) ) {
								if ( stripos( $metadata["tag"], $fileTagFilter ) === false ) {
									continue;
								}
							}

							$searchResult[] = sprintf( '%1$s/%2$s/%3$s',
								untrailingslashit( WindowsAzureStorageUtil::get_storage_url_base( false ) ),
								$searchContainer,
								$blob->getName() );
						}
					}

					echo '<h3 style="margin: 10px;">Search Result</h3>';

					if ( empty( $searchResult ) ) {
						echo '<p style="margin: 10px;">No file found matching specified criteria (' . implode( ', ', esc_html( $criteria ) ) . ')</p><br/>';
					} else {
						echo '<p style="margin: 10px;">Found ' . esc_html( count( $searchResult ) ) . ' file(s) matching specified criteria (' . implode( ', ', esc_html( $criteria ) ) . ')</p><br/>';
						foreach ( $searchResult as $url ) {
							//TODO: remove inline JS and CSS
							$style          = 'margin: 10px;';
							$onmouseover_js = 'this.height=50; this.width=50; this.style.border="3px solid yellow";';
							$onmouseout_js  = 'this.height=32; this.width=32; this.style.border="0 solid black";';
							$onclick_js     = 'return insertImageTag("%s");';
							printf(
								'<img src="%1$s" style="%2$s" width="32" height="32" onmouseover="%3$s" onmouseout="%4$s" onclick="%5$s" />',
								esc_url( $url ),
								esc_attr( $style ),
								esc_js( $onmouseover_js ),
								esc_js( $onmouseout_js ),
								esc_js( sprintf( $onclick_js, esc_url( $url ) ) )
							);
						}
					}
					echo "<hr/>";

					return;
				}
			} catch ( Exception $e ) {
				?>
				<div class="notice notice-error" role="banner">
					<p role="alert">
						<?php
						esc_html_e( 'Sorry, but there was a problem searching the container.', 'windows-azure-storage' );
						?>
					</p>
					<p role="status">
						<?php echo esc_html( $e->getMessage() ); ?>
					</p>
				</div>
				<?php
				// If this exception is thrown, don't continue loading media.
				return;
			}
		}
		$first_container_name = "";
		$form_action_url      = add_query_arg(
			array(
				'post_id' => $post_id,
				'tab'     => 'browse',
			),
			MSFT_AZURE_PLUGIN_LEGACY_MEDIA_URL
		);
		?>
		<form name="SelectContainerForm" style="margin: 10px;" method="post" action="<?php echo esc_url( $form_action_url ); ?>">
			<?php wp_nonce_field( 'browse_select_container_' . $post_id, 'browse_select_container_nonce' ); ?>
			<table>
				<tr>
					<th>
						<label for="selected_container">
							<?php esc_html_e( 'Container Name:', 'windows-azure-storage' ); ?>
						</label>
					</th>
					<td>
						<select name="selected_container" id="selected_container"
						        title="<?php esc_attr_e( 'Container to use for storing media files.', 'windows-azure-storage' ); ?>"
						        onchange="<?php echo esc_js( 'this.form.setAttribute( "aria-busy", true ); this.form.submit(); this.disabled = true;' ); ?>">
							<?php
							try {
								$storageClient       = WindowsAzureStorageUtil::getStorageClient();
								$listContainerResult = $storageClient->listContainers();
								foreach ( $listContainerResult->getContainers() as $container ) {
									if ( empty( $first_container_name ) ) {
										$first_container_name = $container->getName();
									}
									?>
									<option value="<?php echo esc_attr( $container->getName() ); ?>"
										<?php selected( $container->getName(), $selected_container_name, true ); ?>>
										<?php echo esc_html( $container->getName() ); ?>
									</option>
									<?php
								}
							} catch ( Exception $ex ) {
								// Fires if account keys are not yet set
								error_log( $ex->getMessage(), E_USER_WARNING );
							}
							?>
						</select>
					</td>
				</tr>
			</table>
		</form>

		<table id="media-items">
			<tr>
				<td>
					<?php
					try {
						if ( empty( $selected_container_name ) ) {
							echo '<p style="margin: 10px; color: red;">Default Azure Storage Container name is not yet configured. Please configure it in the Windows Azure Settings Tab.</p>';
							$selected_container_name = $first_container_name;
						}

						// Get list of blobs in specified container
						$listBlobResult = $storageClient->listBlobs( $selected_container_name );
						$blobs          = $listBlobResult->getBlobs();
						if ( empty( $blobs ) ) { ?>
							<p style="margin: 10px;"><?php
								printf(
									esc_html__( 'No items in container "%s".', 'windows-azure-storage' ),
									esc_html( $selected_container_name )
								); ?>
							</p>
							<?php
						} else {
							echo '<p style="margin: 10px;">Note: Click on the image to insert image URL into the blog!</p><br/>';
							/** @var WindowsAzure\Blob\Models\Blob $blob */
							foreach ( $blobs as $blob ) {
								$url = sprintf( '%1$s/%2$s/%3$s',
									untrailingslashit( WindowsAzureStorageUtil::get_storage_url_base( false ) ),
									$selected_container_name,
									$blob->getName()
								);
								// TODO switch to wp_check_filetype_and_ext()
								$fileExt = substr( strrchr( $blob->getName(), '.' ), 1 );

								//TODO: remove inline JS and CSS
								$style          = 'margin: 10px;';
								$onmouseover_js = 'this.height=50; this.width=50; this.style.border="3px solid yellow";';
								$onmouseout_js  = 'this.height=32; this.width=32; this.style.border="0 solid black";';
								$onclick_js     = 'return insertImageTag( "%s", false );';
								switch ( strtolower( $fileExt ) ) {
									case "jpg":
									case "jpeg":
									case "gif":
									case "bmp":
									case "png":
									case "tiff":
										printf(
											'<img src="%1$s" style="%2$s" width="32" height="32" onmouseover="%3$s" onmouseout="%4$s" onclick="%5$s" />',
											esc_url( $url ),
											esc_attr( $style ),
											esc_js( $onmouseover_js ),
											esc_js( $onmouseout_js ),
											esc_js( sprintf( $onclick_js, esc_url( $url ) ) )
										);
										break;
									default:
										printf( '<a href="%1$s" style="%2$s" onclick="%3$s">%4$s</a>',
											esc_url( $url ),
											esc_attr( $style ),
											esc_js( sprintf( $onclick_js, esc_url( $url ) ) ),
											esc_html( $blob->getName() )
										);
										break;
								}
								if ( WindowsAzureStorageUtil::check_action_permissions( 'delete_blob' ) ) {
									// Generate an absolute URL used for deleting files
									$delete_blob_url = add_query_arg( array(
										'post_id'            => $post_id,
										'tab'                => 'browse', // default tab
										'filename'           => $blob->getName(),
										'selected_container' => $selected_container_name,
									), MSFT_AZURE_PLUGIN_LEGACY_MEDIA_URL );
									$delete_blob_url = wp_nonce_url( $delete_blob_url, 'delete_blob_' . $post_id, 'delete_blob' );
									/* translators: 1: URL, 2: link description, 3: link text */
									printf(
										'<a class="delete-permanently" href="%1$s" role="button" title="%2$s" aria-label="%2$s">%3$s</a>',
										esc_url( $delete_blob_url ),
										/* translators: %s is the blob/file name */
										sprintf(
											esc_attr__(
												'Delete "%s" from this container.', 'windows-azure-storage'
											),
											esc_html( $blob->getName() )
										),
										'x' // TODO maybe make this customizable via L10N?
									);
								}
							}
						}
					} catch ( Exception $e ) {
						echo '<p style="margin: 10px; color: red;">Error in listing storage containers: ' . esc_html( $e->getMessage() ) . "</p><br/>";
					}
					?>
				</td>
			</tr>
		</table>

		<?php
		// TODO: add an AYS check before submitting this form.
		if (
			! empty( $blobs ) &&
			WindowsAzureStorageUtil::check_action_permissions( 'delete_all_blobs' )
		) :
			$form_action_url = add_query_arg( array(
				'post_id' => $post_id,
				'tab'     => 'browse',
			), MSFT_AZURE_PLUGIN_LEGACY_MEDIA_URL );
			?>
			<form name="DeleteAllBlobsForm" method="POST" action="<?php echo esc_url( $form_action_url ); ?>">
				<?php wp_nonce_field( 'delete_all_blobs_' . $post_id, 'delete_all_blobs_nonce' ); ?>
				<input type='hidden' name='selected_container' value='<?php echo esc_attr( $selected_container_name ); ?>' />
				<?php
				submit_button(
					__( 'Delete All Files', 'windows-azure-storage' ),
					'delete',
					'delete_all_blobs',
					true,
					array(
						'aria-label' => __( 'Delete all blobs from this container.', 'windows-azure-storage' ),
						'id'         => 'was-delete-all-blobs',
						'role'       => 'button',
					)
				);
				?>
			</form>
		<?php endif; ?>
		<?php
	}
}

/**
 * Render Search Tab in the Windows Azure Storage popup dialog
 *
 * @return void
 */
function windows_azure_storage_dialog_search_tab() {
	// remove all registerd filters for the tabs
	unset( $GLOBALS['wp_filter']['media_upload_tabs'] );

	// register our filter for the tabs
	add_filter( "media_upload_tabs", "windows_azure_storage_dialog_add_tab" );

	media_upload_header();

	/**
	 * The post ID of the originating editor page.
	 *
	 * Passed via $_GET from the post being edited when the iframe is loaded.
	 * If iframe is accessed outside an originating editor, this will be 0 and
	 * nonces will fail. :)
	 *
	 * @var int $post_id
	 */
	$post_id = isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0;

	$azure_storage_account_name                   = WindowsAzureStorageUtil::getAccountName();
	$azure_storage_account_primary_access_key     = WindowsAzureStorageUtil::getAccountKey();
	$default_azure_storage_account_container_name = WindowsAzureStorageUtil::getDefaultContainer();

	if ( empty( $azure_storage_account_name ) || empty( $azure_storage_account_primary_access_key ) ) {
		echo '<h3 style="margin: 10px;">Azure Storage Account not yet configured</h3>';
		echo '<p style="margin: 10px;">Please configure the account in Windows Azure Settings Tab.</p>';
	} else {
		$form_action_url = add_query_arg( array(
			'post_id' => $post_id,
			'tab'     => 'browse',
		), MSFT_AZURE_PLUGIN_LEGACY_MEDIA_URL );
		?>
		<h3 style="margin: 10px;">Search Files</h3>
		<div id="search-form">
			<form role="search" method="POST" action="<?php echo esc_url( $form_action_url ); ?>">
				<?php wp_nonce_field( 'search_' . $post_id, 'search_nonce' ); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="searchFileTag">Tag:</label>
						</th>
						<td>
							<input type="text" name="searchFileTag" value="" />
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="searchFileName">File Name:</label>
						</th>
						<td>
							<input type="text" name="searchFileName" value="" />
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="searchFileType">File Type:</label>
						</th>
						<td>
							<input type="text" name="searchFileType" value="" />
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="ContainerName">Container Name:</label>
						</th>
						<td>
							<select name="searchContainer" title="Search within this container">
								<?php
								try {
									$storageClient       = WindowsAzureStorageUtil::getStorageClient();
									/** @var WindowsAzure\Blob\Models\ListContainersResult $listContainerResult */
									$listContainerResult = $storageClient->listContainers();
									/** @var WindowsAzure\Blob\Models\Container $container */
									foreach ( $listContainerResult->getContainers() as $container ) {
										$container_name = $container->getName();
										?>
										<option value="<?php echo esc_attr( $container_name ); ?>"
											<?php selected( $container_name, $default_azure_storage_account_container_name ); ?>>
											<?php echo esc_html( $container_name ); ?>
										</option>
										<?php
									}

									echo '<option value="ALL_CONTAINERS">All Containers</option>';
								} catch ( Exception $ex ) {
									// Fires if account keys are not yet set
									error_log( $ex->getMessage(), E_USER_WARNING );
								}
								?>
							</select>
						</td>
					</tr>
				</table>

				<input type="hidden" name="action" value="search_azure_storage" />
				<?php
				submit_button(
					esc_attr_x( 'Search', 'verb', 'windows-azure-storage' ),
					'primary search',
					'submit',
					true,
					array(
						'aria-label' => __( 'Search this container.', 'windows-azure-storage' ),
						'id'         => 'windows-azure-storage-search',
						'role'       => 'button',
					)
				);
				?>
			</form>
		</div>
		<hr />
		<?php
	}
}

/**
 * Render Upload Tab in the Windows Azure Storage popup dialog
 *
 * @return void
 */
function windows_azure_storage_dialog_upload_tab() {
	// remove all registerd filters for the tabs
	unset( $GLOBALS['wp_filter']['media_upload_tabs'] );

	// register our filter for the tabs
	add_filter( "media_upload_tabs", "windows_azure_storage_dialog_add_tab" );

	media_upload_header();

	/**
	 * The post ID of the originating editor page.
	 *
	 * Passed via $_GET from the post being edited when the iframe is loaded.
	 * If iframe is accessed outside an originating editor, this will be 0 and
	 * nonces will fail. :)
	 *
	 * @var int $post_id
	 */
	$post_id = isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0;

	$azure_storage_account_name                   = WindowsAzureStorageUtil::getAccountName();
	$azure_storage_account_primary_access_key     = WindowsAzureStorageUtil::getAccountKey();
	$default_azure_storage_account_container_name = WindowsAzureStorageUtil::getDefaultContainer();
	$uploadMessage                                = null;
	$uploadSuccess                                = true;
	if ( empty( $azure_storage_account_name ) || empty( $azure_storage_account_primary_access_key ) ) {
		echo '<h3 style="margin: 10px;">Azure Storage Account not yet configured</h3>';
		echo '<p style="margin: 10px;">Please configure the account in Windows Azure Settings Tab.</p>';
	} else {
		// Set selected container. If none, then use default container
		$selected_container_name = $default_azure_storage_account_container_name;

		if ( ! empty( $_POST['selected_container'] ) ) {
			$selected_container_name = sanitize_text_field( $_POST['selected_container'] );
		} else if ( ! empty( $_GET['selected_container'] ) ) {
			$selected_container_name = sanitize_text_field( $_GET['selected_container'] );
		}

		if ( empty( $selected_container_name ) ) {
			echo '<p style="margin: 10px; color: red;">Default Azure Storage Container name is not yet configured. Please configure it in the Windows Azure Settings Tab.</p>';
		}

		// Handle file upload
		if ( ! empty( $_POST['action'] ) && 'upload' === $_POST['action'] ) { // CSRF okay
			if ( 0 === $_FILES["uploadFileName"]["error"] ) {
				if ( ! file_exists( $_FILES['uploadFileName']['tmp_name'] ) ) {
					echo "<p>Uploaded file " . esc_html( $_FILES['uploadFileName']['tmp_name'] ) . " does not exist</p><br/>";
				} else {
					$metaData = array( 'mimetype' => $_FILES['uploadFileName']['type'] );
					if ( ! empty( $_POST["uploadFileTag"] ) ) {
						$metaData["tag"] = sanitize_text_field( $_POST["uploadFileTag"] );
					}

					try {
						if (
							false === check_admin_referer( 'upload_blob_' . $post_id, 'upload_blob_nonce' ) ||
							false === WindowsAzureStorageUtil::check_action_permissions( 'upload' )
						) {
							throw new Exception( __( 'Nonce check failed. Please try again, or contact your site administrator for assistance.', 'windows-azure-storage' ) );
						}
						$blobName = WindowsAzureStorageUtil::uniqueBlobName( $selected_container_name, $_FILES['uploadFileName']['name'] );
						WindowsAzureStorageUtil::putBlockBlob( $selected_container_name, $blobName, $_FILES['uploadFileName']['tmp_name'], null, $metaData );
						$uploadMessage = "Successfully uploaded file '" . $blobName . "' to the container '" . $selected_container_name . "'.";
					} catch ( Exception $e ) {
						$uploadSuccess = false;
						$uploadMessage = "Error in uploading file '" . $_FILES['uploadFileName']['name'] . "', Error: " . $e->getMessage();
					}
				}
			}
		} else if ( ! empty( $_POST['action'] ) && 'create' === $_POST['action'] ) {
			if ( ! empty( $_POST["createContainer"] ) ) {
				try {
					if ( false === check_admin_referer( 'upload_create_container_' . $post_id,
							'upload_create_container_nonce'
						) ||
					     false === WindowsAzureStorageUtil::check_action_permissions( 'create_container' )
					) {
						throw new Exception( __( 'Sorry, you do not have permission to create containers for this
						account. Please contact your site administrator for assistance.',
							'windows-azure-storage' ) );
					}
					WindowsAzureStorageUtil::createPublicContainer( sanitize_text_field( $_POST["createContainer"] ) );
					$uploadMessage = "The container '" . $_POST["createContainer"] . "' successfully created";
				} catch ( Exception $e ) {
					$uploadSuccess = false;
					$uploadMessage = "Container creation failed: " . $e->getMessage();
				}
			} else {
				$uploadSuccess = false;
				$uploadMessage = "Please specify container name";
			}
		}
		$form_action_url = add_query_arg(
			array(
				'post_id' => $post_id,
				'tab'     => 'upload',
			),
			MSFT_AZURE_PLUGIN_LEGACY_MEDIA_URL
		);
		?>
		<h3 style="margin: 10px;">Upload New File</h3>
		<div id="upload-form">
			<form name="UploadNewFileForm" style="margin: 10px;" method="post" enctype="multipart/form-data" action="<?php echo esc_url( $form_action_url ); ?>">
				<?php wp_nonce_field( 'upload_blob_' . $post_id, 'upload_blob_nonce' ); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="selected_container">Container Name :</label>
						</th>
						<td>
							<select name="selected_container" title="Storage container to be used for uploading media files" onChange="<?php echo esc_js( 'onUpload_ContainerSelectionChanged();' ); ?>">
								<?php
								try {
									$storageClient       = WindowsAzureStorageUtil::getStorageClient();
									$listContainerResult = $storageClient->listContainers();
									foreach ( $listContainerResult->getContainers() as $container ) :
										if ( empty( $selected_container_name ) ) {
											$selected_container_name = $container->getName();
										}
										$container_name = $container->getName();
										?>
										<option value="<?php echo esc_attr( $container_name ); ?>"
											<?php selected( $container_name, $selected_container_name ); ?>>
											<?php echo esc_html( $container_name ); ?>
										</option>
									<?php endforeach; ?>
									<option value="__newContainer__">&mdash;&thinsp;<?php esc_html_e( 'Create New Container', 'windows-azure-storage' ); ?>&thinsp;&mdash;</option>
									<?php
								} catch ( Exception $ex ) {
									// Fires if account keys are not yet set.
									error_log( $ex->getMessage(), E_USER_WARNING );
								}
								?>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="createContainer" id="lblNewContainer">New Container Name:</label>
						</th>
						<td>
							<input type="text" name="createContainer" value="" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="uploadFileTag">Tag:</label>
						</th>
						<td>
							<input type="text" name="uploadFileTag" value="" />
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="uploadFileName">File Name:</label>
						</th>
						<td>
							<input type="file" name="uploadFileName" />
						</td>
					</tr>
				</table>

				<input type='hidden' name='action' value='upload' />
				<?php wp_nonce_field( 'upload_create_container_' . $post_id, 'upload_create_container_nonce' ); ?>
				<p class="submit">
					<input type="submit" class="button-primary" id="submit" value="<?php esc_attr_e( 'Upload', 'windows-azure-storage' ); ?>" />
				</p>
			</form>
		</div>
		<script type="text/javascript">
			onUpload_ContainerSelectionChanged();
		</script>
		<?php
		//TODO: Use WP classes and markup to create notices
		if ( ! empty( $uploadMessage ) ) {
			$color = $uploadSuccess ? 'green' : 'red';
			printf( '<p style="margin: 10px; color: %1$s;">%2$s</p>',
				esc_attr( $color ),
				wp_kses_post( $uploadMessage )
			);
		}
	}
}

/**
 * Delete a blob from specified container
 *
 * @param string $containerName Name of the parent container
 *
 * @param string $blobName      Name of the blob to be deleted
 *
 * @return void
 */
function deleteBlob( $containerName, $blobName ) {
	try {
		if ( WindowsAzureStorageUtil::blobExists( $containerName, $blobName ) ) {
			$_SERVER['REQUEST_URI'] = remove_query_arg(
				array(
					'delete_blob',
					'filename',
					'selected_container',
				),
				$_SERVER['REQUEST_URI']
			);
			WindowsAzureStorageUtil::deleteBlob( $containerName, $blobName );
		}
	} catch ( Exception $e ) {
		/* translators: 1: blob (file) name, 2: container name, 3: error message */
		$message = sprintf(
			__( 'Error in deleting blob %1$s from container %2$s: %3$s', 'windows-azure-storage' ),
			$blobName,
			$containerName,
			$e->getMessage()
		);
		echo '<p class="warning">' . esc_html( $message ) . '</p>';
	}
}

/**
 * Generate ISO 8601 compliant date string in UTC time zone
 *
 * @param int $timestamp input timestamp for conversion
 *
 * @return string
 */
function isoDate( $timestamp = null ) {
	$tz = @date_default_timezone_get();
	@date_default_timezone_set( 'UTC' );
	if ( is_null( $timestamp ) ) {
		$timestamp = time();
	}

	$returnValue = str_replace( '+00:00', 'Z', @date( 'c', $timestamp ) );
	@date_default_timezone_set( $tz );

	return $returnValue;
}
