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

Class Collection extends Set implements \Countable, \IteratorAggregate 
{
	protected
		$params = [],
		$singletons = [],
		$read_only = false, 
		$is_object = false;

	function __construct($params = [], $read_only = false)
	{
		$this->read_only = $read_only;
		$this->params = $params;
		
		if(is_object($params)){
			$this->params = $this->convert();
		}
	}

	public function set($key, $value = null, $singleton = false)
	{
		if(is_array($key)){
			foreach ($key as $k => $v) {
				$this->set($k, $v);
			}
		}
		else {
			if(!$this->isReadOnly() || !$this->has($key)){

				if($value instanceof \Closure
					|| (is_object($value) && method_exists($value, '__invoke'))){
					$this->params[$this->normalize($key)] = $value($this);
				} else {
					$this->params[$this->normalize($key)] = $value;
				}

				// if($singleton){
				// 	$this->singletons[$this->normalize($key)] = null;
				// }
			}
		}
		return $this;
	}
	public function get($key, $default = null)
	{
		return $this->has($key) ? $this->params[$this->normalize($key)] : $default;
	}
	public function has($key)
	{
		return array_key_exists($this->normalize($key), $this->params);
	}
	public function remove($key)
	{
		if(is_array($key)){
			array_map(array($this, 'remove'), $key);
		} else {
			if(!$this->isReadOnly())
				unset($this->params[$this->normalize($key)]);
		}
		return $this;
	}
	public function keys()
	{
		return array_keys($this->params);
	}
	public function replace(array $key)
	{
		foreach ($key as $k => $v) {
			$this->set($k, $v);
		}
	}
	public function clear()
	{
		$this->params = [];
	}
	public function params()
	{
		return $this->isObject() ? $this->revert() : $this->params;
	}

	/**
	 * @todo
	 */
	public function singleton($key, $value)
	{
		return $this->set($key, $value, true);
	}


	public function count()
	{
		return count($this->params);
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->params);
	}


	protected function isObject()
	{
		return !!$this->is_object;
	}
	protected function isReadOnly()
	{
		return !!$this->read_only;
	}

	public function toArray()
	{
		return json_decode(json_encode($this->params), true);
	}
	public function toObject()
	{
		return json_decode(json_encode($this->params));
	}
	/**
	 * Normalize key names, this method can be overwritten for custom functions
	 * @param  string $key Key name to normalize
	 * @return string
	 */
	public function normalize($key)
	{
		return $key;
	}
	/**
	 * Convert Object-type argument to Array-type
	 * @param  object $arg 
	 * @return array
	 */
	protected function convert()
	{
		$this->is_object = true;
		return $this->toArray();
	}
	/**
	 * Convert Array-type argument to Object-type
	 * @param  array $arg
	 * @return object
	 */
	protected function revert()
	{
		return $this->toObject();
	}
}
