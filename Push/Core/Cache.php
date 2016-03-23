<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */


/**
*
* Cache::get($cache_name, $default);
*
* $default = function(){
*   // If $cache_name not exist
* };
*
*/

namespace Push\Core;

defined('CACHE') or define('CACHE', STORAGE.DS.'caches');

Class Cache
{
  public static $_init; // Contains class instance
  public static $cache_path; // Absolute path to cache files
  public static $enabled = true; // Cache name
  public $ext_path; // specific cache path
  public $expire = 604800; // Expiration timespan
  public static $cache_name; // Cache name
  public static $cache_file; // Cache filename
  public static $cache_log = []; // Cache Log

  const CACHE_PATH = CACHE, CACHE_EXT = '.cache';

  public static function get()
  {
    $args = func_get_args();
    $cache_name = str_replace('/', DS, array_shift($args));

    if(empty($cache_path))
      $cache_path = static::$cache_path = static::CACHE_PATH;
    $cache_path .= DS.rtrim(trim(dirname($cache_name), DS), '.');

    if(!file_exists($cache_path))
      static::mkdir($cache_name);

    static::$cache_path = $cache_path;

    if(static::$enabled === false)
      static::$cache_log = (array)'Cache is not Enabled';

    if(static::check($cache_name) && static::$enabled){
      static::log($cache_name, static::$cache_path);
      return static::_get();
    }

    if(is_callable($result = array_pop($args)))
      $result = call_user_func_array($result, $args);
    return static::_set($result) ? static::_set($result) : $result;
  }
  public static function set($cache_name, $cache_contents)
  {
    static::$cache_file = $cache_name;
    static::$cache_path = static::CACHE_PATH;
    if(!file_exists(static::$cache_path))
      static::mkdir(static::$cache_file);

    return static::_set($cache_contents);
  }

  public static function delete($cache_name = null, $empty = false)
  {
    static::$cache_path = static::CACHE_PATH;
    if($empty === TRUE or $cache_name === TRUE) {
      static::_gbc(false);
    } else {
      if(is_array($cache_name)){
        foreach($cache_name as $cache_name) {
          if(file_exists(static::$cache_path.DS.static::file($cache_name)))
            unlink(static::$cache_path.DS.static::file($cache_name));
        }
      } else {
        if(file_exists(static::$cache_path.DS.static::file($cache_name)))
          unlink(static::$cache_path.DS.static::file($cache_name));
      }
    }
  }
  public static function flush($path_name = null, $clear_previous_path = true)
  {
    if($clear_previous_path === true) static::$cache_path = '';
    if(!empty(static::$cache_path)){
      if(!is_null($path_name))
        static::$cache_path .= DS.$path_name;
    } elseif(is_dir(static::CACHE_PATH.DS.$path_name)) {
      static::$cache_path = static::CACHE_PATH.DS.$path_name;
    }
    static::_gbc(false);
  }
  public static function _gbc($expire = true)
  {
    if(empty(static::$cache_path))
      static::$cache_path = static::CACHE_PATH;

    $files = glob(static::$cache_path.DS.'*'.static::CACHE_EXT);

    if (empty($files))
      return;
    foreach($files as $cache){
      @chmod($cache, '0755');
      if($expire === TRUE){
        if((time()-@filemtime($cache)) > static::$expire)
          @unlink($cache);
      }
      else @unlink($cache);
    }
  }

  private static function check($cache_name)
  {
    static::$cache_file = static::file($cache_name);
    return file_exists(static::$cache_path.DS.static::$cache_file);
  }
  private static function mkdir($cache_name)
  {
    $ex = static::$cache_path;
    foreach(explode(DS, dirname($cache_name)) as $path){
      if(!file_exists($ex.DS.$path))
        mkdir($ex.DS.$path, 0755);
      $ex .= DS.$path;
    }
  }
  private static function _set($cache_contents)
  {
    if ((is_array($cache_contents) or is_object($cache_contents)) and !empty($cache_contents)) {
      $_contents = serialize($cache_contents);
    } elseif(!empty($cache_contents)) {
      $_contents = serialize($cache_contents);
    }

    if(isset($_contents)){
      file_put_contents(static::$cache_path.DS.static::$cache_file,$_contents);
      chmod(static::$cache_path.DS.static::$cache_file, 0777);
      return $cache_contents;
    }
    return false;
  }
  private static function _get()
  {
    chmod(static::$cache_path.DS.static::$cache_file, 0777);
    return unserialize(file_get_contents(static::$cache_path.DS.static::$cache_file));
  }


  public static function name($cache_name = null)
  {
    return $cache_name ? (static::$cache_name = strtolower(trim($cache_name))) : static::$cache_name;
  }
  public static function path($path_name = null)
  {
    if(!$path_name) return static::$cache_path;
    if(empty(static::$cache_path))
      static::$cache_path = static::CACHE_PATH;
    static::$cache_path .= DS.$path_name;
  }
  public static function file($cache_name = null)
  {
    return $cache_name ? (static::$cache_file = md5(static::name($cache_name)).static::CACHE_EXT) : static::$cache_file;
  }

  public static function log($file_name  = null, $directory = null)
  {
    if(is_null($file_name))
      return static::$cache_log;
    list($a, $b) = debug_backtrace();
    static::$cache_log[] = array(
      'Cache_Path' => trim_root($directory),
      'Cache_Name' => $file_name,
      'Cache_Size' => byte_size(filesize($directory.DS.static::$cache_file)),
      'BackTrace'=> array(
        'Folder' => trim_root(dirname($b['file'])),
        'File' => basename($b['file']),
        'Line' => $b['line']
        )
      );
  }
}
