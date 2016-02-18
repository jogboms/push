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
namespace Push\Interfaces;

interface ApplicationInterface {
	public function setDB(\Closure $callable);
	public function getDB();

	public function model($model);
	public function controller($controller);

	public function env($title = null, $value = null);
	public function request();
	public function response();
	public function view();
	public function config($parameter);

	public function skip();
	public function stop($message);
	public function show404($message);
	public function offline($message);
	
	public function onError(\Closure $callable);
	public function onOffline(\Closure $callable);
	public function onShutdown(\Closure $callable);
	public function notFound(\Closure $callable);

	public function addModule($name, \Push\Core\Module $module);

	public function on($event_name, $callable);
	public function once($event_name, $callable);
	public function emit($event_name);
	public function off($event_name);
	public function events();


	public function uses($middleware);
	public function register($service);

	public function end();
}

