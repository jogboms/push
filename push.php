<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */

defined('REQUIRED_PHP_VERSION') or define('REQUIRED_PHP_VERSION', '5.4.0');

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
