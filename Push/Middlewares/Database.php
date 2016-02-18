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

namespace Push\Middlewares;

Class Database extends \Push\Middleware 
{
	private $debug = true;

	function __construct($debug = true)
	{
		$this->debug = $debug;
	}

	/*
	Fix for whether Development or Production
	 */
	final public function call()
	{
		// Set Database
		$app = $this->app->setDB(function($config){
			$db = new \DB\Drivers\Mysqli(
					$config['host'], 
					$config['username'], 
					$config['password'], 
					$config['dbname'],
					$config['debug'] ? \DB\DB::DEVELOPMENT : \DB\DB::PRODUCTION 
				);
			return $db->charset('utf8');
		});

		$this->next->call();

		if($app->env('APP_USE_DEBUGGER')){
			echo '<pre><h3>Database Middleware</h3>';
			print_r($app->db->dump());
			echo '</pre>';
		}
	}
}
