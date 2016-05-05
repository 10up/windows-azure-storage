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
	 * Windows_Azure_Rest_Api_Client constructor.
	 *
	 * @param string $account_name Optional storage account name.
	 * @param string $access_key   Optional storage access key.
	 */
	public function __construct( $account_name = null, $access_key = null ) {
		$this->_account_name = $account_name;
		$this->_access_key   = $access_key;
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
		$this->_account_name = $account_name;
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


}
