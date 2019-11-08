<?php

/**
 * Microsoft Azure Storage REST API list containers response.
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
abstract class Windows_Azure_Generic_List_Response implements Iterator {

	/**
	 * Current position.
	 *
	 * @since 4.0.0
	 *
	 * @var int
	 */
	protected $_position = 0;

	/**
	 * List of containers.
	 *
	 * @since 4.0.0
	 *
	 * @var array
	 */
	protected $_items;

	/**
	 * REST client.
	 *
	 * @since 4.0.0
	 *
	 * @var Windows_Azure_Rest_Api_Client
	 */
	protected $_rest_client;

	/**
	 * Max containers results per one request.
	 *
	 * @since 4.0.0
	 *
	 * @var int
	 */
	protected $_max_results;

	/**
	 * Next collection marker.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	protected $_next_marker;

	/**
	 * Search prefix.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	protected $_prefix;

	/**
	 * Generic path value.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	protected $_path;

	/**
	 * Windows_Azure_List_Containers_Response constructor.
	 *
	 * @since 4.0.0
	 *
	 * @param array                         $rest_response Rest response.
	 * @param Windows_Azure_Rest_Api_Client $client        REST client.
	 * @param string                        $prefix        Search prefix.
	 * @param int                           $max_results   Max results per one request.
	 * @param string                        $path          Optional request path.
	 */
	public function __construct( array $rest_response, Windows_Azure_Rest_Api_Client $client, $prefix = '', $max_results = Windows_Azure_Rest_Api_Client::API_REQUEST_BULK_SIZE, $path = '' ) {
		$this->_position    = 0;
		$this->_items       = array();
		$this->_rest_client = $client;
		$this->_max_results = $max_results;
		$this->_prefix      = $prefix;
		$this->_path        = $path;

		if ( isset( $rest_response['NextMarker'] ) && ! empty( $rest_response['NextMarker'] ) ) {
			$this->_next_marker = $rest_response['NextMarker'];
		}
	}


	/**
	 * Return the current element
	 *
	 * @since 4.0.0
	 *
	 * @return mixed Can return any type.
	 */
	public function current() {
		if ( ! isset( $this->_items[ $this->_position ] ) ) {
			return null;
		} else {
			return $this->_items[ $this->_position ];
		}
	}

	/**
	 * Move forward to next element
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function next() {
		if ( ! empty( $this->_next_marker ) && ( ( count( $this->_items ) - 1 ) === $this->_position ) ) {
			$lazy_loaded = $this->_list_items( $this->_prefix, $this->_max_results, $this->_next_marker, $this->_path );
			if ( $lazy_loaded instanceof Windows_Azure_Generic_List_Response ) {
				$this->_items       = array_merge( $this->_items, $lazy_loaded->get_all() );
				$this->_next_marker = $lazy_loaded->get_next_marker();
				unset( $lazy_loaded );
			}
		}
		++$this->_position;
	}

	/**
	 * Return the key of the current element
	 *
	 * @since 4.0.0
	 *
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key() {
		return $this->_position;
	}

	/**
	 * Checks if current position is valid
	 *
	 * @since 4.0.0
	 *
	 * @return boolean The return value will be casted to boolean and then evaluated. Returns true on success or false on failure.
	 */
	public function valid() {
		return isset( $this->_items[ $this->_position ] );
	}

	/**
	 * Rewind the Iterator to the first element
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function rewind() {
		$this->_position = 0;
	}

	/**
	 * Return all items.
	 *
	 * @since 4.0.0
	 *
	 * @return array Array containing all items.
	 */
	public function get_all() {
		return $this->_items;
	}

	/**
	 * Return next marker.
	 *
	 * @since 4.0.0
	 *
	 * @return null|string Next portion of data marker.
	 */
	public function get_next_marker() {
		return $this->_next_marker;
	}

	/**
	 * Whether collection is empty or not.
	 *
	 * @since 4.0.0
	 *
	 * @return bool True or false.
	 */
	public function is_empty() {
		return 0 === count( $this->_items );
	}

	/**
	 * Empty stub for lazy loading of items.
	 *
	 * @since 4.0.0
	 *
	 * @param string $prefix      Search prefix.
	 * @param int    $max_results Max API listing results.
	 * @param string $next_marker Offset marker.
	 * @param string $path        Optional request path.
	 *
	 * @return null
	 */
	protected function _list_items( $prefix, $max_results, $next_marker, $path ) {
		return null;
	}
}
