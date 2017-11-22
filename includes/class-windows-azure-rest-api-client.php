<?php

/**
 * Microsoft Azure Storage REST API client.
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
 * @since     4.0.0
 */
class Windows_Azure_Rest_Api_Client {

	/**
	 * Azure API version.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_VERSION = '2015-12-11';

	/**
	 * Blob API default request timeout.
	 *
	 * @since 4.0.0
	 *
	 * @const int
	 */
	const API_REQUEST_TIMEOUT = 1800;

	/**
	 * Blob API default bulk size for various operations.
	 *
	 * @since 4.0.0
	 *
	 * @const int
	 */
	const API_REQUEST_BULK_SIZE = 100;

	/**
	 * Blob API endpoint pattern.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_BLOB_ENDPOINT = 'https://%s.blob.core.windows.net/';

	/**
	 * Azure API version header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_MS_VERSION = 'x-ms-version';

	/**
	 * Azure API date header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_MS_DATE = 'x-ms-date';

	/**
	 * Azure API blob public access header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_BLOB_PUBLIC_ACCESS = 'x-ms-blob-public-access';

	/**
	 * Azure API canonicalized.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_CANONICALIZED_HEADER_PREFIX = 'x-ms-';

	/**
	 * Last-Modified header.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_LAST_MODIFIED = 'last-modified';

	/**
	 * Azure API blob type header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_BLOB_TYPE = 'x-ms-blob-type';

	/**
	 * Azure API blob copy completion time header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_COPY_COMPLETION_TIME = 'x-ms-copy-completion-time';

	/**
	 * Azure API blob copy status description header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_COPY_STATUS_DESCRIPTION = 'x-ms-copy-status-description';

	/**
	 * Azure API blob copy id header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_COPY_ID = 'x-ms-copy-id';

	/**
	 * Azure API blob copy progress header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_COPY_PROGRESS = 'x-ms-copy-progress';

	/**
	 * Azure API blob copy source header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_COPY_SOURCE = 'x-ms-copy-source';

	/**
	 * Azure API blob copy status header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_COPY_STATUS = 'x-ms-copy-status';

	/**
	 * Azure API blob lease duration header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_LEASE_DURATION = 'x-ms-lease-duration';

	/**
	 * Azure API blob lease state header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_LEASE_STATE = 'x-ms-lease-state';

	/**
	 * Azure API blob lease status header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_LEASE_STATUS = 'x-ms-lease-status';

	/**
	 * Content-Length header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_CONTENT_LENGTH = 'content-length';

	/**
	 * Content-Type header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_CONTENT_TYPE = 'content-type';

	/**
	 * Etag header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_ETAG = 'etag';

	/**
	 * Content-MD5 header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_CONTENT_MD5 = 'content-md5';

	/**
	 * Content-Encoding header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_CONTENT_ENCODING = 'content-encoding';

	/**
	 * Content-Language header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_CONTENT_LANGUAGE = 'content-language';

	/**
	 * Content-Disposition header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_CONTENT_DISPOSITION = 'content-disposition';

	/**
	 * Cache-Control header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_CACHE_CONTROL = 'cache-control';

	/**
	 * Azure API blob sequence number header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_BLOB_SEQUENCE_NUMBER = 'x-ms-blob-sequence-number';

	/**
	 * Azure API blob commited block count header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_BLOB_COMMITED_BLOCK_COUNT = 'x-ms-blob-committed-block-count';

	/**
	 * Azure API blob cache control property header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_MS_BLOB_CACHE_CONTROL = 'x-ms-blob-cache-control';

	/**
	 * Azure API blob content type property header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_MS_BLOB_CONTENT_TYPE = 'x-ms-blob-content-type';

	/**
	 * Azure API blob content MD5 property header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_MS_BLOB_CONTENT_MD5 = 'x-ms-blob-content-md5';

	/**
	 * Azure API blob content encoding property header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_MS_BLOB_CONTENT_ENCODING = 'x-ms-blob-content-encoding';

	/**
	 * Azure API blob content language property header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_MS_BLOB_CONTENT_LANGUAGE = 'x-ms-blob-content-language';

	/**
	 * Azure API blob content disposition property header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_MS_BLOB_CONTENT_DISPOSITION = 'x-ms-blob-content-disposition';

	/**
	 * Accept-Ranges header name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const API_HEADER_ACCEPT_RANGES = 'accept-ranges';

	/**
	 * Container private access type.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const CONTAINER_VISIBILITY_PRIVATE = 'private';

	/**
	 * Container "container" access type (publicily browseable).
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const CONTAINER_VISIBILITY_CONTAINER = 'container';

	/**
	 * Container "blob: access type (direct blob access only).
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const CONTAINER_VISIBILITY_BLOB = 'blob';

	/**
	 * Azure API Append Blob type.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const APPEND_BLOB_TYPE = 'AppendBlob';

	/**
	 * Azure Storage account name.
	 *
	 * @since 4.0.0
	 *
	 * @var null|string
	 */
	protected $_account_name;

	/**
	 * Azure Storage access key.
	 *
	 * @since 4.0.0
	 *
	 * @var null|string
	 */
	protected $_access_key;

	/**
	 * URL which is currently being requested.
	 *
	 * @since 4.0.0
	 *
	 * @var null|string
	 */
	protected $_current_url;

	/**
	 * List of headers which should be included when computing request signature.
	 *
	 * @since 4.0.0
	 *
	 * @var array
	 */
	protected $_signature_headers;

	/**
	 * Windows_Azure_Rest_Api_Client constructor.
	 *
	 * @since 4.0.0
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
			'Range',
		);
	}

	/**
	 * Get storage account name.
	 *
	 * @since 4.0.0
	 *
	 * @return null|string Account name.
	 */
	public function get_account_name() {
		return $this->_account_name;
	}

	/**
	 * Set storage account name.
	 *
	 * @since 4.0.0
	 *
	 * @param null|string $account_name Storage account name.
	 *
	 * @return void
	 */
	public function set_account_name( $account_name ) {
		$this->_account_name = sanitize_text_field( $account_name );
	}

	/**
	 * Get storage access key.
	 *
	 * @since 4.0.0
	 *
	 * @return null|string Access key.
	 */
	public function get_access_key() {
		return $this->_access_key;
	}

	/**
	 * Set storage access key.
	 *
	 * @since 4.0.0
	 *
	 * @param null|string $access_key Storage access key.
	 *
	 * @return void
	 */
	public function set_access_key( $access_key ) {
		$this->_access_key = $access_key;
	}

	/**
	 * Filter hook for http_request_args.
	 *
	 * @since 4.0.0
	 *
	 * @param array  $args Request arguments.
	 * @param string $url  Request URL.
	 *
	 * @return array Modified request arguments.
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
		if ( array_key_exists( 'Content-Length', $args['headers'] ) ) {
			$args['headers']['Content-Length'] = (int)$args['headers']['Content-Length'];
		}

		return $args;
	}

	/**
	 * List containers.
	 *
	 * @since 4.0.0
	 *
	 * @param string $prefix      List containers which names start with this prefix.
	 * @param int    $max_results Max containers to return.
	 * @param bool   $next_marker Next collection marker.
	 *
	 * @return Windows_Azure_List_Containers_Response|WP_Error List of containers of WP_Error on failure.
	 */
	public function list_containers( $prefix = '', $max_results = self::API_REQUEST_BULK_SIZE, $next_marker = false ) {
		$query_args = array(
			'comp'       => 'list',
			'maxresults' => apply_filters( 'azure_blob_list_containers_max_results', $max_results ),
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
	 * @since 4.0.0
	 *
	 * @param string $name       Container name.
	 * @param string $visibility Container visibility.
	 *
	 * @return string|WP_Error New container name or WP_Error on failure.
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
	 * @since 4.0.0
	 *
	 * @param string $name Container name.
	 *
	 * @return array|WP_Error Container properties array of WP_Error on failure.
	 */
	public function get_container_properties( $name ) {
		$query_args = array(
			'restype' => 'container',
		);

		$result = $this->_send_request( 'HEAD', $query_args, array(), '', $name );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$headers    = array( self::API_HEADER_LAST_MODIFIED, 'etag', 'x-ms-lease-status', 'x-ms-lease-state', 'x-ms-lease-duration' );
		$properties = array();

		foreach ( $headers as $header ) {
			$properties[ $header ] = wp_remote_retrieve_header( $result, $header );
		}

		return $properties;
	}

	/**
	 * Get container ACL.
	 *
	 * @since 4.0.0
	 *
	 * @param string $name Container name.
	 *
	 * @return string|WP_Error Container ACL string or WP_Error on failure.
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
	 * List blobs in container.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container   Container name.
	 * @param string $prefix      List blobs which names start with this prefix.
	 * @param int    $max_results Max blobs to return.
	 * @param bool   $next_marker Next collection marker.
	 *
	 * @return Windows_Azure_List_Blobs_Response|WP_Error Blobs list or WP_Error on failure.
	 */
	public function list_blobs( $container, $prefix = '', $max_results = self::API_REQUEST_BULK_SIZE, $next_marker = false ) {
		$query_args = array(
			'comp'       => 'list',
			'maxresults' => apply_filters( 'azure_blob_list_blobs_max_results', $max_results ),
			'restype'    => 'container',
		);

		if ( ! empty( $prefix ) ) {
			$query_args['prefix'] = rawurlencode( $prefix );
		}

		if ( $next_marker ) {
			$query_args['marker'] = $next_marker;
		}

		$result = $this->_send_request( 'GET', $query_args, array(), '', $container );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new Windows_Azure_List_Blobs_Response( $result, $this, $prefix, $max_results, $container );
	}

	/**
	 * Delete blob from container.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container   Container name.
	 * @param string $remote_path Remote blob path.
	 *
	 * @return bool|WP_Error True on success or WP_Error on failure.
	 */
	public function delete_blob( $container, $remote_path ) {
		$container = trailingslashit( $container );
		$result    = $this->_send_request( 'DELETE', array(), array(), '', $container . $remote_path );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return true;
	}

	/**
	 * Get blob properties.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container   Container name.
	 * @param string $remote_path Remote blob path.
	 *
	 * @return array|WP_Error Blob properties array or WP_Error on failure.
	 */
	public function get_blob_properties( $container, $remote_path ) {
		$container = trailingslashit( $container );
		$result    = $this->_send_request( 'HEAD', array(), array(), '', $container . $remote_path );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$headers    = array(
			self::API_HEADER_LAST_MODIFIED,
			self::API_HEADER_BLOB_TYPE,
			self::API_HEADER_COPY_COMPLETION_TIME,
			self::API_HEADER_COPY_STATUS_DESCRIPTION,
			self::API_HEADER_COPY_ID,
			self::API_HEADER_COPY_PROGRESS,
			self::API_HEADER_COPY_SOURCE,
			self::API_HEADER_COPY_STATUS,
			self::API_HEADER_LEASE_DURATION,
			self::API_HEADER_LEASE_STATE,
			self::API_HEADER_LEASE_STATUS,
			self::API_HEADER_CONTENT_LENGTH,
			self::API_HEADER_CONTENT_TYPE,
			self::API_HEADER_ETAG,
			self::API_HEADER_CONTENT_MD5,
			self::API_HEADER_CONTENT_ENCODING,
			self::API_HEADER_CONTENT_LANGUAGE,
			self::API_HEADER_CONTENT_DISPOSITION,
			self::API_HEADER_CACHE_CONTROL,
			self::API_HEADER_BLOB_SEQUENCE_NUMBER,
			self::API_HEADER_ACCEPT_RANGES,
			self::API_HEADER_BLOB_COMMITED_BLOCK_COUNT,
		);
		$properties = array();

		foreach ( $headers as $header ) {
			$properties[ $header ] = wp_remote_retrieve_header( $result, $header );
		}

		return $properties;
	}

	/**
	 * Put blob properties.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container   Container name.
	 * @param string $remote_path Remote blob path.
	 * @param array  $properties  Array with properties.
	 *
	 * @return bool|WP_Error True on success or WP_Error on failure.
	 */
	public function put_blob_properties( $container, $remote_path, array $properties = array() ) {
		$container  = trailingslashit( $container );
		$query_args = array(
			'comp' => 'properties',
		);
		$properties = apply_filters( 'windows_azure_storage_blob_properties', $properties, $container, $remote_path );

		$allowed_properties  = array(
			self::API_HEADER_MS_BLOB_CACHE_CONTROL,
			self::API_HEADER_MS_BLOB_CONTENT_TYPE,
			self::API_HEADER_MS_BLOB_CONTENT_MD5,
			self::API_HEADER_MS_BLOB_CONTENT_ENCODING,
			self::API_HEADER_MS_BLOB_CONTENT_LANGUAGE,
			self::API_HEADER_MS_BLOB_CONTENT_DISPOSITION,
		);
		$filtered_properties = array();

		foreach ( $allowed_properties as $allowed_property ) {
			if ( isset( $properties[ $allowed_property ] ) ) {
				$filtered_properties[ $allowed_property ] = $properties[ $allowed_property ];
			}
		}

		$result = $this->_send_request( 'PUT', $query_args, $filtered_properties, '', $container . $remote_path );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return true;
	}

	// @formatter:off
	/**
	 * Sanitize blobs names. Make sure their names are unique.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container Container name.
	 * @param array  $files     File names structure. Expected:
	 *                          {
	 *                              $prefix_mask_1 => {
	 *                                  $local_path_1_1 => $remote_path_1_1,
	 *                                  $local_path_1_n => $remote_path_1_n
	 *                              },
	 *                              $prefix_mask_n => {
	 *                                  $local_path_n_1 => $remote_path_n_1,
	 *                                  $local_path_n_n => $remote_path_n_n
	 *                              },
	 *                          }
	 *
	 * @return array|WP_Error Sanitized blobs names or WP_Error on failure.
	 */
	// @formatter:on
	public function sanitize_blobs_names( $container, array $files = array() ) {
		if ( empty( $files ) ) {
			return $files;
		}
		$cycles        = 0;
		$was_sanitized = false;
		foreach ( $files as $prefix_group => &$group_contents ) {
			$cycles = 0;
			do {
				$sanitized_group_contents = $this->_sanitize_remote_paths( $container, $prefix_group, $group_contents );
				$was_sanitized            = $sanitized_group_contents !== $group_contents;
				if ( $was_sanitized ) {
					$group_contents = $sanitized_group_contents;
				}
				$cycles++;
			} while ( $was_sanitized && 5 > $cycles );
		}

		if ( 5 === $cycles && $was_sanitized ) {
			return new WP_Error( -100, __( 'Unable to safely sanitize blob names.', 'windows-azure-storage' ) );
		} else {
			return $files;
		}
	}

	// @formatter:off
	/**
	 * Put blobs on Azure Storage account.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container Container name.
	 * @param array  $files     File names structure. Should be sainitized before exporting. Expected:
	 *                          {
	 *                              $prefix_mask_1 => {
	 *                                  $local_path_1_1 => $remote_path_1_1,
	 *                                  $local_path_1_n => $remote_path_1_n
	 *                              },
	 *                              $prefix_mask_n => {
	 *                                  $local_path_n_1 => $remote_path_n_1,
	 *                                  $local_path_n_n => $remote_path_n_n
	 *                              },
	 *                          }
	 *
	 * @return bool|array True if files collection is empty or array with blobs and put operation responses.
	 */
	// @formatter:on
	public function put_blobs( $container, array $files = array() ) {
		if ( empty( $files ) ) {
			return true;
		}

		$all_contents = array();
		foreach ( $files as $group_contents ) {
			$all_contents += $group_contents;
		}

		foreach ( $all_contents as $local_path => &$remote_path ) {
			$remote_path = $this->put_blob( $container, $local_path, $remote_path );
		}

		return $all_contents;
	}

	/**
	 * Put blob on Azure Storage account.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container                Container name.
	 * @param string $local_path               Local path.
	 * @param string $remote_path              Remote path.
	 * @param bool   $force_direct_file_access Whether to force direct file access.
	 *
	 * @return bool|string|WP_Error Newly put blob URI or WP_Error|false on failure.
	 */
	public function put_blob( $container, $local_path, $remote_path, $force_direct_file_access = false ) {
		$container  = trailingslashit( $container );
		$query_args = array();
		$headers    = apply_filters( 'azure_blob_put_blob_headers', array() );

		// overwrite blob type.
		$headers[ self::API_HEADER_BLOB_TYPE ] = self::APPEND_BLOB_TYPE;

		$result = $this->_send_request( 'PUT', $query_args, $headers, '', $container . $remote_path );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$contents_provider = new Windows_Azure_File_Contents_Provider( $local_path, null, $force_direct_file_access );
		$is_valid          = $contents_provider->is_valid();
		if ( ! $is_valid || is_wp_error( $is_valid ) ) {
			return $is_valid;
		}
		do {
			$chunk = $contents_provider->get_chunk();
			if ( $chunk ) {
				$result = $this->_append_blob( $container, $remote_path, $chunk );
			}
		} while ( false !== $chunk && true === $result );

		$contents_provider->close();

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $this->_build_api_endpoint_url( $container . $remote_path );
	}

	/**
	 * Append blob operation.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container   Container.
	 * @param string $remote_path Remote path.
	 * @param string $content     Content to append.
	 *
	 * @return bool|WP_Error True on success or WP_Error on failure.
	 */
	protected function _append_blob( $container, $remote_path, $content ) {
		$container  = trailingslashit( $container );
		$query_args = array(
			'comp' => 'appendblock',
		);
		$headers    = apply_filters( 'azure_blob_append_blob_headers', array(), $container, $remote_path, $content, $this );
		$result     = $this->_send_request( 'PUT', $query_args, $headers, $content, $container . $remote_path );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return true;
	}

	/**
	 * Send REST request and return response.
	 *
	 * @since 4.0.0
	 *
	 * @param string       $method     HTTP verb.
	 * @param array        $query_args Request query args.
	 * @param array        $headers    Request headers.
	 * @param string|array $body       Request body.
	 * @param string       $path       REST API endpoint path.
	 *
	 * @return array|WP_Error Response structure on success or WP_Error on failure.
	 */
	protected function _send_request( $method, array $query_args = array(), array $headers = array(), $body = '', $path = '' ) {

		$query_args = wp_parse_args( $query_args, array(
			'timeout' => apply_filters( 'azure_blob_operation_timeout', self::API_REQUEST_TIMEOUT ),
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
			self::API_HEADER_MS_DATE    => get_gmt_from_date( current_time( 'mysql', 0 ), 'D, d M Y H:i:s' ) . ' GMT',
			'Content-Length'            => strlen( $body ) > 0 ? strlen( $body ) : null,
			'Content-Type'              => 'text/plain',
		) );

		// Add this filter to be able to inject authorization header.
		add_filter( 'http_request_args', array( $this, 'inject_authorization_header' ), PHP_INT_MAX, 2 );

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
		remove_filter( 'http_request_args', array( $this, 'inject_authorization_header' ), PHP_INT_MAX );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $result );
		if ( $response_code < 200 || $response_code > 299 ) {
			return new WP_Error( $response_code, wp_remote_retrieve_response_message( $result ) );
		}

		$body = wp_remote_retrieve_body( $result );
		if ( ! empty( $body ) ) {
			if ( ! function_exists( 'simplexml_load_string' ) ) {
				$message = __( "SimpleXML library hasn't been found. Please, check your PHP config.", 'windows-azure-storage' );
				return new WP_Error( 'simplexml', $message );
			}

			$xml_structure = simplexml_load_string( $body );
			return json_decode( json_encode( $xml_structure ), true );
		} else {
			return $result;
		}
	}

	/**
	 * Return Blob API endpoint URL.
	 *
	 * @since 4.0.0
	 *
	 * @param string $path URI path.
	 *
	 * @return string|WP_Error Endpoint URL or WP_Error on failure.
	 */
	protected function _build_api_endpoint_url( $path = '' ) {
		if ( empty( $this->_account_name ) ) {
			return new WP_Error( -1, __( 'Storage account name not set.', 'windows-azure-storage' ) );
		}

		$endpoint_url = sprintf( self::API_BLOB_ENDPOINT, $this->_account_name );

		return $endpoint_url . trim( $path );
	}

	/**
	 * Build canonicalized headers collection.
	 *
	 * @since 4.0.0
	 *
	 * @param array $headers Headers.
	 *
	 * @return array Canonicalized headers structure.
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
	 * @since 4.0.0
	 *
	 * @param string $url          Endpoint URL.
	 * @param string $account_name Storage account name.
	 *
	 * @return string Canonicalized resource string.
	 */
	protected function _build_canonicalized_resource( $url, $account_name ) {
		/** @var $parsed_url array */
		$parsed_url             = parse_url( $url );
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

	/**
	 * Sanitize remote paths. Check if given paths exist and append unique suffix when necessary.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container      Container to check paths against.
	 * @param string $prefix_group   Prefix check group.
	 * @param array  $group_contents Group contents.
	 *
	 * @return array|WP_Error Sanitized remote paths array or WP_Error on failure.
	 */
	protected function _sanitize_remote_paths( $container, $prefix_group, $group_contents ) {
		$remote_paths = array_flip( $group_contents );
		$blobs        = $this->list_blobs( $container, $prefix_group );

		if ( is_wp_error( $blobs ) ) {
			return $blobs;
		}

		$needs_sanitization = array();
		foreach ( $blobs as $blob ) {
			if ( isset( $remote_paths[ $blob['Name'] ] ) ) {

				$needs_sanitization[] = $blob['Name'];
				unset( $remote_paths[ $blob['Name'] ] );

				// quit early as $blob is an Iterator instance with lazy loading.
				if ( 0 === count( $remote_paths ) ) {
					break;
				}
			}
		}

		if ( empty( $needs_sanitization ) ) {
			return $group_contents;
		}

		$sanitized_names = array();
		$remote_paths    = array_flip( $group_contents );

		foreach ( $needs_sanitization as $item ) {
			$info     = pathinfo( $item );
			$dirname  = isset( $info['dirname'] ) ? ltrim( $info['dirname'], '.' ) : '';
			$new_name = ! empty( $dirname ) ? trailingslashit( $dirname ) : '';
			$new_name .= $info['filename'] . '-' . uniqid( '', false );
			$new_name .= isset( $info['extension'] ) ? '.' . $info['extension'] : '';
			$sanitized_names[ $item ] = $new_name;
		}

		foreach ( $sanitized_names as $original_path => $fixed_path ) {
			if ( ! isset( $remote_paths[ $original_path ] ) ) {
				continue;
			}

			$index                    = $remote_paths[ $original_path ];
			$group_contents[ $index ] = $fixed_path;
		}

		return $group_contents;
	}
}
