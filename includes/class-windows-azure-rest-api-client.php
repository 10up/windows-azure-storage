<?php

/**
 * windows-azure-rest-api-client.php
 *
 * Windows Azure Storage REST API client.
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
class Windows_Azure_Rest_Api_Client {

	/**
	 * Azure API version.
	 *
	 * @const string
	 */
	const API_VERSION = '2015-04-05';

	/**
	 * Blob API default request timeout.
	 *
	 * @const int
	 */
	const API_REQUEST_TIMEOUT = 30;

	/**
	 * Blob API default bulk size for various operations.
	 *
	 * @const int
	 */
	const API_REQUEST_BULK_SIZE = 100;

	/**
	 * Blob API endpoint pattern.
	 *
	 * @const string
	 */
	const API_BLOB_ENDPOINT = 'https://%s.blob.core.windows.net/';

	/**
	 * Azure API version header name.
	 *
	 * @const string
	 */
	const API_HEADER_MS_VERSION = 'x-ms-version';

	/**
	 * Azure API date header name.
	 *
	 * @const string
	 */
	const API_HEADER_MS_DATE = 'x-ms-date';

	/**
	 * Azure API blob public access header name.
	 *
	 * @const string
	 */
	const API_HEADER_BLOB_PUBLIC_ACCESS = 'x-ms-blob-public-access';

	/**
	 * Azure API canonicalized
	 * @const string
	 */
	const API_CANONICALIZED_HEADER_PREFIX = 'x-ms-';

	/**
	 * Container private access type.
	 *
	 * @const string
	 */
	const CONTAINER_VISIBILITY_PRIVATE = 'private';

	/**
	 * Container "container" access type (publicily browseable).
	 *
	 * @const string
	 */
	const CONTAINER_VISIBILITY_CONTAINER = 'container';

	/**
	 * Container "blob: access type (direct blob access only).
	 *
	 * @const string
	 */
	const CONTAINER_VISIBILITY_BLOB = 'blob';

	/**
	 * Azure Storage account name.
	 *
	 * @var null|string
	 */
	protected $_account_name;

	/**
	 * Azure Storage access key.
	 *
	 * @var null|string
	 */
	protected $_access_key;

	/**
	 * URL which is currently being requested.
	 *
	 * @var null|string
	 */
	protected $_current_url;

	/**
	 * List of headers which should be included when computing request signature.
	 *
	 * @var array
	 */
	protected $_signature_headers;

	/**
	 * Windows_Azure_Rest_Api_Client constructor.
	 *
	 * @param string $account_name Optional storage account name.
	 * @param string $access_key   Optional storage access key.
	 */
	public function __construct( $account_name = null, $access_key = null ) {
		$this->set_account_name( $account_name );
		$this->set_access_key( $access_key );
		$this->_signature_headers = array(
			'Content-Encoding',
			'Content-Language',
			'Content-Length',
			'Content-MD5',
			'Content-Type',
			'Date',
			'If-Modified-Since',
			'If-Match',
			'If-None-Match',
			'If-Unmodified-Since',
			'Range'
		);
	}

	/**
	 * Get storage account name.
	 *
	 * @return null|string
	 */
	public function get_account_name() {
		return $this->_account_name;
	}

	/**
	 * Set storage account name.
	 *
	 * @param null|string $account_name Storage account name.
	 */
	public function set_account_name( $account_name ) {
		$this->_account_name = sanitize_text_field( $account_name );
	}

	/**
	 * Get storage access key.
	 *
	 * @return null|string
	 */
	public function get_access_key() {
		return $this->_access_key;
	}

	/**
	 * Set storage access key.
	 *
	 * @param null|string $access_key
	 */
	public function set_access_key( $access_key ) {
		$this->_access_key = $access_key;
	}

	/**
	 * Filter hook for http_request_args.
	 *
	 * @param array  $args Request arguments.
	 * @param string $url  Request URL.
	 *
	 * @return array
	 */
	public function inject_authorization_header( array $args, $url ) {

		// Only handle our known urls.
		if ( $url !== $this->_current_url ) {
			return $url;
		}

		$args = wp_parse_args( $args, array(
			'method'  => 'GET',
			'headers' => array(),
		) );

		$signature_data   = array();
		$signature_data[] = strtoupper( $args['method'] );

		foreach ( $this->_signature_headers as $header ) {
			$signature_data[] = isset( $args['headers'][ $header ] ) ? $args['headers'][ $header ] : null;
		}

		$signature_data[] = implode( "\n", $this->_build_canonicalized_headers( $args['headers'] ) );
		$signature_data[] = $this->_build_canonicalized_resource( $url, $this->_account_name );

		$string_to_sign                   = implode( "\n", $signature_data );
		$signature                        = 'SharedKey ' . $this->get_account_name() . ':' . base64_encode( hash_hmac( 'sha256', $string_to_sign, base64_decode( $this->get_access_key() ), true ) );
		$args['headers']['Authorization'] = $signature;

		return $args;
	}

	/**
	 * List containers.
	 *
	 * @param string $prefix      List containers which names start with this prefix.
	 * @param int    $max_results Max containers to return.
	 * @param bool   $next_marker Next collection marker.
	 *
	 * @return Windows_Azure_List_Containers_Response|WP_Error
	 */
	public function list_containers( $prefix = '', $max_results = self::API_REQUEST_BULK_SIZE, $next_marker = false ) {
		$query_args = array(
			'comp'       => 'list',
			'maxresults' => apply_filters( 'azure_blob_list_containers_max_results', $max_results )
		);

		if ( ! empty( $prefix ) ) {
			$query_args['prefix'] = rawurlencode( $prefix );
		}

		if ( $next_marker ) {
			$query_args['marker'] = $next_marker;
		}

		$result = $this->_send_request( 'GET', $query_args );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new Windows_Azure_List_Containers_Response( $result, $this, $prefix, $max_results );
	}

	/**
	 * Create new container.
	 *
	 * @param string $name       Container name.
	 * @param string $visibility Container visibility.
	 *
	 * @return string|WP_Error
	 */
	public function create_container( $name, $visibility = self::CONTAINER_VISIBILITY_BLOB ) {
		$query_args = array(
			'restype' => 'container',
		);

		$name = sanitize_title_with_dashes( $name );

		$headers = array();

		switch ( $visibility ) {
			case self::CONTAINER_VISIBILITY_BLOB:
			case self::CONTAINER_VISIBILITY_CONTAINER:
				$headers[ self::API_HEADER_BLOB_PUBLIC_ACCESS ] = $visibility;
				break;
		}

		$result = $this->_send_request( 'PUT', $query_args, $headers, '', $name );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $name;
	}

	/**
	 * Get container properties.
	 *
	 * @param string $name Container name.
	 *
	 * @return array|WP_Error
	 */
	public function get_container_properties( $name ) {
		$query_args = array(
			'restype' => 'container',
		);

		$result = $this->_send_request( 'HEAD', $query_args, array(), '', $name );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$headers    = array( 'last-modified', 'etag', 'x-ms-lease-status', 'x-ms-lease-state', 'x-ms-lease-duration' );
		$properties = array();

		foreach ( $headers as $header ) {
			$properties[ $header ] = wp_remote_retrieve_header( $result, $header );
		}

		return $properties;
	}

	/**
	 * Get container ACL.
	 *
	 * @param string $name Container name.
	 *
	 * @return string|WP_Error
	 */
	public function get_container_acl( $name ) {
		$query_args = array(
			'restype' => 'container',
			'comp'    => 'acl',
		);

		$result = $this->_send_request( 'HEAD', $query_args, array(), '', $name );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$acl_header = wp_remote_retrieve_header( $result, 'x-ms-blob-public-access' );
		if ( empty( $acl_header ) ) {
			$acl_header = self::CONTAINER_VISIBILITY_PRIVATE;
		}

		return $acl_header;
	}

	/**
	 * Send REST request and return response.
	 *
	 * @param string       $method     HTTP verb.
	 * @param array        $query_args Request query args.
	 * @param array        $headers    Request headers.
	 * @param string|array $body       Request body.
	 * @param string       $path       REST API endpoint path.
	 *
	 * @return array|WP_Error
	 */
	protected function _send_request( $method, array $query_args = array(), array $headers = array(), $body = '', $path = '' ) {

		$query_args = wp_parse_args( $query_args, array(
			'timeout' => apply_filters( 'azure_blob_operation_timeout', self::API_REQUEST_TIMEOUT )
		) );

		$endpoint_url = $this->_build_api_endpoint_url( $path );

		if ( is_wp_error( $endpoint_url ) ) {
			return $endpoint_url;
		}

		$endpoint_url = add_query_arg( $query_args, $endpoint_url );

		if ( is_array( $body ) ) {
			$body = http_build_query( $body, null, '&' );
		}

		$headers = array_merge( $headers, array(
			self::API_HEADER_MS_VERSION => self::API_VERSION,
			self::API_HEADER_MS_DATE    => get_gmt_from_date( date( 'D, d M Y H:i:s T' ), 'D, d M Y H:i:s T' ),
			'Content-Length'            => strlen( $body ) > 0 ? strlen( $body ) : null,
			'Content-Type'              => 'text/plain',
		) );

		// Add this filter to be able to inject authorization header.
		add_filter( 'http_request_args', array( $this, 'inject_authorization_header' ), 10, 2 );

		$this->_current_url = $endpoint_url;

		$result = wp_remote_request( $endpoint_url, array(
			'method'      => $method,
			'headers'     => $headers,
			'body'        => $body,
			'timeout'     => $query_args['timeout'],
			'httpversion' => '1.1',
		) );

		$this->_current_url = null;

		// Remove this filter once request is done.
		remove_filter( 'http_request_args', array( $this, 'inject_authorization_header' ) );

		$body = wp_remote_retrieve_body( $result );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $result );

		if ( $response_code < 200 || $response_code > 299 ) {
			return new WP_Error( $response_code, wp_remote_retrieve_response_message( $result ) );
		}

		$xml_structure = simplexml_load_string( $body );
		if ( ! empty( $body ) ) {
			return json_decode( wp_json_encode( $xml_structure ), true );
		} else {
			return $result;
		}
	}

	/**
	 * Return Blob API endpoint URL.
	 *
	 * @param string $path URI path.
	 *
	 * @return string|WP_Error
	 */
	protected function _build_api_endpoint_url( $path = '' ) {
		static $endpoint_url;

		if ( null !== $endpoint_url ) {
			return $endpoint_url;
		}

		if ( empty( $this->_account_name ) ) {
			return new WP_Error( -1, __( 'Storage account name not set.', MSFT_AZURE_PLUGIN_DOMAIN_NAME ) );
		}

		$endpoint_url = sprintf( self::API_BLOB_ENDPOINT, $this->_account_name );

		return $endpoint_url . trim( $path );
	}

	/**
	 * Build canonicalized headers collection.
	 *
	 * @param array $headers Headers.
	 *
	 * @return array
	 */
	protected function _build_canonicalized_headers( array $headers ) {
		$normalized_headers    = array();
		$canonicalized_headers = array();

		foreach ( $headers as $header => $value ) {
			$header = strtolower( $header );
			$header = ltrim( $header );
			if ( 0 !== strpos( $header, self::API_CANONICALIZED_HEADER_PREFIX ) ) {
				continue;
			}

			$value                         = str_replace( "\r\n", ' ', $value );
			$value                         = rtrim( $value );
			$normalized_headers[ $header ] = $value;
		}

		ksort( $normalized_headers );

		foreach ( $normalized_headers as $key => $value ) {
			$canonicalized_headers[] = $key . ':' . $value;
		}

		return $canonicalized_headers;
	}

	/**
	 * Build canonicalized resource string.
	 *
	 * @param string $url          Endpoint URL.
	 * @param string $account_name Storage account name.
	 *
	 * @return string
	 */
	protected function _build_canonicalized_resource( $url, $account_name ) {
		/** @var $parsed_url array */
		$parsed_url             = wp_parse_url( $url );
		$canonicalized_resource = '/' . $account_name . ( isset( $parsed_url['path'] ) ? $parsed_url['path'] : '' );

		if ( isset( $parsed_url['query'] ) ) {
			$query = array();
			parse_str( $parsed_url['query'], $query );
			array_change_key_case( $query, CASE_LOWER );
			ksort( $query );

			foreach ( $query as $key => $value ) {
				$values = explode( ',', $value );
				sort( $values );
				$canonicalized_resource .= "\n" . $key . ':' . rawurldecode( implode( ',', $values ) );
			}
		}

		return $canonicalized_resource;
	}
}
