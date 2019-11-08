<?php
/**
 * Compatibility assistance for WordPress and Microsoft Azure Storage.
 */

namespace Microsoft\Azure\BlobStorage\Helpers;

/**
 * Restore original images from Azure Blob Storage when regenerating thumbnails via WP-CLI.
 *
 * As the Azure Blob Storage plugin offloads all media to Azure, we need to bring that media back
 * to the local filesystem when we're regenerating thumbnails.
 *
 * This function very carefully determines if we're using WP-CLI's `media regenerate` command and,
 * if so, determine whether or not the source file should be downloaded.
 *
 * Whether or not the file will be removed again is dependent upon the plugin settings; if the
 * plugin has been instructed to remove media after moving it to Azure, the file we just pulled
 * down (as well as its freshly-regenerated thumbnails) will be removed from the web server.
 *
 * Please note that while this executes on a WordPress filter (specifically "get_attached_file"),
 * the value passed through the filter will *not* be modified. If, in the future, an action is
 * added to `wp media regenerate`, it's recommended that this be rewritten to use that action
 * instead of being a filter with side effects (albeit under a very specific use-case).
 *
 * @link http://wp-cli.org/commands/media/regenerate/
 *
 * @global $argv
 *
 * @param string $file          The full system filepath for the image file.
 * @param int    $attachment_id The ID of the attachment we're resizing.
 * @return string The unaltered $file string.
 */
function restore_original_image( $file, $attachment_id ) {
	global $argv;

	// Return early if we're not using WP-CLI.
	if ( ! defined( 'WP_CLI' ) || ! WP_CLI || empty( $argv ) ) {
		return $file;
	}

	/*
	 * Next, ensure we're using WP-CLI's `media regenerate` command: `wp media regenerate`.
	 *
	 * [0] will be the path to WP-CLI.
	 * [1] should be "media".
	 * [2] should be "regenerate".
	 */
	if ( 'media' !== $argv[1] || 'regenerate' !== $argv[2] ) {
		return $file;
	}

	// Does the file exist?
	if ( $file && file_exists( $file ) ) {
		return $file;
	}

	// If not, we'll need to retrieve it.
	$url      = wp_get_attachment_url( $attachment_id );
	$response = wp_remote_get( $url, array(
		'timeout'  => MINUTE_IN_SECONDS,
		'stream'   => true,
		'filename' => $file,
	) );

	if ( is_wp_error( $response ) ) {
		error_log( esc_html( sprintf(
			/** Translators: %1$s is the URL, %2$s is the filepath, %3$d is the attachment ID, and %4$s the error message. */
			__( 'Unable to download %1$s to %2$s for attachment ID %3$d: %4$s', 'windows-azure-storage' ),
			$url,
			$file,
			$attachment_id,
			$response->get_error_message()
		) ) );

	} elseif ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
		error_log( esc_html( sprintf(
			/** Translators: %1$d is the response code, %2$s is the URL. */
			__( 'Received %1$d response code for %2$s', 'windows-azure-storage' ),
			wp_remote_retrieve_response_code( $response ),
			$url
		) ) );
	}

	return $file;
}
add_filter( 'get_attached_file', __NAMESPACE__ . '\restore_original_image', 10, 2 );
