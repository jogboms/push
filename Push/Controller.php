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

namespace Push;

abstract Class Controller extends \Push\Utils\Set {
	public $isRESTful = false;
	protected static $data = [];
	protected $errors = [];

	final public function __construct($app)
	{

		$app->response->assign('pagination', ($this->pagination = new \Push\Components\Pagination));

		$this->action = $app->environment['__ACTION__'];
		$this->params = $app->environment['__PARAMS__'];

		$this->view = $app->view;
		/**
		 * @todo Add a validation function.
		 * if it returns something [string], pass this string or [object] of strings to a method called [onError]
		 */
		if(method_exists($this, 'validate'))
			$this->validate($app->request, $app->response);

		$this->app = $app;
	}

	final public function set($key, $value)
	{
		static::$data[$key] = $value;
		return $this;
	}

	final public function get($key, $default = null)
	{
		return $this->has($key) ? static::$data[$key] : $default;
	}

	final public function params()
	{
		return static::$data;
	}

	final public function has($key)
	{
		return array_key_exists($key, static::$data);
	}

	final public function remove($key)
	{
		unset(static::$data[$key]);
		return $this;
	}

	/**
	 * Return an Instance of a given namespaced Model
	 * @param  string $name Name of Model class
	 * @return object \Push\Model
	 */
	public function model($name)
	{
		return $this->app->model($name);
	}
	/**
	 * Checks if a COntroller is a RESTful one based on the $isRESTful parameter
	 *
	 *  @return bool
	 */
	final public function isRESTful()
	{
		return !!$this->isRESTful;
	}
	/**
	 * Success function if validation returns true
	 * @param  function $func callback function to run if Validation return true
	 * @return Object       Instance of Controller for chainability
	 */
	final public function onSuccess($func = null)
	{
		if(is_callable($func) && empty($this->errors))
			$func();
		return $this;
	}
	/**
	 * Error function if validation fails
	 * @param  function $func callback function to run if validation fails
	 * @return Object       Instance of Controller for chainability
	 */
	final public function onError($func = null)
	{
		if(is_callable($func) && !empty($this->errors))
			$func($this->errors);
		return $this;
	}
	/**
	 * Add an error string if validation fails
	 * @param  String $error Error string to return from validation
	 * @return Object       Instance of Controller for chainability
	 */
	final public function error($error)
	{
		$this->errors[] = $error;
		return $this;
	}

	/**
	 * Handles the Controller initialization
	 * 
	 * @param  Request $request  Request Object parameters and methods
	 * @param  Response $response Response Object parameters and methods
	 * @return void
	 */
	final public function initialize($request, $response){}

}