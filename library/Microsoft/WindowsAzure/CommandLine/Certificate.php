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
 * @package    Microsoft_Console
 * @subpackage Exception
 * @version    $Id: Exception.php 55733 2011-01-03 09:17:16Z unknown $
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */

/**
 * @see Microsoft_AutoLoader
 */
require_once dirname(__FILE__) . '/../../AutoLoader.php';

/**
 * Certificate commands
 * 
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure_CommandLine
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * 
 * @command-handler certificate
 * @command-handler-description Windows Azure Certificate commands
 * @command-handler-header Windows Azure SDK for PHP
 * @command-handler-header Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @command-handler-footer Note: Parameters that are common across all commands can be stored 
 * @command-handler-footer in two dedicated environment variables.
 * @command-handler-footer - SubscriptionId: The Windows Azure Subscription Id to operate on.
 * @command-handler-footer - Certificate The Windows Azure .cer Management Certificate.
 * @command-handler-footer 
 * @command-handler-footer All commands support the --ConfigurationFile or -F parameter.
 * @command-handler-footer The parameter file is a simple INI file carrying one parameter
 * @command-handler-footer value per line. It accepts the same parameters as one can
 * @command-handler-footer use from the command line command.
 */
class Microsoft_WindowsAzure_CommandLine_Certificate
	extends Microsoft_Console_Command
{	
	/**
	 * List certificates for a specified hosted service in a specified subscription.
	 * 
	 * @command-name List
	 * @command-description List certificates for a specified hosted service in a specified subscription.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --ServiceName|-sn Required. The name of the hosted service.
	 * @command-example List certificates for service name "phptest":
	 * @command-example List -sid="<your_subscription_id>" -cert="mycert.pem" -sn="phptest"
	 */
	public function listCertificatesCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$result = $client->listCertificates($serviceName);

		if (count($result) == 0) {
			echo 'No data to display.';
		}
		foreach ($result as $object) {
			$this->_displayObjectInformation($object, array('Thumbprint', 'CertificateUrl', 'ThumbprintAlgorithm'));
		}
	}
	
	/**
	 * Add a certificate for a specified hosted service in a specified subscription.
	 * 
	 * @command-name Add
	 * @command-description Add a certificate for a specified hosted service in a specified subscription.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --ServiceName|-sn Required. The name of the hosted service.
	 * @command-parameter-for $certificateLocation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --CertificateLocation Required. Path to the .pfx certificate to be added.
	 * @command-parameter-for $certificatePassword Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --CertificatePassword Required. The password for the certificate that will be added.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 * @command-example Add certificates for service name "phptest":
	 * @command-example Add -sid="<your_subscription_id>" -cert="mycert.pem" -sn="phptest" --CertificateLocation="cert.pfx" --CertificatePassword="certpassword"
	 */
	public function addCertificateCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $certificateLocation, $certificatePassword, $waitForOperation = false)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$client->addCertificate($serviceName, $certificateLocation, $certificatePassword, 'pfx');
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $serviceName;
	}
	
	/**
	 * Gets a certificate from a specified hosted service in a specified subscription.
	 * 
	 * @command-name Get
	 * @command-description Gets a certificate from a specified hosted service in a specified subscription.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --ServiceName|-sn Required. The name of the hosted service.
	 * @command-parameter-for $thumbprint Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --CertificateThumbprint Required. The certificate thumbprint for which to retrieve the certificate.
	 * @command-parameter-for $algorithm Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --CertificateAlgorithm Required. The certificate's algorithm.
	 * @command-example Get certificate for service name "phptest":
	 * @command-example Get -sid="<your_subscription_id>" -cert="mycert.pem" -sn="phptest" --CertificateThumbprint="<thumbprint>" --CertificateAlgorithm="sha1"
	 */
	public function getCertificateCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $thumbprint, $algorithm = "sha1")
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$result = $client->getCertificate($serviceName, $algorithm, $thumbprint);

		$this->_displayObjectInformation($result, array('Thumbprint', 'CertificateUrl', 'ThumbprintAlgorithm'));
	}
	
	/**
	 * Gets a certificate property from a specified hosted service in a specified subscription.
	 * 
	 * @command-name GetProperty
	 * @command-description Gets a certificate property from a specified hosted service in a specified subscription.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --ServiceName|-sn Required. The name of the hosted service.
	 * @command-parameter-for $thumbprint Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --CertificateThumbprint Required. The certificate thumbprint for which to retrieve the certificate.
	 * @command-parameter-for $algorithm Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --CertificateAlgorithm Required. The certificate's algorithm.
	 * @command-parameter-for $property Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Property|-prop Required. The property to retrieve for the certificate.
	 * @command-example Get certificate for service name "phptest":
	 * @command-example Get -sid="<your_subscription_id>" -cert="mycert.pem" -sn="phptest" --CertificateThumbprint="<thumbprint>" --CertificateAlgorithm="sha1"
	 */
	public function getCertificatePropertyCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $thumbprint, $algorithm = "sha1", $property)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$result = $client->getCertificate($serviceName, $algorithm, $thumbprint);

		printf("%s\r\n", $result->$property);
	}
	
	/**
	 * Deletes a certificate from a specified hosted service in a specified subscription.
	 * 
	 * @command-name Delete
	 * @command-description Deletes a certificate from a specified hosted service in a specified subscription.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --ServiceName|-sn Required. The name of the hosted service.
	 * @command-parameter-for $thumbprint Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --CertificateThumbprint Required. The certificate thumbprint for which to retrieve the certificate.
	 * @command-parameter-for $algorithm Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --CertificateAlgorithm Required. The certificate's algorithm.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 * @command-example Get certificate for service name "phptest":
	 * @command-example Get -sid="<your_subscription_id>" -cert="mycert.pem" -sn="phptest" --CertificateThumbprint="<thumbprint>" --CertificateAlgorithm="sha1"
	 */
	public function deleteCertificateCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $thumbprint, $algorithm = "sha1", $waitForOperation = false)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$client->deleteCertificate($serviceName, $algorithm, $thumbprint);
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $serviceName;
	}
}

Microsoft_Console_Command::bootstrap($_SERVER['argv']);