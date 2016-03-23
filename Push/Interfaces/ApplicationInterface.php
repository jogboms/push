<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
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

