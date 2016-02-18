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

namespace Push\Utils;

abstract Class Set implements \ArrayAccess 
{

	public function __set($key, $value)
	{
		return $this->set($key, $value);
	}
	public function __get($key)
	{
		return $this->get($key);
	}
	public function __isset($key)
	{
		return $this->has($key);
	}
	public function __unset($key)
	{
		return $this->remove($key);
	}

	public function offsetExists($key)
	{
		return $this->has($key);
	}
	public function offsetGet($key)
	{
		return $this->get($key);
	}
	public function offsetSet($key, $value)
	{
		return $this->set($key, $value);
	}
	public function offsetUnset($key)
	{
		return $this->remove($key);
	}

	abstract public function set($key, $value);
	abstract public function get($key, $default = null);
	abstract public function has($key);
	abstract public function remove($key);
	abstract public function params();
}