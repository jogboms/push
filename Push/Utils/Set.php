<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */

namespace Push\Utils;

abstract Class Set implements \ArrayAccess
{

  public function __set($key, $value)
  {
    return $this[$key] = $value;
  }
  public function __get($key)
  {
    return $this[$key];
  }
  public function __isset($key)
  {
    return isset($this[$key]);
  }
  public function __unset($key)
  {
    unset($this[$key]);
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
