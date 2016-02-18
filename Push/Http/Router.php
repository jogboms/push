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

Class Router 
{
	private 
		$url, 
		$routeObject, 
		$routes = [], 
		$groups = [];

	/**
	 * @param RouteObject $routeObject 
	 */
	public function __construct(RouteObject $routeObject)
	{
		$this->routeObject = $routeObject;
	}
	/**
	 * Utility functions
	 */
		/**
		 * Get REQUEST URI
		 * @return string REQUEST URI of current page
		 */
		public function url()
		{
			return $this->routeObject->url['uri'];
		}
		/**
		 * Get REQUEST METHOD
		 * @return string REQUEST METHOD of current page
		 */
		public function method()
		{
			return $this->routeObject->url['method'];
		}
		/**
		 * Get Matched Route parameters
		 * @return array Array of parameters
		 */
		public function matches()
		{
			return $this->routeObject->match;
		}
		/**
		 * Get Available Routes
		 * @return array Array of Routes
		 */
		public function routes()
		{
			return $this->routes;
		}
		/**
		 * Add new matchable regex pattern 
		 * @param  string $name    Identifier name of pattern
		 * @param  string $pattern Regex pattern
		 * @return Router
		 */
		public function pattern($name, $pattern)
		{
			$this->routeObject->addPattern($name, $pattern);
			return $this;
		}

	/** 
	 * RESTful API Router Methods 
	 * 
	 * @example $this->{route|any|get|post|put|delete|patch}($route, [$middleware1, $middleware2,...], $callback)
	 * @return Route
	 */
		public function route($route, $controller = null)
		{
			return $this->map(func_get_args());
		}
		/**
		 * ALL kinds of Request
		 */
		public function any($route, $controller = null)
		{
			return $this->map(func_get_args())->methods('GET|POST|PATCH|DELETE|PUT');
		}
		/**
		 * GET Request
		 */
		public function get($route, $controller = null)
		{
			return $this->map(func_get_args())->methods('GET');
		}
		/**
		 * POST Request
		 */
		public function post($route, $controller = null)
		{
			return $this->map(func_get_args())->methods('POST');
		}
		/**
		 * PUT Request
		 */
		public function put($route, $controller = null)
		{
			return $this->map(func_get_args())->methods('PUT');
		}
		/**
		 * DELETE Request
		 */
		public function delete($route, $controller = null)
		{
			return $this->map(func_get_args())->methods('DELETE');
		}
		/**
		 * PATCH Request
		 */
		public function patch($route, $controller = null)
		{
			return $this->map(func_get_args())->methods('PATCH');
		}

	/**
	 * Groups related to a Route
	 * @param  string $pattern Route pattern
	 * @param  array $middleware Middleware to include with each Route
	 * @return array [Grouped Route pattern, Middleware, Defaults]
	 */
	protected function groups($route, $middleware)
	{
		if(empty($this->groups))
			return [$route, $middleware, []];

		$rou = '';
		$mid = [];
		$def = [];
		foreach ($this->groups as $group) {
		 	$rou .= $group['route'].'/';

		 	if(!empty($group['middlewares']))
		 		$mid = array_merge($mid, $group['middlewares']);

		 	if(!empty($group['defaults']))
		 		$def = array_merge($mid, $group['defaults']);
		}

		return ['/'.$rou.trim($route, '/'), array_merge($mid, $middleware), $def];
	}
	/**
	 * Group REST routes
	 * @param  string $route      URL Route to route to
	 * @param  string $controller Controller class to route to
	 * @example $this->group($route, [$middleware1, $middleware2,...], $callable)
	 * @example For grouped default parameters, an array of defaults can be passed as the last parameter
	 *          $this->group($route, [$middleware1, $middleware2,...], $callable, ['version' => 1])
	 *          or
	 *          $this->group($route, [$middleware1, $middleware2,...], $callable, function(){
	 *          	return ['version' => 1];
	 *          });
	 * @return self
	 */
	public function group($route, $callable)
	{
		$args = func_get_args();
		$route = array_shift($args);
		$callable = array_pop($args);
		$defaults = is_array($callable) ? $callable : [];

		if(!!$defaults) 
			$callable = array_pop($args);

		$this->groups[] = $this->routeObject->route(trim($route, '/'), $callable)->uses($args)->defaults($defaults);

		$callable();

		array_pop($this->groups);
		return $this;
	}
	/**
	 * All kinds of Request types
	 * @return Route
	 */
	public function map($args)
	{
		$route = array_shift($args);
		$controller = array_pop($args);

		list($route, $middleware, $defaults) = $this->groups($route, $args);

		return $this->routes[] = $this->routeObject->route($route, $controller)->uses($middleware)->defaults($defaults);
	}

	public function dispatch($route)
	{
		return $this->routeObject->match($route);
	}
	/**
	* Returns Route parameters
	*
	* @return array If Route is found|new Route or all Queried URI params
	*/
	public function dump()
	{
		return !$this->matches() ? 'Router: No `Routes` found for url = `'.$this->url().'`' : $this->matches();
	}
}
