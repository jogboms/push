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


namespace Push\Utils;

Class Uri 
{
	
	public function __call($method, $args)
	{
		switch($method){
			case 'home':
				return ROOT_URI.'/';
			case 'auth':
				return (ROOT_URI.'/'.implode('/', $args));
			default:
				return (ROOT_URI.'/'.$method.'/'.str_replace('//', '/', implode('/', $args)));
		}
	}

	public static function __callStatic($method, $args)
	{
		switch($method){
			case 'home':
				return ROOT_URI.'/';
			case 'auth':
				return (ROOT_URI.'/'.implode('/', $args));
			default:
				return (ROOT_URI.'/'.$method.'/'.str_replace('//', '/', implode('/', $args)));
		}
	}

	public static function go($path)
	{
		return ROOT_URI.'/'.$path;
	}
	public static function url()
	{
	   $e = explode('/', self::protocol());
	   return strtolower(array_shift($e)).'://'.self::host().$_SERVER['REQUEST_URI'];
	}

	public static function host()
	{
		return $_SERVER['HTTP_HOST'];
	}
	public static function protocol()
	{
		return $_SERVER['SERVER_PROTOCOL'];
	}
	public static function self()
	{
		return self::url();
	}
	public static function back()
	{
		return Application::init()->env('URL_BACK');
	}

	public static function this($url = '')
	{
		return self::url().$url;
	}
	
	public static function hash($url = '')
	{
		// return rtrim(self::url(), '/').'#'.trim($url, '/');
		// return '#/'.trim($url, '/');
		// return '/#/'.trim($url, '/');
		return rtrim(self::url(), '/').'/#/'.trim($url, '/');
	}
	public static function root_hash($url = '')
	{
		return self::go('#/'.trim($url, '/'));
	}
}