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
class Windows_Azure_WP_Filesystem_Direct extends WP_Filesystem_Direct {

	/**
	 * Whether this class supports stream reading from files.
	 *
	 * @since 4.0.0
	 *
	 * @var bool
	 */
	public $support_stream_reading = true;

	/**
	 * Returns file handler.
	 *
	 * @since 4.0.0
	 *
	 * @param string $file File path.
	 *
	 * @return resource File handler.
	 */
	public function get_handle( $file ) {
		return fopen( $file, 'r+' );
	}

	/**
	 * Close file handle.
	 *
	 * @since 4.0.0
	 *
	 * @param resource $handle File handle.
	 *
	 * @return void
	 */
	public function close_handle( $handle ) {
		fclose( $handle );
	}

	/**
	 * Read chunk size from the file.
	 *
	 * @since 4.0.0
	 *
	 * @param resource $handle File handler.
	 * @param int      $length Chunk size.
	 *
	 * @return string Chunk data.
	 */
	public function read_chunk( $handle, $length ) {
		if ( feof( $handle ) ) {
			return false;
		} else {
			return fread( $handle, $length );
		}
	}

	/**
	 * Rewind file cursor to the beginning.
	 *
	 * @since 4.0.0
	 *
	 * @param resource $handle File handler.
	 *
	 * @return void
	 */
	public function rewind( $handle ) {
		fseek( $handle, 0 );
	}
}
