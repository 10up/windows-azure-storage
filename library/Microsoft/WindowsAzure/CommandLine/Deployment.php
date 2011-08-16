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
 * Deployment commands
 * 
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure_CommandLine
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * 
 * @command-handler deployment
 * @command-handler-description Windows Azure Deployment commands
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
class Microsoft_WindowsAzure_CommandLine_Deployment
	extends Microsoft_Console_Command
{	
	/**
	 * Creates a deployment from a remote package file and service configuration.
	 * 
	 * @command-name CreateFromStorage
	 * @command-description Creates a deployment from a remote package file and service configuration.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Name Required. The hosted service account name to operate on.
	 * @command-parameter-for $deploymentName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --DeploymentName Required. The name for the deployment.
	 * @command-parameter-for $label Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Label Required. The label for the deployment.
	 * @command-parameter-for $staging Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Staging Host the service in the staging slot.
	 * @command-parameter-for $production Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Production Host the service in the staging slot.
	 * @command-parameter-for $packageUrl Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --PackageUrl Required. The remote location of the .cspkg file.
	 * @command-parameter-for $serviceConfigurationLocation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ServiceConfigLocation Required. The location of the .cspkg file.
	 * @command-parameter-for $startImmediately Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --StartImmediately Optional. Start the deployment after creation.
	 * @command-parameter-for $warningsAsErrors Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WarningsAsErrors Optional. Treat warnings as errors.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 * @command-example Create a deployment from a remote .cspkg:
	 * @command-example CreateFromStorage -sid="<your_subscription_id>" -cert="mycert.pem" --Name="hostedservicename" --DeploymentName="deploymentname"
	 * @command-example --Label="deploymentlabel" --Production
	 * @command-example --PackageUrl="http://acct.blob.core.windows.net/pkgs/service.cspkg"
	 * @command-example --ServiceConfigLocation=".\ServiceConfiguration.cscfg" --StartImmediately --WaitFor
	 */
	public function createFromStorageCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $deploymentName, $label, $staging = false, $production = false, $packageUrl, $serviceConfigurationLocation, $startImmediately = true, $warningsAsErrors = false, $waitForOperation = false)
	{
		$deploymentSlot = 'staging';
		if (!$staging && !$production) {
			throw new Microsoft_Console_Exception('Either --Staging or --Production should be specified.');
		}
		if ($production) {
			$deploymentSlot = 'production';
		}

		$client->createDeployment($serviceName, $deploymentSlot, $deploymentName, $label, $packageUrl, $serviceConfigurationLocation, $startImmediately, $warningsAsErrors);

		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $client->getLastRequestId();
	}
	
	/**
	 * Creates a deployment from a local package file and service configuration.
	 * 
	 * @command-name CreateFromLocal
	 * @command-description Creates a deployment from a local package file and service configuration.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Name Required. The hosted service account name to operate on.
	 * @command-parameter-for $deploymentName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --DeploymentName Required. The name for the deployment.
	 * @command-parameter-for $label Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Label Required. The label for the deployment.
	 * @command-parameter-for $staging Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Staging Host the service in the staging slot.
	 * @command-parameter-for $production Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Production Host the service in the staging slot.
	 * @command-parameter-for $packageLocation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_StdIn --PackageLocation Required. The location of the .cspkg file.
	 * @command-parameter-for $serviceConfigurationLocation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ServiceConfigLocation Required. The location of the .cspkg file.
	 * @command-parameter-for $storageAccount Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --StorageAccount Required. Storage account to use when creating the deployment.
	 * @command-parameter-for $startImmediately Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --StartImmediately Optional. Start the deployment after creation.
	 * @command-parameter-for $warningsAsErrors Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WarningsAsErrors Optional. Treat warnings as errors.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 * @command-example Create a deployment from a local .cspkg:
	 * @command-example CreateFromLocal -sid="<your_subscription_id>" -cert="mycert.pem" --Name="hostedservicename" --DeploymentName="deploymentname"
	 * @command-example --Label="deploymentlabel" --Production --PackageLocation=".\service.cspkg"
	 * @command-example --ServiceConfigLocation=".\ServiceConfiguration.cscfg" --StorageAccount="mystorage"
	 * @command-example --StartImmediately --WaitFor
	 */
	public function createFromLocalCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $deploymentName, $label, $staging = false, $production = false, $packageLocation, $serviceConfigurationLocation, $storageAccount, $startImmediately = true, $warningsAsErrors = false, $waitForOperation = false)
	{
		$deploymentSlot = 'staging';
		if (!$staging && !$production) {
			throw new Microsoft_Console_Exception('Either --Staging or --Production should be specified.');
		}
		if ($production) {
			$deploymentSlot = 'production';
		}

		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$blobClient = $client->createBlobClientForService($storageAccount);
		$blobClient->createContainerIfNotExists('phpazuredeployments');
		$blobClient->putBlob('phpazuredeployments', basename($packageLocation), $packageLocation);
		$package = $blobClient->getBlobInstance('phpazuredeployments', basename($packageLocation));
		
		$client->createDeployment($serviceName, $deploymentSlot, $deploymentName, $label, $package->Url, $serviceConfigurationLocation, $startImmediately, $warningsAsErrors);

		$client->waitForOperation();
		$blobClient->deleteBlob('phpazuredeployments', basename($packageLocation));
		
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $client->getLastRequestId();
	}
	
	/**
	 * Get deployment properties.
	 * 
	 * @command-name GetProperties
	 * @command-description Get deployment properties.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --Name Required. The hosted service account name to operate on.
	 * @command-parameter-for $deploymentSlot Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --BySlot Required if deployment name is omitted. The slot to retrieve property information for.
	 * @command-parameter-for $deploymentName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ByName Required if deployment slot is omitted. The deployment name to retrieve property information for.
	 * @command-example Get deployment properties for service "phptest" (production slot):
	 * @command-example GetProperties -sid="<your_subscription_id>" -cert="mycert.pem" --Name="servicename" --BySlot="production"
	 */
	public function getPropertiesCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $deploymentSlot, $deploymentName)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		
		$result = null;
		
		if (!is_null($deploymentSlot) && $deploymentSlot != '') {
			$deploymentSlot = strtolower($deploymentSlot);
			
			$result = $client->getDeploymentBySlot($serviceName, $deploymentSlot);
		} else {
			$result = $client->getDeploymentByDeploymentId($serviceName, $deploymentName);
		}

		$this->_displayObjectInformation($result, array('Name', 'DeploymentSlot', 'Label', 'Url', 'Status'));
	}
	
	/**
	 * Get hosted service account property.
	 * 
	 * @command-name GetProperty
	 * @command-description Get deployment property.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --Name Required. The hosted service account name to operate on.
	 * @command-parameter-for $deploymentSlot Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --BySlot Required if deployment name is omitted. The slot to retrieve property information for.
	 * @command-parameter-for $deploymentName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ByName Required if deployment slot is omitted. The deployment name to retrieve property information for.
	 * @command-parameter-for $property Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Property|-prop Required. The property to retrieve for the hosted service account.
	 * @command-example Get deployment property "Name" for service "phptest" (production slot):
	 * @command-example GetProperties -sid="<your_subscription_id>" -cert="mycert.pem"
	 * @command-example --Name="servicename" --BySlot="production" --Property="Name"
	 */
	public function getPropertyCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $deploymentSlot, $deploymentName, $property)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		
		$result = null;
		
		if (!is_null($deploymentSlot) && $deploymentSlot != '') {
			$deploymentSlot = strtolower($deploymentSlot);
			
			$result = $client->getDeploymentBySlot($serviceName, $deploymentSlot);
		} else {
			$result = $client->getDeploymentByDeploymentId($serviceName, $deploymentName);
		}

		printf("%s\r\n", $result->$property);
	}
	
	/**
	 * Swap deployment slots (perform VIP swap).
	 * 
	 * @command-name Swap
	 * @command-description Swap deployment slots (perform VIP swap).
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --Name Required. The hosted service account name to operate on.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 * @command-example Swap deployment slots:
	 * @command-example Swap -sid="<your_subscription_id>" -cert="mycert.pem" --Name="servicename"
	 */
	public function swapCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $waitForOperation = false)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		
		$productionDeploymentName = null;
		try { $productionDeploymentName = $client->getDeploymentBySlot($serviceName, 'production')->Name; } catch (Exception $ex) {}
		
		$stagingDeploymentName = null;
		try { $stagingDeploymentName = $client->getDeploymentBySlot($serviceName, 'staging')->Name; } catch (Exception $ex) {}
		
		if (is_null($productionDeploymentName)) {
			$productionDeploymentName = $stagingDeploymentName;
		}
		if (is_null($stagingDeploymentName)) {
			throw new Microsoft_Console_Exception('Swapping deployment slots is only possible when both slots have an active deployment or when production slot is empty.');
		}

		$client->swapDeployment($serviceName, $productionDeploymentName, $stagingDeploymentName);
		
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $client->getLastRequestId();
	}
	
	/**
	 * Deletes a deployment.
	 * 
	 * @command-name Delete
	 * @command-description Deletes a deployment.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --Name Required. The hosted service account name to operate on.
	 * @command-parameter-for $deploymentSlot Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --BySlot Required if deployment name is omitted. The slot to retrieve property information for.
	 * @command-parameter-for $deploymentName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ByName Required if deployment slot is omitted. The deployment name to retrieve property information for.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 * @command-example Delete a deployment:
	 * @command-example Delete -sid="<your_subscription_id>" -cert="mycert.pem" --Name="hostedservicename" --DeploymentName="deploymentname"
	 */
	public function deleteCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $deploymentSlot, $deploymentName, $waitForOperation = false)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);

		if (!is_null($deploymentSlot) && $deploymentSlot != '') {
			$deploymentSlot = strtolower($deploymentSlot);
			
			$client->deleteDeploymentBySlot($serviceName, $deploymentSlot);
		} else {
			$client->deleteDeploymentByDeploymentId($serviceName, $deploymentName);
		}
		
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $client->getLastRequestId();
	}
	
	/**
	 * Updates a deployment's configuration.
	 * 
	 * @command-name UpdateConfig
	 * @command-description Updates a deployment's configuration.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --Name Required. The hosted service account name to operate on.
	 * @command-parameter-for $deploymentSlot Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --BySlot Required if deployment name is omitted. The slot to retrieve property information for.
	 * @command-parameter-for $deploymentName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ByName Required if deployment slot is omitted. The deployment name to retrieve property information for.
	 * @command-parameter-for $serviceConfigurationLocation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ServiceConfigLocation Required. The location of the .cspkg file.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 * @command-example Update configuration:
	 * @command-example UpdateConfig -sid="<your_subscription_id>" -cert="mycert.pem"
	 * @command-example --Name="hostedservicename" --ByName="deploymentname"
	 * @command-example --ServiceConfigLocation=".\ServiceConfiguration.cscfg"
	 */
	public function updateConfigurationCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $deploymentSlot, $deploymentName, $serviceConfigurationLocation, $waitForOperation = false)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);

		if (!is_null($deploymentSlot) && $deploymentSlot != '') {
			$deploymentSlot = strtolower($deploymentSlot);
			
			$client->configureDeploymentBySlot($serviceName, $deploymentSlot, $serviceConfigurationLocation);
		} else {
			$client->configureDeploymentByDeploymentId($serviceName, $deploymentName, $serviceConfigurationLocation);
		}
		
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $client->getLastRequestId();
	}
	
	/**
	 * Updates a deployment's status.
	 * 
	 * @command-name UpdateStatus
	 * @command-description Updates a deployment's status.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --Name Required. The hosted service account name to operate on.
	 * @command-parameter-for $deploymentSlot Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --BySlot Required if deployment name is omitted. The slot to retrieve property information for.
	 * @command-parameter-for $deploymentName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ByName Required if deployment slot is omitted. The deployment name to retrieve property information for.
	 * @command-parameter-for $newStatus Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Status Required. New status (Suspended|Running)
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 * @command-example Suspend a deployment:
	 * @command-example UpdateStatus -sid="<your_subscription_id>" -cert="mycert.pem"
	 * @command-example --Name="hostedservicename" --ByName="deploymentname"
	 * @command-example --Status="Suspended"
	 */
	public function updateStatusCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $deploymentSlot, $deploymentName, $newStatus, $waitForOperation = false)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);

		if (!is_null($deploymentSlot) && $deploymentSlot != '') {
			$deploymentSlot = strtolower($deploymentSlot);
			
			$client->updateDeploymentStatusBySlot($serviceName, $deploymentSlot, $newStatus);
		} else {
			$client->updateDeploymentStatusByDeploymentId($serviceName, $deploymentName, $newStatus);
		}
		
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $client->getLastRequestId();
	}
	
	/**
	 * Updates the number of instances.
	 * 
	 * @command-name EditInstanceNumber
	 * @command-description Updates the number of instances.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --Name Required. The hosted service account name to operate on.
	 * @command-parameter-for $deploymentSlot Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --BySlot Required if deployment name is omitted. The slot to retrieve property information for.
	 * @command-parameter-for $deploymentName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ByName Required if deployment slot is omitted. The deployment name to retrieve property information for.
	 * @command-parameter-for $roleName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --RoleName|-r Required. Role name to update the number of instances for.
	 * @command-parameter-for $newInstanceNumber Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --NewInstanceNumber|-i Required. New number of instances.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 * @command-example Suspend a deployment:
	 * @command-example EditInstanceNumber -sid="<your_subscription_id>" -cert="mycert.pem"
	 * @command-example --Name="hostedservicename" --ByName="deploymentname"
	 * @command-example --NewInstanceNumber="4"
	 */
	public function editInstanceNumberCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $deploymentSlot, $deploymentName, $roleName, $newInstanceNumber = 1, $waitForOperation = false)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);

		if (!is_null($deploymentSlot) && $deploymentSlot != '') {
			$deploymentSlot = strtolower($deploymentSlot);
			
			$client->setInstanceCountBySlot($serviceName, $deploymentSlot, $roleName, $newInstanceNumber);
		} else {
			$client->setInstanceCountByDeploymentId($serviceName, $deploymentName, $roleName, $newInstanceNumber);
		}
		
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $client->getLastRequestId();
	}
	
	/**
	 * Reboots a role instance.
	 * 
	 * @command-name RebootInstance
	 * @command-description Reboots a role instance.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --Name Required. The hosted service account name to operate on.
	 * @command-parameter-for $deploymentSlot Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --BySlot Required if deployment name is omitted. The slot to retrieve property information for.
	 * @command-parameter-for $deploymentName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ByName Required if deployment slot is omitted. The deployment name to retrieve property information for.
	 * @command-parameter-for $instanceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --RoleInstanceName Required. The name of the role instance to work with.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 * @command-example Reboot a role instance:
	 * @command-example RebootInstance -sid="<your_subscription_id>" -cert="mycert.pem"
	 * @command-example --Name="hostedservicename" --ByName="deploymentname"
	 * @command-example --RoleInstanceName="PhpOnAzure.Web_IN_0"
	 */
	public function rebootInstanceCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $deploymentSlot, $deploymentName, $instanceName, $waitForOperation = false)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);

		if (!is_null($deploymentSlot) && $deploymentSlot != '') {
			$deploymentSlot = strtolower($deploymentSlot);
			
			$client->rebootRoleInstanceBySlot($serviceName, $deploymentSlot, $instanceName);
		} else {
			$client->rebootRoleInstanceByDeploymentId($serviceName, $deploymentName, $instanceName);
		}
		
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $client->getLastRequestId();
	}
	
	/**
	 * Reimages a role instance.
	 * 
	 * @command-name ReimageInstance
	 * @command-description Reimages a role instance.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --Name Required. The hosted service account name to operate on.
	 * @command-parameter-for $deploymentSlot Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --BySlot Required if deployment name is omitted. The slot to retrieve property information for.
	 * @command-parameter-for $deploymentName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ByName Required if deployment slot is omitted. The deployment name to retrieve property information for.
	 * @command-parameter-for $instanceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --RoleInstanceName Required. The name of the role instance to work with.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 * @command-example Reimage a role instance:
	 * @command-example ReimageInstance -sid="<your_subscription_id>" -cert="mycert.pem"
	 * @command-example --Name="hostedservicename" --ByName="deploymentname"
	 * @command-example --RoleInstanceName="PhpOnAzure.Web_IN_0"
	 */
	public function reimageInstanceCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $deploymentSlot, $deploymentName, $instanceName, $waitForOperation = false)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);

		if (!is_null($deploymentSlot) && $deploymentSlot != '') {
			$deploymentSlot = strtolower($deploymentSlot);
			
			$client->reimageRoleInstanceBySlot($serviceName, $deploymentSlot, $instanceName);
		} else {
			$client->reimageRoleInstanceByDeploymentId($serviceName, $deploymentName, $instanceName);
		}
		
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $client->getLastRequestId();
	}
	
	/**
	 * Upgrades a deployment from a remote package file and service configuration.
	 * 
	 * @command-name UpgradeFromStorage
	 * @command-description Upgrades a deployment from a remote package file and service configuration.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Name Required. The hosted service account name to operate on.
	 * @command-parameter-for $deploymentSlot Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --BySlot Required if deployment name is omitted. The slot to retrieve property information for.
	 * @command-parameter-for $deploymentName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ByName Required if deployment slot is omitted. The deployment name to retrieve property information for.
	 * @command-parameter-for $label Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Label Required. The label for the deployment.
	 * @command-parameter-for $packageUrl Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --PackageUrl Required. The remote location of the .cspkg file.
	 * @command-parameter-for $serviceConfigurationLocation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ServiceConfigLocation Required. The location of the .cspkg file.
	 * @command-parameter-for $mode Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Mode Required. Set to auto|manual.
	 * @command-parameter-for $roleName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --RoleName Optional. Role name to upgrade.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 */
	public function upgradeFromStorageCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $deploymentSlot, $deploymentName, $label, $packageUrl, $serviceConfigurationLocation, $mode = 'auto', $roleName = null, $waitForOperation = false)
	{		
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);

		if (!is_null($deploymentSlot) && $deploymentSlot != '') {
			$deploymentSlot = strtolower($deploymentSlot);
			
			$client->upgradeDeploymentBySlot($serviceName, $deploymentSlot, $label, $packageUrl, $serviceConfigurationLocation, $mode, $roleName);
		} else {
			$client->upgradeDeploymentByDeploymentId($serviceName, $deploymentName, $label, $packageUrl, $serviceConfigurationLocation, $mode, $roleName);
		}
		
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $client->getLastRequestId();
	}
	
	/**
	 * Upgrades a deployment from a local package file and service configuration.
	 * 
	 * @command-name UpgradeFromLocal
	 * @command-description Upgrades a deployment from a local package file and service configuration.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Name Required. The hosted service account name to operate on.
	 * @command-parameter-for $deploymentSlot Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --BySlot Required if deployment name is omitted. The slot to retrieve property information for.
	 * @command-parameter-for $deploymentName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ByName Required if deployment slot is omitted. The deployment name to retrieve property information for.
	 * @command-parameter-for $label Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Label Required. The label for the deployment.
	 * @command-parameter-for $packageLocation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_StdIn --PackageLocation Required. The location of the .cspkg file.
	 * @command-parameter-for $serviceConfigurationLocation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ServiceConfigLocation Required. The location of the .cspkg file.
	 * @command-parameter-for $storageAccount Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --StorageAccount Required. Storage account to use when creating the deployment.
	 * @command-parameter-for $mode Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --Mode Required. Set to auto|manual.
	 * @command-parameter-for $roleName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --RoleName Optional. Role name to upgrade.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 */
	public function upgradeFromLocalCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $deploymentSlot, $deploymentName, $label, $packageLocation, $serviceConfigurationLocation, $storageAccount, $mode = 'auto', $roleName = null, $waitForOperation = false)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		
		$blobClient = $client->createBlobClientForService($storageAccount);
		$blobClient->createContainerIfNotExists('phpazuredeployments');
		$blobClient->putBlob('phpazuredeployments', basename($packageLocation), $packageLocation);
		$package = $blobClient->getBlobInstance('phpazuredeployments', basename($packageLocation));
		
		if (!is_null($deploymentSlot) && $deploymentSlot != '') {
			$deploymentSlot = strtolower($deploymentSlot);
			
			$client->upgradeDeploymentBySlot($serviceName, $deploymentSlot, $label, $package->Url, $serviceConfigurationLocation, $mode, $roleName);
		} else {
			$client->upgradeDeploymentByDeploymentId($serviceName, $deploymentName, $label, $package->Url, $serviceConfigurationLocation, $mode, $roleName);
		}
		
		$client->waitForOperation();
		$blobClient->deleteBlob('phpazuredeployments', basename($packageLocation));
		
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $client->getLastRequestId();
	}
	
	/**
	 * Walks upgrade domains.
	 * 
	 * @command-name WalkUpgradeDomains
	 * @command-description Walks upgrade domains.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $serviceName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env|Microsoft_Console_Command_ParameterSource_StdIn --Name Required. The hosted service account name to operate on.
	 * @command-parameter-for $deploymentSlot Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --BySlot Required if deployment name is omitted. The slot to retrieve property information for.
	 * @command-parameter-for $deploymentName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --ByName Required if deployment slot is omitted. The deployment name to retrieve property information for.
	 * @command-parameter-for $upgradeDomain Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --UpgradeDomain Required. The upgrade domain index.
	 * @command-parameter-for $waitForOperation Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --WaitFor|-w Optional. Wait for the operation to complete?
	 */
	public function walkUpgradeDomainsCommand($subscriptionId, $certificate, $certificatePassphrase, $serviceName, $deploymentSlot, $deploymentName, $upgradeDomain, $waitForOperation = false)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		
		if (!is_null($deploymentSlot) && $deploymentSlot != '') {
			$deploymentSlot = strtolower($deploymentSlot);
			
			$client->walkUpgradeDomainBySlot($serviceName, $deploymentSlot, $upgradeDomain);
		} else {
			$client->walkUpgradeDomainByDeploymentId($serviceName, $deploymentName, $upgradeDomain);
		}
		
		if ($waitForOperation) {
			$client->waitForOperation();
		}
		echo $client->getLastRequestId();
	}
}

Microsoft_Console_Command::bootstrap($_SERVER['argv']);