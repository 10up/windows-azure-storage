<?php
/**
 * Microsoft Azure Storage command line client.
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
class Windows_Azure_Storage_CLI extends WP_CLI_Command {

	/**
	 * List containers.
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command options.
	 *
	 * @return void
	 *
	 * ## OPTIONS
	 *
	 * [--prefix=<prefix>]
	 * : List containers which names start with prefix.
	 *
	 * @subcommand containers-list
	 *
	 * ## EXAMPLE
	 * wp windows-azure-storage containers-list --prefix=demo
	 */
	public function list_containers( $args, $assoc_args ) {
		$assoc_args = wp_parse_args( $assoc_args, array(
			'prefix' => '',
		) );

		$credentials = Windows_Azure_Config_Provider::get_account_credentials();
		$client      = new Windows_Azure_Rest_Api_Client( $credentials['account_name'], $credentials['account_key'] );
		$format_args = array(
			'format' => 'table',
			'fields' => array( 'Name' ),
			'field'  => null,
		);

		$table      = new \WP_CLI\Formatter( $format_args );
		$containers = $client->list_containers( $assoc_args['prefix'] );

		if ( is_wp_error( $containers ) ) {
			WP_CLI::error( $containers->get_error_message() );
			exit;
		}
		$items = array();
		foreach ( $containers as $container ) {
			$items[] = $container;
		}

		if ( empty( $items ) ) {
			WP_CLI::warning( __( 'No containers found.', 'windows-azure-storage' ) );
			exit;
		}

		$table->display_items( $items );
	}

	/**
	 * Create container.
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command options.
	 *
	 * @return void
	 *
	 * ## OPTIONS
	 *
	 * <name>
	 * : Container name.
	 *
	 * @subcommand container-create
	 *
	 * ## EXAMPLE
	 * wp windows-azure-storage container-create testcontainer
	 */
	public function create_container( $args, $assoc_args ) {
		if ( empty( $args ) ) {
			WP_CLI::error( __( 'Container name must be set.', 'windows-azure-storage' ) );
			exit;
		}

		list( $name ) = $args;
		$credentials = Windows_Azure_Config_Provider::get_account_credentials();
		$client      = new Windows_Azure_Rest_Api_Client( $credentials['account_name'], $credentials['account_key'] );
		$result      = $client->create_container( $name, Windows_Azure_Rest_Api_Client::CONTAINER_VISIBILITY_BLOB );

		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
			exit;
		}

		WP_CLI::success(
			sprintf(
				__( 'Created container with name "%s"', 'windows-azure-storage' ),
				$result
			)
		);
	}

	/**
	 * Get container properties.
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command options.
	 *
	 * @return void
	 *
	 * ## OPTIONS
	 *
	 * <name>
	 * : Container name.
	 *
	 * @subcommand container-properties
	 *
	 * ## EXAMPLE
	 * wp windows-azure-storage container-properties testcontainer
	 */
	public function get_container_properties( $args, $assoc_args ) {
		if ( empty( $args ) ) {
			WP_CLI::error( __( 'Container name must be set.', 'windows-azure-storage' ) );
			exit;
		}

		list( $name ) = $args;
		$credentials = Windows_Azure_Config_Provider::get_account_credentials();
		$client      = new Windows_Azure_Rest_Api_Client( $credentials['account_name'], $credentials['account_key'] );
		$result      = $client->get_container_properties( $name );

		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
			exit;
		}

		$format_args = array(
			'format' => 'table',
			'fields' => array_keys( $result ),
			'field'  => null,
		);

		$table = new \WP_CLI\Formatter( $format_args );
		$table->display_item( $result );
	}

	/**
	 * Get container ACL.
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command options.
	 *
	 * @return void
	 *
	 * ## OPTIONS
	 *
	 * <name>
	 * : Container name.
	 *
	 * @subcommand container-acl
	 *
	 * ## EXAMPLE
	 * wp windows-azure-storage container-acl testcontainer
	 */
	public function get_container_acl( $args, $assoc_args ) {
		if ( empty( $args ) ) {
			WP_CLI::error( __( 'Container name must be set.', 'windows-azure-storage' ) );
			exit;
		}

		list( $name ) = $args;
		$credentials = Windows_Azure_Config_Provider::get_account_credentials();
		$client      = new Windows_Azure_Rest_Api_Client( $credentials['account_name'], $credentials['account_key'] );
		$result      = $client->get_container_acl( $name );

		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
			exit;
		}

		WP_CLI::success(
			sprintf(
				__( 'Container "%s" access policy set to: "%s"', 'windows-azure-storage' ),
				$name,
				$result
			)
		);
	}

	/**
	 * Delete blob from container.
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command options.
	 *
	 * @return void
	 *
	 * ## OPTIONS
	 *
	 * <container>
	 * : Container name.
	 *
	 * <path>
	 * : Remote file path.
	 *
	 * @subcommand delete-blob
	 *
	 * ## EXAMPLE
	 * wp windows-azure-storage delete-blob testcontainer image1.png
	 */
	public function delete_blob( $args, $assoc_args ) {
		if ( empty( $args ) || count( $args ) < 2 ) {
			WP_CLI::error( __( 'Container and remote path must be set.', 'windows-azure-storage' ) );
			exit;
		}

		list( $container, $remote_path ) = $args;
		$credentials = Windows_Azure_Config_Provider::get_account_credentials();
		$client      = new Windows_Azure_Rest_Api_Client( $credentials['account_name'], $credentials['account_key'] );
		$result      = $client->delete_blob( $container, $remote_path );

		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
			exit;
		}

		WP_CLI::success(
			__( 'Blob has been deleted.', 'windows-azure-storage' )
		);
	}

	/**
	 * Get blob properties.
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command options.
	 *
	 * @return void
	 *
	 * ## OPTIONS
	 *
	 * <container>
	 * : Container name.
	 *
	 * <remote_path>
	 * : Blob path.
	 *
	 * @subcommand blob-properties
	 *
	 * ## EXAMPLE
	 * wp windows-azure-storage blob-properties testcontainer image1.png
	 */
	public function get_blob_properties( $args, $assoc_args ) {
		if ( count( $args ) < 2 ) {
			WP_CLI::error( __( 'Container name and blob path must be set.', 'windows-azure-storage' ) );
			exit;
		}

		list( $container, $remote_path ) = $args;
		$credentials = Windows_Azure_Config_Provider::get_account_credentials();
		$client      = new Windows_Azure_Rest_Api_Client( $credentials['account_name'], $credentials['account_key'] );
		$result      = $client->get_blob_properties( $container, $remote_path );

		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
			exit;
		}

		$format_args = array(
			'format' => 'table',
			'fields' => array_keys( $result ),
			'field'  => null,
		);

		$table = new \WP_CLI\Formatter( $format_args );
		$table->display_item( $result );
	}

	/**
	 * List blobs in given container.
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command options.
	 *
	 * @return void
	 *
	 * ## OPTIONS
	 *
	 * --container=<name>
	 * : Container name.
	 *
	 * [--prefix=<prefix>]
	 * : List containers which names start with prefix.
	 *
	 * @subcommand blobs-list
	 *
	 * ## EXAMPLE
	 * wp windows-azure-storage blobs-list test-container --prefix=demo
	 */
	public function list_blobs( $args, $assoc_args ) {
		$assoc_args = wp_parse_args( $assoc_args, array(
			'prefix'    => '',
			'container' => '',
		) );

		if ( empty( $assoc_args['container'] ) ) {
			WP_CLI::error( __( 'Container name amust be set.', 'windows-azure-storage' ) );
			exit;
		}

		$credentials = Windows_Azure_Config_Provider::get_account_credentials();
		$client      = new Windows_Azure_Rest_Api_Client( $credentials['account_name'], $credentials['account_key'] );
		$format_args = array(
			'format' => 'table',
			'fields' => array( 'Name' ),
			'field'  => null,
		);

		$table = new \WP_CLI\Formatter( $format_args );
		$blobs = $client->list_blobs( $assoc_args['container'], $assoc_args['prefix'] );

		if ( is_wp_error( $blobs ) ) {
			WP_CLI::error( $blobs->get_error_message() );
			exit;
		}
		$items = array();
		foreach ( $blobs as $blob ) {
			$items[] = $blob;
		}

		if ( empty( $items ) ) {
			WP_CLI::warning( __( 'No blobs found.', 'windows-azure-storage' ) );
			exit;
		}

		$table->display_items( $items );
	}
}

WP_CLI::add_command( 'windows-azure-storage', 'Windows_Azure_Storage_CLI' );
