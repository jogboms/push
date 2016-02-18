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

define('REQUIRED_PHP_VERSION', '5.4.0');

// Debug Purposes
function __($_ = []){echo '<pre>'; print_r($_); echo '</pre>';}
function ___($e = []){
	foreach(func_get_args() as $_)
		__($_);
	exit;
}
// gotten from composer autoload
// remove $this from the scope of the included file
function requireOnce($file, $readable = false){
	if(is_readable($file) or $readable) 
		require_once($file);
}

Class Autoload 
{
	static 
		$paths = [],
		$ext = '.php';

	public static function load($directory, $class)
	{
		$filename = str_replace(array('\\', '_', "\0"), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, ''), ltrim($class, '\\')).static::$ext;
		$file = $directory.DIRECTORY_SEPARATOR.$filename;

		if(!is_readable($file)){
			requireOnce($directory.DIRECTORY_SEPARATOR.strtolower($filename));
		}
		else {
			requireOnce($file, true);
		}
	}
	public static function import($path)
	{
		if(is_array($path)){
			foreach ($path as $ph) {
				static::import($ph);
			}
		}
		elseif(is_string($path)) {
			if(in_array($path, static::$paths))
				return;

			static::$paths[] = $path;
			spl_autoload_register(function($class) use($path){
				call_user_func(array(__CLASS__, 'load'), $path, $class);
			});
		}
	}
}

Autoload::import([__DIR__]);
