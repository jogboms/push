<?php
/**
 * PUSH MVC Framework.
 * @package PUSH MVC Framework
 * @version See PUSH.json
 * @author See PUSH.json
 * @copyright See PUSH.json
 * @license See PUSH.json
 * PUSH MVC Framework is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See PUSH.json for copyright notices and details.
 */
namespace Push\Http;

Class RequestParams extends \Push\Http\Request 
{

	public function __construct($_TEMP_ = [], $_PARAMS_ = [])
	{
		$this->_TEMP_ = $_TEMP_;
		parent::__construct($_PARAMS_);
	}
	
	public function __get($name)
	{
		return isset($this->_TEMP_[$name]) ? $this->_TEMP_[$name] : (
			isset($this->_PARAMS_[$name]) ? $this->_PARAMS_[$name] : null
		);
	}
}
