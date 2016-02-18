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

abstract Class Service extends \Push\Utils\Collection 
{
	protected 
		/**
		 * @var \Push\Application Instance of Push MVC
		 */
		$app;

	/**
	 * Start the Service
	 * @param \Push\Application $app Instance of the Application
	 * @return self
	 */
	abstract function start(\Push\Application $app);
	/**
	 * Stop the Service
	 * @param \Push\Application $app Instance of the Application
	 * @return self
	 */
	function stop(\Push\Application $app) {}
	/**
	 * Calls on the Service's boot method when invoked
	 * @param \Push\Application $app 
	 * @return self
	 */
	final function __invoke(\Push\Application $app)
	{
		if(method_exists($this, 'stop')){
			$service = $this;
			$app->on('app.after.run', function() use($service, $app){
				return $service->stop($app);
			});
		}
		return $this->start($app);
	}
}
