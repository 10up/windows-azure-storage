<?php
/**
 * Copyright (c) 2009 - 2011, RealDolmen
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of RealDolmen nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY RealDolmen ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL RealDolmen BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * @version    $Id: Storage.php 66505 2011-12-02 08:45:51Z unknown $
 */

/**
 * @see Microsoft_AutoLoader
 */
require_once dirname(__FILE__) . '/../AutoLoader.php';

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class Microsoft_WindowsAzure_Storage
{
	/**
	 * Development storage URLS
	 */
	const URL_DEV_BLOB      = "http://127.0.0.1:10000";
	const URL_DEV_QUEUE     = "http://127.0.0.1:10001";
	const URL_DEV_TABLE     = "http://127.0.0.1:10002";
	
	/**
	 * Live storage URLS
	 */
	const URL_CLOUD_BLOB    = "http://blob.core.windows.net";
	const URL_CLOUD_QUEUE   = "http://queue.core.windows.net";
	const URL_CLOUD_TABLE   = "http://table.core.windows.net";
	const URL_CLOUD_BLOB_HTTPS    = "ssl://blob.core.windows.net";
	const URL_CLOUD_QUEUE_HTTPS   = "ssl://queue.core.windows.net";
	const URL_CLOUD_TABLE_HTTPS   = "ssl://table.core.windows.net";
	
	/**
	 * Resource types
	 */
	const RESOURCE_UNKNOWN     = "unknown";
	const RESOURCE_CONTAINER   = "c";
	const RESOURCE_BLOB        = "b";
	const RESOURCE_TABLE       = "t";
	const RESOURCE_ENTITY      = "e";
	const RESOURCE_QUEUE       = "q";
	
	/**
	 * HTTP header prefixes
	 */
	const PREFIX_PROPERTIES      = "x-ms-prop-";
	const PREFIX_METADATA        = "x-ms-meta-";
	const PREFIX_STORAGE_HEADER  = "x-ms-";
	
	/**
	 * Protocols
	 */
	const PROTOCOL_HTTP  = 'http://';
	const PROTOCOL_HTTPS = 'https://';
	const PROTOCOL_SSL   = 'ssl://';
	
	/**
	 * Current API version
	 * 
	 * @var string
	 */
	protected $_apiVersion = '2009-09-19';
	
	/**
	 * Storage protocol
	 *
	 * @var string
	 */
	protected $_protocol = 'http://';
	
	/**
	 * Storage host name
	 *
	 * @var string
	 */
	protected $_host = '';
	
	/**
	 * Account name for Windows Azure
	 *
	 * @var string
	 */
	protected $_accountName = '';
	
	/**
	 * Account key for Windows Azure
	 *
	 * @var string
	 */
	protected $_accountKey = '';
	
	/**
	 * Use path-style URI's
	 *
	 * @var boolean
	 */
	protected $_usePathStyleUri = false;
	
	/**
	 * Microsoft_WindowsAzure_Credentials_CredentialsAbstract instance
	 *
	 * @var Microsoft_WindowsAzure_Credentials_CredentialsAbstract
	 */
	protected $_credentials = null;
	
	/**
	 * Microsoft_WindowsAzure_RetryPolicy_RetryPolicyAbstract instance
	 * 
	 * @var Microsoft_WindowsAzure_RetryPolicy_RetryPolicyAbstract
	 */
	protected $_retryPolicy = null;
	
	/**
	 * Microsoft_Http_Client channel used for communication with REST services
	 * 
	 * @var Microsoft_Http_Client
	 */
	protected $_httpClientChannel = null;
	
	/**
	 * Use proxy?
	 * 
	 * @var boolean
	 */
	protected $_useProxy = false;
	
	/**
	 * Proxy url
	 * 
	 * @var string
	 */
	protected $_proxyUrl = '';
	
	/**
	 * Proxy port
	 * 
	 * @var int
	 */
	protected $_proxyPort = 80;
	
	/**
	 * Proxy credentials
	 * 
	 * @var string
	 */
	protected $_proxyCredentials = '';
	
	/**
	 * Creates a new Microsoft_WindowsAzure_Storage instance
	 *
	 * @param string $host Storage host name
	 * @param string $accountName Account name for Windows Azure
	 * @param string $accountKey Account key for Windows Azure
	 * @param boolean $usePathStyleUri Use path-style URI's
	 * @param Microsoft_WindowsAzure_RetryPolicy_RetryPolicyAbstract $retryPolicy Retry policy to use when making requests
	 */
	public function __construct(
		$host = self::URL_DEV_BLOB,
		$accountName = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::DEVSTORE_ACCOUNT,
		$accountKey = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::DEVSTORE_KEY,
		$usePathStyleUri = false,
		Microsoft_WindowsAzure_RetryPolicy_RetryPolicyAbstract $retryPolicy = null
	) {
		if (strpos($host, self::PROTOCOL_HTTP) !== false) {
			$this->_protocol = self::PROTOCOL_HTTP;
			$this->_host = str_replace(self::PROTOCOL_HTTP, '', $host);
		} else if (strpos($host, self::PROTOCOL_HTTPS) !== false) {
			$this->_protocol = self::PROTOCOL_HTTPS;
			$this->_host = str_replace(self::PROTOCOL_HTTPS, '', $host);
		} else if (strpos($host, self::PROTOCOL_SSL) !== false) {
			$this->_protocol = self::PROTOCOL_SSL;
			$this->_host = str_replace(self::PROTOCOL_SSL, '', $host);
		} else {
			$this->_protocol = self::PROTOCOL_HTTP;
			$this->_host = $host;
		}
		$this->_accountName = $accountName;
		$this->_accountKey = $accountKey;
		$this->_usePathStyleUri = $usePathStyleUri;

		// Using local storage?
		if (!$this->_usePathStyleUri
			&& ($this->_protocol . $this->_host == self::URL_DEV_BLOB
				|| $this->_protocol . $this->_host == self::URL_DEV_QUEUE
				|| $this->_protocol . $this->_host == self::URL_DEV_TABLE)
		) {
			// Local storage
			$this->_usePathStyleUri = true;
		}
		
		if (is_null($this->_credentials)) {
		    $this->_credentials = new Microsoft_WindowsAzure_Credentials_SharedKey(
		    	$this->_accountName, $this->_accountKey, $this->_usePathStyleUri);
		}
		
		$this->_retryPolicy = $retryPolicy;
		if (is_null($this->_retryPolicy)) {
		    $this->_retryPolicy = Microsoft_WindowsAzure_RetryPolicy_RetryPolicyAbstract::noRetry();
		}
		
		// Setup default Microsoft_Http_Client channel
		$options = array(
			'adapter' => 'Microsoft_Http_Client_Adapter_Proxy'
		);
		if (function_exists('curl_init')) {
			// Set cURL options if cURL is used afterwards
			$options['curloptions'] = array(
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_TIMEOUT => 120,
			);
		}
		$this->_httpClientChannel = new Microsoft_Http_Client(null, $options);
	}
	
	/**
	 * Set the HTTP client channel to use
	 * 
	 * @param Microsoft_Http_Client_Adapter_Interface|string $adapterInstance Adapter instance or adapter class name.
	 */
	public function setHttpClientChannel($adapterInstance = 'Microsoft_Http_Client_Adapter_Proxy')
	{
		$this->_httpClientChannel->setAdapter($adapterInstance);
	}
	
    /**
     * Retrieve HTTP client channel
     * 
     * @return Microsoft_Http_Client_Adapter_Interface
     */
    public function getHttpClientChannel()
    {
        return $this->_httpClientChannel;
    }
	
	/**
	 * Set retry policy to use when making requests
	 *
	 * @param Microsoft_WindowsAzure_RetryPolicy_RetryPolicyAbstract $retryPolicy Retry policy to use when making requests
	 */
	public function setRetryPolicy(Microsoft_WindowsAzure_RetryPolicy_RetryPolicyAbstract $retryPolicy = null)
	{
		$this->_retryPolicy = $retryPolicy;
		if (is_null($this->_retryPolicy)) {
		    $this->_retryPolicy = Microsoft_WindowsAzure_RetryPolicy_RetryPolicyAbstract::noRetry();
		}
	}
	
	/**
	 * Set proxy
	 * 
	 * @param boolean $useProxy         Use proxy?
	 * @param string  $proxyUrl         Proxy URL
	 * @param int     $proxyPort        Proxy port
	 * @param string  $proxyCredentials Proxy credentials
	 */
	public function setProxy($useProxy = false, $proxyUrl = '', $proxyPort = 80, $proxyCredentials = '')
	{
	    $this->_useProxy         = $useProxy;
	    $this->_proxyUrl         = $proxyUrl;
	    $this->_proxyPort        = $proxyPort;
	    $this->_proxyCredentials = $proxyCredentials;
	    
	    if ($this->_useProxy) {
	    	$credentials = explode(':', $this->_proxyCredentials);
	    	
	    	$this->_httpClientChannel->setConfig(array(
				'proxy_host' => $this->_proxyUrl,
	    		'proxy_port' => $this->_proxyPort,
	    		'proxy_user' => $credentials[0],
	    		'proxy_pass' => $credentials[1],
	    	));
	    } else {
			$this->_httpClientChannel->setConfig(array(
				'proxy_host' => '',
	    		'proxy_port' => 8080,
	    		'proxy_user' => '',
	    		'proxy_pass' => '',
	    	));
	    }
	}
	
	/**
	 * Returns the Windows Azure account name
	 * 
	 * @return string
	 */
	public function getAccountName()
	{
		return $this->_accountName;
	}
	
	/**
	 * Get base URL for creating requests
	 *
	 * @return string
	 */
	public function getBaseUrl()
	{
		if ($this->_usePathStyleUri) {
			return $this->_protocol . $this->_host . '/' . $this->_accountName;
		} else {
			return $this->_protocol . $this->_accountName . '.' . $this->_host;
		}
	}
	
	/**
	 * Set Microsoft_WindowsAzure_Credentials_CredentialsAbstract instance
	 * 
	 * @param Microsoft_WindowsAzure_Credentials_CredentialsAbstract $credentials Microsoft_WindowsAzure_Credentials_CredentialsAbstract instance to use for request signing.
	 */
	public function setCredentials(Microsoft_WindowsAzure_Credentials_CredentialsAbstract $credentials)
	{
	    $this->_credentials = $credentials;
	    $this->_credentials->setAccountName($this->_accountName);
	    $this->_credentials->setAccountkey($this->_accountKey);
	    $this->_credentials->setUsePathStyleUri($this->_usePathStyleUri);
	}
	
	/**
	 * Get Microsoft_WindowsAzure_Credentials_CredentialsAbstract instance
	 * 
	 * @return Microsoft_WindowsAzure_Credentials_CredentialsAbstract
	 */
	public function getCredentials()
	{
	    return $this->_credentials;
	}
	
	/**
	 * Returns the properties of a storage service. 
	 * Service properties include Logging (no connection with the logging components of this SDK) 
	 * and Metrics details.
	 * Returned strucutre:
	 * array(
	 *   '<Area>' => array('<Property>' => string|array)
	 * );
	 * <Area> can be: Logging, Metrics or other service areas.
	 * 
	 * @return array
	 */
	public function getServiceProperties()
	{
		// Perform request
		$response = $this->_performRequest('/', '?restype=service&comp=properties');
		if (!$response->isSuccessful()) {
			throw new Microsoft_WindowsAzure_Exception($this->_getErrorMessage(
				$response, 'Cannot obtain service properties.'
			));
		}
		
		// turn the response from a SimpleXML object to an array
		$parsedResponse = $this->_parseResponse($response);
		
		$returnArray = array();
		foreach ($parsedResponse as $key => $propertyAsXmlNode) {
			$returnArray[$key] = $this->_parseServicePropertyResponseNode($propertyAsXmlNode);
		}
		
		return $returnArray;
	}
	
	/**
	 * Parses a node of the service property response. A node is a direct child of the root
	 * element (StorageServiceProperties). 
	 * Returns the array representation of that node.
	 * 
	 * @param $responseNode
	 * @return array
	 */
	protected function _parseServicePropertyResponseNode($responseNode)
	{
		if ($responseNode->count() > 0) {
			$returnArray = array();
			foreach ($responseNode as $key => $childNode) {
				$returnArray[$key] = $this->_parseServicePropertyResponseNode($childNode);
			}
			return $returnArray;
		} else {
			return (string)$responseNode;
		}
	}
	
	/**
	 * Perform request using Microsoft_Http_Client channel
	 *
	 * @param string $path Path
	 * @param array $query Query parameters
	 * @param string $httpVerb HTTP verb the request will use
	 * @param array $headers x-ms headers to add
	 * @param boolean $forTableStorage Is the request for table storage?
	 * @param mixed $rawData Optional RAW HTTP data to be sent over the wire
	 * @param string $resourceType Resource type
	 * @param string $requiredPermission Required permission
	 * @return Microsoft_Http_Response
	 */
	protected function _performRequest(
		$path = '/',
		$query = array(),
		$httpVerb = Microsoft_Http_Client::GET,
		$headers = array(),
		$forTableStorage = false,
		$rawData = null,
		$resourceType = Microsoft_WindowsAzure_Storage::RESOURCE_UNKNOWN,
		$requiredPermission = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::PERMISSION_READ
	) {
	    // Clean path
		if (strpos($path, '/') !== 0) {
			$path = '/' . $path;
		}
			
		// Clean headers
		if (is_null($headers)) {
		    $headers = array();
		}
		
		// Ensure cUrl will also work correctly:
		//  - disable Content-Type if required
		//  - disable Expect: 100 Continue
		if (!isset($headers["Content-Type"])) {
			$headers["Content-Type"] = '';
		}
		$headers["Expect"]= '';

		// Add version header
		$headers['x-ms-version'] = $this->_apiVersion;
		    
		// Generate URL
		$path = str_replace(' ', '%20', $path);
		$requestUrl = $this->getBaseUrl() . $path;
		if (count($query) > 0) {
			$queryString = '';
			foreach ($query as $key => $value) {
				$queryString .= ($queryString ? '&' : '?') . rawurlencode($key) . '=' . rawurlencode($value);
			}			
			$requestUrl .= $queryString;
		}
		
		// Sign request
		$requestUrl     = $this->_credentials
						  ->signRequestUrl($requestUrl, $resourceType, $requiredPermission);
		$requestHeaders = $this->_credentials
						  ->signRequestHeaders($httpVerb, $path, $query, $headers, $forTableStorage, $resourceType, $requiredPermission, $rawData);

		// Prepare request 
		$this->_httpClientChannel->resetParameters(true);
		$this->_httpClientChannel->setUri($requestUrl);
		$this->_httpClientChannel->setHeaders($requestHeaders);
		$this->_httpClientChannel->setRawData($rawData);
				
		// Execute request
		$response = $this->_retryPolicy->execute(
		    array($this->_httpClientChannel, 'request'),
		    array($httpVerb)
		);
		
		return $response;
	}
	
	/** 
	 * Parse result from Microsoft_Http_Response
	 *
	 * @param Microsoft_Http_Response $response Response from HTTP call
	 * @return object
	 * @throws Microsoft_WindowsAzure_Exception
	 */
	protected function _parseResponse(Microsoft_Http_Response $response = null)
	{
		if (is_null($response)) {
			throw new Microsoft_WindowsAzure_Exception('Response should not be null.');
		}
		
        $xml = @simplexml_load_string($response->getBody());
        
        if ($xml !== false) {
            // Fetch all namespaces 
            $namespaces = array_merge($xml->getNamespaces(true), $xml->getDocNamespaces(true)); 
            
            // Register all namespace prefixes
            foreach ($namespaces as $prefix => $ns) { 
                if ($prefix != '') {
                    $xml->registerXPathNamespace($prefix, $ns);
                } 
            } 
        }
        
        return $xml;
	}
	
	/**
	 * Generate metadata headers
	 * 
	 * @param array $metadata
	 * @return HTTP headers containing metadata
	 */
	protected function _generateMetadataHeaders($metadata = array())
	{
		// Validate
		if (!is_array($metadata)) {
			return array();
		}
		
		// Return headers
		$headers = array();
		foreach ($metadata as $key => $value) {
			if (strpos($value, "\r") !== false || strpos($value, "\n") !== false) {
				throw new Microsoft_WindowsAzure_Exception('Metadata cannot contain newline characters.');
			}
			
			if (!self::isValidMetadataName($key)) {
		    	throw new Microsoft_WindowsAzure_Exception('Metadata name does not adhere to metadata naming conventions. See http://msdn.microsoft.com/en-us/library/aa664670(VS.71).aspx for more information.');
			}
			
		    $headers["x-ms-meta-" . strtolower($key)] = $value;
		}
		return $headers;
	}
	
	/**
	 * Parse metadata headers
	 * 
	 * @param array $headers HTTP headers containing metadata
	 * @return array
	 */
	protected function _parseMetadataHeaders($headers = array())
	{
		// Validate
		if (!is_array($headers)) {
			return array();
		}
		
		// Return metadata
		$metadata = array();
		foreach ($headers as $key => $value) {
		    if (substr(strtolower($key), 0, 10) == "x-ms-meta-") {
		        $metadata[str_replace("x-ms-meta-", '', strtolower($key))] = $value;
		    }
		}
		return $metadata;
	}
	
	/**
	 * Parse metadata XML
	 * 
	 * @param SimpleXMLElement $parentElement Element containing the Metadata element.
	 * @return array
	 */
	protected function _parseMetadataElement($element = null)
	{
		// Metadata present?
		if (!is_null($element) && isset($element->Metadata) && !is_null($element->Metadata)) {
			return get_object_vars($element->Metadata);
		}

		return array();
	}
	
	/**
	 * Generate ISO 8601 compliant date string in UTC time zone
	 * 
	 * @param int $timestamp
	 * @return string
	 */
	public function isoDate($timestamp = null) 
	{        
	    $tz = @date_default_timezone_get();
	    @date_default_timezone_set('UTC');
	    
	    if (is_null($timestamp)) {
	        $timestamp = time();
	    }
	        
	    $returnValue = str_replace('+00:00', '.0000000Z', @date('c', $timestamp));
	    @date_default_timezone_set($tz);
	    return $returnValue;
	}
	
	/**
	 * Is valid metadata name?
	 *
	 * @param string $metadataName Metadata name
	 * @return boolean
	 */
    public static function isValidMetadataName($metadataName = '')
    {
        if (preg_match("/^[a-zA-Z0-9_@][a-zA-Z0-9_]*$/", $metadataName) === 0) {
            return false;
        }
    
        if ($metadataName == '') {
            return false;
        }

        return true;
    }
}
