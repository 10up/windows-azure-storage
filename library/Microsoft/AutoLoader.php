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
 * @package    Microsoft
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */

/** Microsoft root directory */
if (!defined('MICROSOFT_ROOT')) {
	define('MICROSOFT_ROOT', realpath(dirname(__FILE__) . '/../') . DIRECTORY_SEPARATOR);
}
Microsoft_AutoLoader::Register();

/**
 * @category   Microsoft
 * @package    Microsoft
 * @subpackage AutoLoader
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class Microsoft_AutoLoader
{
	/**
	 * Registers the autoloader
	 */
	public static function Register() {
		return spl_autoload_register(array('Microsoft_AutoLoader', 'Load'));
	}
	
	/**
	 * Load a class
	 * 
	 * @param string $className Class name to load
	 */
	public static function Load($className){
		if ((class_exists($className)) || (strpos($className, 'Microsoft') === false)) {
			return false;
		}

		$classFilePath = MICROSOFT_ROOT . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		
		if ((file_exists($classFilePath) === false) || (is_readable($classFilePath) === false)) {
			return false;
		}

		require($classFilePath);
	}
}