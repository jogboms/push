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

namespace Push\Core;

use Push\Interfaces\MiddlewareInterface;

final Class MiddlewareObject implements MiddlewareInterface 
{
	
	private 
		/**
		 * Callable Middleware
		 * @var null
		 */
		$MW = null,
		/**
		 * Contains Next Middleware
		 * @var null
		 */
		$next = null;

	/**
	 * Process Middleware `call` function or Invoke Callable function 
	 * @return mixed
	 */
	final public function call()
	{
		$MW = $this->MW;
		if($MW instanceof \Closure)
			return $MW($this->next);

		$MW->next = $this->next;
		return $MW->call();
	}

	/**
	 * Create a Wrapper for each Middleware and 
	 * sets the Next parameter on the previous Middleware
	 * 
	 * @param callable $MW 	Callable Middleware
	 * @param mixed $app      		Application Object
	 * @param MiddlewareInterface $previous     Previous Middleware
	 */
	final function __construct($MW, $app, MiddlewareInterface $previous = null)
	{
		if($MW instanceof \Closure){
			$this->MW = function($next) use($MW, $app){
				return $MW($app->request, $app->response, $next);
			};
		} elseif(
			($MW instanceof MiddlewareInterface) ||
			(is_object($MW) && method_exists($MW, 'call'))
			){
				$this->MW = $MW;
				$this->MW->app = $app;
		}
		if($previous) $previous->next = $this;
	}

	final public function __invoke()
	{
		if($this->MW) return $this->call();
	}
}
