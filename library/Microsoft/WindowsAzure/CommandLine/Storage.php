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
 * Storage commands
 * 
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure_CommandLine
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * 
 * @command-handler storage
 * @command-handler-description Windows Azure Storage commands
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
class Microsoft_WindowsAzure_CommandLine_Storage
	extends Microsoft_Console_Command
{	
	/**
	 * List storage accounts for a specified subscription.
	 * 
	 * @command-name ListAccounts
	 * @command-description List storage accounts for a specified subscription.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-example List storage accounts for subscription:
	 * @command-example ListAccounts -sid="<your_subscription_id>" -cert="mycert.pem"
	 */
	public function listAccountsCommand($subscriptionId, $certificate, $certificatePassphrase)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$result = $client->listStorageAccounts();

		if (count($result) == 0) {
			echo 'No data to display.';
		}
		foreach ($result as $object) {
			$this->_displayObjectInformation($object, array('ServiceName', 'Url'));
		}
	}
	
	/**
	 * Get storage account properties.
	 * 
	 * @command-name GetProperties
	 * @command-description Get storage account properties.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $accountName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --AccountName Required. The storage account name to operate on.
	 * @command-example Get storage account properties for account "phptest":
	 * @command-example GetProperties -sid="<your_subscription_id>" -cert="mycert.pem"
	 * @command-example --AccountName="phptest"
	 */
	public function getPropertiesCommand($subscriptionId, $certificate, $certificatePassphrase, $accountName)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$result = $client->getStorageAccountProperties($accountName);
		
		$this->_displayObjectInformation($result, array('ServiceName', 'Label', 'AffinityGroup', 'Location'));
	}
	
	/**
	 * Get storage account property.
	 * 
	 * @command-name GetProperty
	 * @command-description Get storage account property.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $accountName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --AccountName Required. The storage account name to operate on.
	 * @command-parameter-for $property Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Property|-prop Required. The property to retrieve for the storage account.
	 * @command-example Get storage account property "Url" for account "phptest":
	 * @command-example GetProperty -sid="<your_subscription_id>" -cert="mycert.pem"
	 * @command-example --AccountName="phptest" --Property:Url
	 */
	public function getPropertyCommand($subscriptionId, $certificate, $certificatePassphrase, $accountName, $property)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$result = $client->getStorageAccountProperties($accountName);
		
		printf("%s\r\n", $result->$property);
	}
	
	/**
	 * Get storage account keys.
	 * 
	 * @command-name GetKeys
	 * @command-description Get storage account keys.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $accountName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --AccountName Required. The storage account name to operate on.
	 * @command-example Get storage account keys for account "phptest":
	 * @command-example GetKeys -sid="<your_subscription_id>" -cert="mycert.pem"
	 * @command-example --AccountName="phptest"
	 */
	public function getKeysCommand($subscriptionId, $certificate, $certificatePassphrase, $accountName)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$result = $client->getStorageAccountKeys($accountName);
		
		$this->_displayObjectInformation((object)array('Key' => 'primary', 'Value' => $result[0]), array('Key', 'Value'));
		$this->_displayObjectInformation((object)array('Key' => 'secondary', 'Value' => $result[1]), array('Key', 'Value'));
	}
	
	/**
	 * Get storage account key.
	 * 
	 * @command-name GetKey
	 * @command-description Get storage account key.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $accountName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --AccountName Required. The storage account name to operate on.
	 * @command-parameter-for $key Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Key|-k Optional. Specifies the key to regenerate (primary|secondary). If omitted, primary key is used as the default.
	 * @command-example Get primary storage account key for account "phptest":
	 * @command-example GetKey -sid="<your_subscription_id>" -cert="mycert.pem"
	 * @command-example --AccountName="phptest" -Key=primary
	 */
	public function getKeyCommand($subscriptionId, $certificate, $certificatePassphrase, $accountName, $key = 'primary')
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$result = $client->getStorageAccountKeys($accountName);
		
		if (strtolower($key) == 'secondary') {
			printf("%s\r\n", $result[1]);
		}
		printf("%s\r\n", $result[0]);
	}
	
	/**
	 * Regenerate storage account keys.
	 * 
	 * @command-name RegenerateKeys
	 * @command-description Regenerate storage account keys.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $accountName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --AccountName Required. The storage account name to operate on.
	 * @command-parameter-for $key Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Key|-k Optional. Specifies the key to regenerate (primary|secondary). If omitted, primary key is used as the default.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 * @command-example Regenerate secondary key for account "phptest":
	 * @command-example RegenerateKeys -sid="<your_subscription_id>" -cert="mycert.pem"
	 * @command-example --AccountName="phptest" -Key=secondary
	 */
	public function regenerateKeysCommand($subscriptionId, $certificate, $certificatePassphrase, $accountName, $key = 'primary', $waitForOperation = false)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$client->regenerateStorageAccountKey($accountName, $key);
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $accountName;
	}
	
	/**
	 * Create storage account.
	 * 
	 * @command-name Create
	 * @command-description Create storage account.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_StdIn --Name Required. The storage service account name.
	 * @command-parameter-for $label Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Label Required. A label for the storage service.
	 * @command-parameter-for $description Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Description Optional. A description for the storage service.
	 * @command-parameter-for $location Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Location Required if AffinityGroup is not specified. The location where the storage service will be created.
	 * @command-parameter-for $affinityGroup Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --AffinityGroup Required if Location is not specified. The name of an existing affinity group associated with this subscription.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 * @command-example Create storage service account in West Europe
	 * @command-example Create -p="phpazure" --Name="phptestsdk2" --Label="phptestsdk2" --Location="West Europe"
	 */
	public function createCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $label, $description, $location, $affinityGroup, $waitForOperation = false)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$client->createStorageAccount($serviceName, $label, $description, $location, $affinityGroup);
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $serviceName;
	}
	
	/**
	 * Update storage account.
	 * 
	 * @command-name Update
	 * @command-description Update storage account.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_StdIn --Name Required. The storage account name.
	 * @command-parameter-for $label Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Label Required. A label for the storage service.
	 * @command-parameter-for $description Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Description Optional. A description for the storage service.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 * @command-example Update storage service
	 * @command-example Update -p="phpazure" --Name="phptestsdk2" --Label="New label" --Description="Some description"
	 */
	public function updateCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $label, $description, $waitForOperation = false)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$client->updateStorageAccount($serviceName, $label, $description);
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $serviceName;
	}
	
	/**
	 * Delete storage account.
	 * 
	 * @command-name Delete
	 * @command-description Delete storage account.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_StdIn --Name Required. The storage account name.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 * @command-example Delete storage service
	 * @command-example Delete -p="phpazure" --Name="phptestsdk2"
	 */
	public function deleteCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $waitForOperation = false)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$client->deleteStorageAccount($serviceName);
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $serviceName;
	}
}

Microsoft_Console_Command::bootstrap($_SERVER['argv']);