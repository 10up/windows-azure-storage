<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Microsoft
 * @package    Microsoft_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Abstract.php 22632 2010-07-18 18:30:08Z ramon $
 */

/**
 * @see Microsoft_AutoLoader
 */
require_once dirname(__FILE__) . '/../../AutoLoader.php';

/**
 * @category   Microsoft
 * @package    Microsoft_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Abstract.php 22632 2010-07-18 18:30:08Z ramon $
 */
abstract class Microsoft_Log_Writer_Abstract implements Microsoft_Log_FactoryInterface
{
    /**
     * @var array of Microsoft_Log_Filter_Interface
     */
    protected $_filters = array();

    /**
     * Formats the log message before writing.
     * @var Microsoft_Log_Formatter_Interface
     */
    protected $_formatter;

    /**
     * Add a filter specific to this writer.
     *
     * @param  Microsoft_Log_Filter_Interface  $filter
     * @return void
     */
    public function addFilter($filter)
    {
        if (is_integer($filter)) {
            $filter = new Microsoft_Log_Filter_Priority($filter);
        }

        if (!$filter instanceof Microsoft_Log_Filter_Interface) {
            /** @see Microsoft_Log_Exception */
            require_once 'Microsoft/Log/Exception.php';
            throw new Microsoft_Log_Exception('Invalid filter provided');
        }

        $this->_filters[] = $filter;
    }

    /**
     * Log a message to this writer.
     *
     * @param  array     $event  log data event
     * @return void
     */
    public function write($event)
    {
        foreach ($this->_filters as $filter) {
            if (! $filter->accept($event)) {
                return;
            }
        }

        // exception occurs on error
        $this->_write($event);
    }

    /**
     * Set a new formatter for this writer
     *
     * @param  Microsoft_Log_Formatter_Interface $formatter
     * @return void
     */
    public function setFormatter(Microsoft_Log_Formatter_Interface $formatter)
    {
        $this->_formatter = $formatter;
    }

    /**
     * Perform shutdown activites such as closing open resources
     *
     * @return void
     */
    public function shutdown()
    {}

    /**
     * Write a message to the log.
     *
     * @param  array  $event  log data event
     * @return void
     */
    abstract protected function _write($event);

    /**
     * Validate and optionally convert the config to array
     *
     * @param  array $config
     * @return array
     * @throws Zend_Log_Exception
     */
    static protected function _parseConfig($config)
    {
        if (!is_array($config)) {
            require_once 'Microsoft/Log/Exception.php';
            throw new Microsoft_Log_Exception(
				'Configuration must be an array'
			);
        }

        return $config;
    }
}
