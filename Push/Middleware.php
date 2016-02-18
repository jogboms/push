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

abstract class Middleware 
	implements \Push\Interfaces\MiddlewareInterface {

	// These variables have to be also implemented and made public to avoid uncertain issues
	public 
		/**
		 * Instace of Application
		 * @var null
		 */
		$app = null,
		/**
		 * Instance of Next Middleware in the Queue
		 * @var null
		 */
		$next = null;

	final public function __invoke($request = null, $response = null, $app = null){
		if($app) {
			if($request) $app->request = $request;
			if($response) $app->response = $response;
			$this->app = $app;
		}

		return $this->call();
	}
}
