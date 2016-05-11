<?php

/**
 * windows-azure-helper.php
 *
 * Windows Azure Storage helper class.
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
class Windows_Azure_Helper {

	/**
	 * Return account name.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void Account name.
	 */
	static public function get_account_name() {
		return get_option( 'azure_storage_account_name' );
	}

	/**
	 * Return account key.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void Account key.
	 */
	static public function get_account_key() {
		return get_option( 'azure_storage_account_primary_access_key' );
	}

	/**
	 * Return HTTP proxy setting.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void Proxy host.
	 */
	static public function get_http_proxy_host() {
		return get_option( 'http_proxy_host' );
	}

	/**
	 * Return HTTP proxy port.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void Proxy port.
	 */
	static public function get_http_proxy_port() {
		return get_option( 'http_proxy_port' );
	}

	/**
	 * Return HTTP proxy username.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void Proxy username.
	 */
	static public function get_http_proxy_username() {
		return get_option( 'http_proxy_username' );
	}

	/**
	 * Return HTTP proxy password.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void Proxy password.
	 */
	static public function get_http_proxy_password() {
		return get_option( 'http_proxy_password' );
	}

	/**
	 * Return storage default container.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void Default container name.
	 */
	static public function get_default_container() {
		return get_option( 'default_azure_storage_account_container_name' );
	}

	/**
	 * Return container ACL.
	 *
	 * @param string $container_name Container name.
	 * @param string $account_name   Account name.
	 * @param string $account_key    Account key.
	 *
	 * @since 4.0.0
	 *
	 * @return string|WP_Error API call result.
	 */
	static public function get_container_acl( $container_name, $account_name = '', $account_key = '' ) {

		list( $account_name, $account_key ) = self::get_api_credentials( $account_name, $account_key );
		$rest_api_client = new Windows_Azure_Rest_Api_Client( $account_name, $account_key );

		return $rest_api_client->get_container_acl( $container_name );
	}

	/**
	 * Return API credentials.
	 *
	 * @param string $account_name Account name.
	 * @param string $account_key  Account key.
	 *
	 * @since 4.0.0
	 *
	 * @return array Account credentials array.
	 */
	static public function get_api_credentials( $account_name, $account_key ) {
		if ( '' === $account_name ) {
			$account_name = self::get_account_name();
		}

		if ( '' === $account_key ) {
			$account_key = self::get_account_key();
		}

		return array( $account_name, $account_key );
	}

	/**
	 * Return containers list.
	 *
	 * @param string $account_name Account name.
	 * @param string $account_key  Account key.
	 * @param bool   $refresh      Whether new API request should be made instead of using cached list.
	 *
	 * @since 4.0.0
	 *
	 * @return WP_Error|Windows_Azure_List_Containers_Response Containers iterator class or WP_Error on failure.
	 */
	static public function list_containers( $account_name = '', $account_key = '', $refresh = false ) {
		static $containers_list;

		if ( null === $containers_list || $refresh ) {
			list( $account_name, $account_key ) = self::get_api_credentials( $account_name, $account_key );
			$rest_api_client = new Windows_Azure_Rest_Api_Client( $account_name, $account_key );

			$containers_list = $rest_api_client->list_containers();
		}

		return $containers_list;
	}

	/**
	 * Create new container.
	 *
	 * @param string $container_name Container name.
	 * @param string $account_name   Account name.
	 * @param string $account_key    Account key.
	 *
	 * @since 4.0.0
	 *
	 * @return string|WP_Error Container name or WP_Error on failure.
	 */
	static public function create_container( $container_name, $account_name = '', $account_key = '' ) {
		list( $account_name, $account_key ) = self::get_api_credentials( $account_name, $account_key );
		$rest_api_client = new Windows_Azure_Rest_Api_Client( $account_name, $account_key );

		return $rest_api_client->create_container( $container_name );
	}
}
