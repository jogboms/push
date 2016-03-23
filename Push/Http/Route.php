<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */


namespace Push\Http;

Class Route implements \ArrayAccess
{

  public
    $route = '',
    $controller = '',
    $action = '',
    $params = [],
    $methods = [],
    $match = [],
    $defaults = [],
    $middlewares = [];


  function offsetset($name, $value)
  {
    $this->$name = $value;
  }
  function offsetget($name)
  {
    return $this->$name;
  }
  function offsetunset($name)
  {
    unset($this->$name);
  }
  function offsetexists($name)
  {
    return isset($this->$name);
  }

  /**
   * Add Middleware(s) to the Route
   * @param  string $route Route pattern
   * @param  callable $callback Callable function or Middleware clas
   s
   */
  function __construct($route, $callback)
  {
    $this->route = $route;
    $this->controller = $callback;
  }
  /**
   * Add Middleware(s) to the Route
   * @param  array|callable $uses Array of Middleware| A single Middleware| Middlewares...
   * @return self
   * @example $route->uses([function(){}, function(){}])
   * @example $route->uses(function(){})
   * @example $route->uses(function(){}, function(){}, function(){},...)
   */
  public function uses($uses)
  {
    if(is_array($uses)){
      if(!empty($uses))
        $this->middlewares = array_merge($this->middlewares, $uses);
    }
    elseif(func_num_args() > 1){
      $this->middlewares = array_merge($this->middlewares, func_get_args());
    }
    else {
      $this->middlewares[] = $uses;
    }

    return $this;
  }

  /**
   * Set default parameter
   * @param  mixed $defaults Array or Callback that returns an Array
   * @return self
   */
  public function defaults($defaults)
  {
    if($defaults instanceof \Closure)
      $defaults = $defaults();

    if(!is_array($defaults))
      throw new Exception('The parameter passed in for `->defaults($parameter)` for `router` should either be an `Array` or a Callback that returns an `Array`');

    $this->defaults = array_merge($this->defaults, $defaults);
    return $this;
  }

  public function methods($methods)
  {
    if($methods instanceof \Closure)
      $methods = $methods();

    if(!is_string($methods))
      throw new Exception('The parameter passed in for `->methods($parameter)` for `router` should either be a `String` or a Callback that returns a `String`');
    $methods = explode('|', $methods);
    $this->methods = array_merge($this->methods, $methods);
    return $this;
  }

  function setController($controller = null)
  {
    if(!$controller) {
      if(isset($this->match['controller'])){
        $controller = $this->match['controller'];
        unset($this->match['controller']);
      }
      else $controller = array_shift($this->match);
      // $controller = isset($this->match['controller']) ? $this->match['controller'] : array_shift($this->match);
    }
    $this->controller = $controller;
  }
  function setAction($action = null)
  {
    if(!$action) {
      if(isset($this->match['controller']))
        array_shift($this->match);

      $action = isset($this->match['action']) ? $this->match['action'] : null;
    }
    $this->action = $action;
  }
  function setParams(Array $params = [])
  {
    $this->params = array_merge($this->defaults, $this->match, $params);
  }

}
