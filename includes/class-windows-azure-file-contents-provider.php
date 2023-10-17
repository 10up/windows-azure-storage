<?php

/**
 * Microsoft Azure Storage file contents provider.
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
class Windows_Azure_File_Contents_Provider {

	/**
	 * Chunk size.
	 *
	 * @since 4.0.0
	 *
	 * @const int
	 */
	const CHUNK_SIZE = 4194304;

	/**
	 * Max Azure REST API request length.
	 *
	 * @since 4.0.0
	 *
	 * @const int
	 */
	const MAX_CHUNK_SIZE = 4194304;

	/**
	 * File path.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	protected $_file_path;

	/**
	 * Whether class file is a file and is readable.
	 *
	 * @since 4.0.0
	 *
	 * @var bool
	 */
	protected $_is_valid;

	/**
	 * File system access.
	 *
	 * @since 4.0.0
	 *
	 * @var bool|WP_Filesystem_Base
	 */
	protected $_wp_filesystem;

	/**
	 * Chunk size.
	 *
	 * @since 4.0.0
	 *
	 * @var int
	 */
	protected $_chunk_size;

	/**
	 * Current stream position.
	 *
	 * @since 4.0.0
	 *
	 * @var int
	 */
	protected $_position;

	/**
	 * File contents for non-stream providers.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	protected $_contents;

	/**
	 * File handle.
	 *
	 * @since 4.0.0
	 *
	 * @var resource
	 */
	protected $_handle;

	/**
	 * Whether WP Filesystem can read from the stream or not.
	 *
	 * @since 4.0.0
	 *
	 * @var bool
	 */
	protected $_stream_reader;

	/**
	 * Windows_Azure_File_Contents_Provider constructor.
	 *
	 * @since 4.0.0
	 *
	 * @param string $file_path  File path.
	 * @param int    $chunk_size Chunk size.
	 */
	public function __construct( $file_path, $chunk_size = self::CHUNK_SIZE ) {
		if ( null === $chunk_size ) {
			$chunk_size = self::CHUNK_SIZE;
		}

		if ( DIRECTORY_SEPARATOR !== $file_path[0] && ( isset( $file_path[1] ) && ':' !== $file_path[1] ) ) {
			$upload_dir = \Windows_Azure_Helper::wp_upload_dir();
			$file_path  = $upload_dir['uploads'] . DIRECTORY_SEPARATOR . $file_path;
		}

		$this->_file_path     = $file_path;
		$force_direct_access  = is_resource( $file_path );
		$this->_wp_filesystem = Windows_Azure_Filesystem_Access_Provider::get_provider( $force_direct_access );
		$this->_chunk_size    = $chunk_size <= self::MAX_CHUNK_SIZE ? (int) $chunk_size : self::MAX_CHUNK_SIZE;
		$this->_position      = 0;
		$this->_stream_reader = $this->_wp_filesystem instanceof WP_Filesystem_Base && isset( $this->_wp_filesystem->support_stream_reading );
	}

	/**
	 * Whether class file is valid.
	 *
	 * @since 4.0.0
	 *
	 * @return bool|WP_Error Whether this class provides access to file or not.
	 */
	public function is_valid() {
		if ( ! $this->_wp_filesystem ) {
			return new WP_Error( -1, __( 'Access to WordPress filesystem has not been granted.', 'windows-azure-storage' ) );
		}

		if ( null === $this->_is_valid ) {
			$this->_is_valid = $this->_wp_filesystem->is_file( $this->_file_path ) && $this->_wp_filesystem->is_readable( $this->_file_path );
		}

		return $this->_is_valid;
	}

	/**
	 * Rewind stream to the beginning.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function rewind() {
		$this->_position = 0;
		if ( $this->_stream_reader && null !== $this->_handle ) {
			$this->_wp_filesystem->rewind( $this->_handle );
		}
	}

	/**
	 * Get chunk contents.
	 *
	 * @since 4.0.0
	 *
	 * @return string|bool Chunk data or false if end of the stream.
	 */
	public function get_chunk() {
		if ( 0 === $this->_position ) {
			if ( ! $this->_stream_reader ) {
				$this->_contents = $this->_wp_filesystem->get_contents( $this->_file_path );
			} else {
				$this->_handle = $this->_wp_filesystem->get_handle( $this->_file_path );
			}
		}

		if ( $this->_stream_reader ) {
			$chunk = $this->_wp_filesystem->read_chunk( $this->_handle, $this->_chunk_size );
		} else {
			$start = self::CHUNK_SIZE * $this->_position;
			$chunk = substr( $this->_contents, $start, $this->_chunk_size );
			if ( empty( $chunk ) ) {
				$chunk = false;
			}
		}

		$this->_position++;

		return $chunk;
	}

	/**
	 * Close stream handlers if possible.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function close() {
		if ( $this->_stream_reader ) {
			$this->_wp_filesystem->close_handle( $this->_handle );
			$this->_handle = null;
		}
	}

	/**
	 * Return file path for given file content path.
	 *
	 * @return string
	 *
	 * @since 4.4.0
	 */
	public function get_file_path() {
		return $this->_file_path;
	}
}
