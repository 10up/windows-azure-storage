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
 * @subpackage Management
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * @version    $Id: BlobInstance.php 53615 2010-11-16 20:45:11Z unknown $
 */

/**
 * @see Microsoft_AutoLoader
 */
require_once dirname(__FILE__) . '/../../AutoLoader.php';

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Management
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * 
 * @property string $Name            The name for the deployment. This name must be unique among other deployments for the specified hosted service.
 * @property string $DeploymentSlot  The environment to which the hosted service is deployed, either staging or production.
 * @property string $PrivateID       The unique identifier for this deployment.
 * @property string $Label           The label for the deployment.
 * @property string $Url             The URL for the deployment.
 * @property string $Configuration   The configuration file (XML, represented as string).
 * @property string $Status          The status of the deployment. Running, Suspended, RunningTransitioning, SuspendedTransitioning, Starting, Suspending, Deploying, Deleting.
 * @property string $UpgradeStatus   Parent node for elements describing an upgrade that is currently underway.
 * @property string $UpgradeType     The upgrade type designated for this deployment. Possible values are Auto and Manual.
 * @property string $CurrentUpgradeDomainState  The state of the current upgrade domain. Possible values are Before and During.
 * @property string $CurrentUpgradeDomain       An integer value that identifies the current upgrade domain. Upgrade domains are identified with a zero-based index: the first upgrade domain has an ID of 0, the second has an ID of 1, and so on.
 * @property string $UpgradeDomainCount         An integer value that indicates the number of upgrade domains in the deployment.
 * @property string $sdkVersion				    Version of SDK used to create the deployment
 * @property array  $RoleInstanceList           The list of role instances.
 * @property array  $RoleList                   The list of roles.
 * @property array  $InputEndpoints             The list of input endpoints.
 */
class Microsoft_WindowsAzure_Management_DeploymentInstance
	extends Microsoft_WindowsAzure_Management_ServiceEntityAbstract
{    
    /**
     * Constructor
     * 
     * @param string $name            The name for the deployment. This name must be unique among other deployments for the specified hosted service.
     * @param string $deploymentSlot  The environment to which the hosted service is deployed, either staging or production.
     * @param string $privateID       The unique identifier for this deployment.
     * @param string $label           The label for the deployment.
     * @param string $url             The URL for the deployment.
     * @param string $configuration   The configuration file (XML, represented as string).
     * @param string $status          The status of the deployment. Running, Suspended, RunningTransitioning, SuspendedTransitioning, Starting, Suspending, Deploying, Deleting.
     * @param string $upgradeStatus   Parent node for elements describing an upgrade that is currently underway.
     * @param string $upgradeType     The upgrade type designated for this deployment. Possible values are Auto and Manual.
     * @param string $currentUpgradeDomainState  The state of the current upgrade domain. Possible values are Before and During.
     * @param string $currentUpgradeDomain       An integer value that identifies the current upgrade domain. Upgrade domains are identified with a zero-based index: the first upgrade domain has an ID of 0, the second has an ID of 1, and so on.
     * @param string $upgradeDomainCount         An integer value that indicates the number of upgrade domains in the deployment.
     * @param string $sdkVersion				 Version of SDK used to create the deployment
     * @param array  $roleInstanceList           The list of role instances.
     * @param array  $roleList                   The list of roles.
     * @param array  $inputEndpoints             The list of input endpoints.
	 */
    public function __construct($name, $deploymentSlot, $privateID, $label, $url, $configuration, $status, $upgradeStatus, $upgradeType, $currentUpgradeDomainState, $currentUpgradeDomain, $upgradeDomainCount, $sdkVersion, $roleInstanceList = array(), $roleList = array(), $inputEndpoints = array()) 
    {	        
        $this->_data = array(
            'name'                        => $name,
            'deploymentslot'              => $deploymentSlot,
            'privateid'                   => $privateID,
            'label'                       => base64_decode($label),
            'url'                         => $url,
            'configuration'               => base64_decode($configuration),
            'status'                      => $status,
            'upgradestatus'               => $upgradeStatus,
            'upgradetype'                 => $upgradeType,
            'currentupgradedomainstate'   => $currentUpgradeDomainState,
            'currentupgradedomain'        => $currentUpgradeDomain,
            'upgradedomaincount'          => $upgradeDomainCount, 
            'sdkversion'                  => $sdkVersion,
            'roleinstancelist'            => $roleInstanceList,
            'rolelist'                    => $roleList,
            'inputendpoints'              => $inputEndpoints  
        );
    }
}
