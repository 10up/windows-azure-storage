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
 * Scaffold commands
 * 
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure_CommandLine
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * 
 * @command-handler scaffolder
 * @command-handler-description Windows Azure Package commands
 * @command-handler-header Windows Azure SDK for PHP
 * @command-handler-header Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @command-handler-footer 
 * @command-handler-footer All commands support the --ConfigurationFile or -F parameter.
 * @command-handler-footer The parameter file is a simple INI file carrying one parameter
 * @command-handler-footer value per line. It accepts the same parameters as one can
 * @command-handler-footer use from the command line command.
 */
class Microsoft_WindowsAzure_CommandLine_Scaffolder
	extends Microsoft_Console_Command
{	
	/**
	 * Runs a scaffolder and creates a Windows Azure project structure which can be customized before packaging.
	 * 
	 * @command-name Run
	 * @command-description Runs a scaffolder and creates a Windows Azure project structure which can be customized before packaging.
	 * 
	 * @command-parameter-for $path Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --OutputPath|-out Required. The path to create the Windows Azure project structure.
	 * @command-parameter-for $scaffolder Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Scaffolder|-s Optional. The path to the scaffolder to use. Defaults to Scaffolders/DefaultScaffolder.phar 
	 */
	public function runCommand($path, $scaffolder, $argv)
	{
		// Default parameter value
		if (is_null($scaffolder) || $scaffolder == '') {
			$scaffolder = 'DefaultScaffolder';
		}
		
		// Locate scaffolder
		$scaffolderFile = realpath($scaffolder);
		if (!is_file($scaffolderFile)) {
			$scaffolderFile = realpath(dirname(__FILE__) . '/../../../../scaffolders/' . str_replace('.phar', '', $scaffolder) . '.phar');
		}
		
		// Verify scaffolder
		if (!is_file($scaffolderFile)) {
			throw new Microsoft_Console_Exception('Could not locate the given scaffolder: ' . $scaffolder);
		}
		
		// Include scaffolder
		require_once $scaffolderFile;
		$scaffolderClass = str_replace('.phar', '', basename($scaffolderFile));
		if (!class_exists($scaffolderClass)) {
			$scaffolderClass = str_replace('-', '_', str_replace('.', '_', $scaffolderClass));
			if (!class_exists($scaffolderClass)) {
				$scaffolderClass = substr($scaffolderClass, 0, strpos($scaffolderClass, '_'));
				if (!class_exists($scaffolderClass)) {
					throw new Microsoft_Console_Exception('Could not locate a class named ' . $scaffolderClass . ' in the given scaffolder: ' . $scaffolder . '. Make sure the scaffolder package contains a file named index.php and contains a class named Scaffolder.');
				}
			}
		}
		
		// Add command parameters
		array_unshift($argv, '--OutputPath=' . $path);
		array_unshift($argv, '--Phar=' . $scaffolderFile);
		array_unshift($argv, 'Run');
		array_unshift($argv, $scaffolderClass);

		// Run scaffolder
		Microsoft_Console_Command::bootstrap($argv);
		
		// Echo output path
		echo "$scaffolderClass finished at location: $path\r\n";
	}
	
	/**
	 * Shows help information for a specific scaffolder.
	 * 
	 * @command-name Help
	 * @command-description Shows help information for a specific scaffolder.
	 * 
	 * @command-parameter-for $scaffolder Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile|Microsoft_Console_Command_ParameterSource_Env --Scaffolder|-s Optional. The path to the scaffolder to use. Defaults to Scaffolders/DefaultScaffolder.phar 
	 */
	public function scaffolderhelpCommand($scaffolder, $argv)
	{
		// Default parameter value
		if (is_null($scaffolder) || $scaffolder == '') {
			$scaffolder = 'DefaultScaffolder';
		}
		
		// Locate scaffolder
		$scaffolderFile = realpath($scaffolder);
		if (!is_file($scaffolderFile)) {
			$scaffolderFile = realpath(dirname(__FILE__) . '/Scaffolders/' . str_replace('.phar', '', $scaffolder) . '.phar');
		}
		
		// Verify scaffolder
		if (!is_file($scaffolderFile)) {
			throw new Microsoft_Console_Exception('Could not locate the given scaffolder: ' . $scaffolder);
		}
		
		// Include scaffolder
		require_once $scaffolderFile;
		$scaffolderClass = str_replace('.phar', '', basename($scaffolderFile));
		if (!class_exists($scaffolderClass)) {
			$scaffolderClass = str_replace('-', '_', str_replace('.', '_', $scaffolderClass));
			if (!class_exists($scaffolderClass)) {
				$scaffolderClass = substr($scaffolderClass, 0, strpos($scaffolderClass, '_'));
				if (!class_exists($scaffolderClass)) {
					throw new Microsoft_Console_Exception('Could not locate a class named ' . $scaffolderClass . ' in the given scaffolder: ' . $scaffolder . '. Make sure the scaffolder package contains a file named index.php and contains a class named Scaffolder.');
				}
			}
		}
		
		// Add command parameters
		array_unshift($argv, '-h');
		array_unshift($argv, $scaffolderClass);
				
		// Run scaffolder
		Microsoft_Console_Command::bootstrap($argv);
	}
		
	/**
	 * Builds a scaffolder from a given path.
	 * 
	 * @command-name Build
	 * @command-description Builds a scaffolder from a given path.
	 * 
	 * @command-parameter-for $rootPath Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --InputPath|-in Required. The path to package into a scaffolder.
	 * @command-parameter-for $scaffolderFile Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_ConfigFile --OutputFile|-out Required. The filename of the scaffolder.
	 */
	public function buildCommand($rootPath, $scaffolderFile)
	{
		$archive = new Phar($scaffolderFile);
		$archive->buildFromIterator(
			new RecursiveIteratorIterator(
				new SourceControlFilteredRecursiveFilterIterator(
					new RecursiveDirectoryIterator(realpath($rootPath)))),
		realpath($rootPath));
		
		echo $scaffolderFile;
	}
}
Microsoft_Console_Command::bootstrap($_SERVER['argv']);

class SourceControlFilteredRecursiveFilterIterator
	extends RecursiveFilterIterator {
	public static $filters = array('.svn', '.git');
 
    public function accept() {
    	return !in_array(
    	$this->current()->getFilename(), self::$filters, true);
    }
}