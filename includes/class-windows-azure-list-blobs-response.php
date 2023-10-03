<?php

use MicrosoftAzure\Storage\Blob\Models\ListBlobsResult;

/**
 * Microsoft Azure Storage REST API list blobs response.
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
class Windows_Azure_List_Blobs_Response extends Windows_Azure_Generic_List_Response {
	/**
	 * Windows_Azure_List_Containers_Response constructor.
	 *
	 * @param ListBlobsResult $blobs Array of blobs.
	 * @param Windows_Azure_Rest_Api_Client $client REST client.
	 * @param int $max_results Max results per one request.
	 * @param string $path Container name.
	 *
	 * @since 4.0.0
	 *
	 */
	public function __construct( ListBlobsResult $blobs, $prefix = '', $max_results = Windows_Azure_Rest_Api_Client::API_REQUEST_BULK_SIZE, $path = '' ) {
		parent::__construct( $blobs, $prefix, $max_results, $path );

		$this->_items = $blobs->getBlobs();
	}

	/**
	 * Lazy loading of blobs.
	 *
	 * @param string $prefix Search prefix.
	 * @param int $max_results Max API listing results.
	 * @param string $next_marker Offset marker.
	 * @param string $path Container name.
	 *
	 * @return WP_Error|Windows_Azure_List_Blobs_Response Blobs list iterator class or WP_Error on failure.
	 * @since 4.0.0
	 *
	 */
	protected function _list_items( $prefix, $max_results, $next_marker, $path ) {
		return $this->_rest_client->list_blobs( $path, $prefix, $max_results, $next_marker );
	}
}
