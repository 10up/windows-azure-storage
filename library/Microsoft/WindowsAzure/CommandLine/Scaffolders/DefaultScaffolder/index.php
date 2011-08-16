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
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * @version    $Id: SharedKeyCredentials.php 14561 2009-05-07 08:05:12Z unknown $
 */


/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure_CommandLine
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * 
 * @command-handler DefaultScaffolder
 * 
 * @command-handler-description Windows Azure SDK for PHP default scaffolder.
 * @command-handler-header Windows Azure SDK for PHP
 * @command-handler-header Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @command-handler-footer 
 * @command-handler-footer The DefaultScaffolder automatically installs PHP
 * @command-handler-footer to the Windows Azure virtual machine. If a customized
 * @command-handler-footer php.ini is required, add it in the /php folder after
 * @command-handler-footer running the scaffolder.
 */ 
class DefaultScaffolder
	extends Microsoft_WindowsAzure_CommandLine_PackageScaffolder_PackageScaffolderAbstract
{
	/**
	 * Runs a scaffolder and creates a Windows Azure project structure which can be customized before packaging.
	 * 
	 * @command-name Run
	 * @command-description Runs the scaffolder.
	 * 
	 * @command-parameter-for $scaffolderFile Argv --Phar Required. The scaffolder Phar file path. This is injected automatically.
	 * @command-parameter-for $rootPath Argv|ConfigFile --OutputPath|-out Required. The path to create the Windows Azure project structure. This is injected automatically. 
	 * @command-parameter-for $diagnosticsConnectionString Argv|ConfigFile|Env --DiagnosticsConnectionString|-d Optional. The diagnostics connection string. This defaults to development storage.
	 */
	public function runCommand($scaffolderFile, $rootPath, $diagnosticsConnectionString = 'UseDevelopmentStorage=true')
	{
		// Load Phar
		$phar = new Phar($scaffolderFile);
		
		// Extract to disk
		$this->log('Extracting resources...');
		$this->createDirectory($rootPath);
		$this->extractResources($phar, $rootPath);
		$this->log('Extracted resources.');
		
		// Apply transforms
		$this->log('Applying transforms...');
		$this->applyTransforms($rootPath, array(
			'DiagnosticsConnectionString' => $diagnosticsConnectionString
		));
		$this->log('Applied transforms.');
		
		// Show "to do" message
		$contentRoot = realpath($rootPath . '/PhpOnAzure.Web');
		$this->log('');
		$this->log('Note: before packaging your application, please copy your application code to ' . $contentRoot);
	}
}
