<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */
namespace Push\Utils;

Class Bench
{
  static public $_init;
  static public $_benchs = [];
  static public $_this = [];

  static public function init()
  {
    self::$_init['timer'] = self::getTime();
    self::$_init['memory'] = self::getMemory();
    self::$_init['peak'] = self::getMemory(true);
  }
  static public function start()
  {
    self::set('bencher');
  }
  static public function stop()
  {
    return self::end();
  }
  static public function end($use_handler = false)
  {
    if($use_handler){
      self::$_this = self::get('bencher');
      return new BenchHandler;
    }
    return self::get('bencher');
  }
  static public function set($tag)
  {
    self::$_benchs[$tag] = array(
        'timer'=>self::getTime(),
        'memory'=>self::getMemory(),
        'peak'=>self::getMemory(true)
        );
  }

  static public function get($tag, $use_handler = false)
  {
    self::$_this = array(
        'speed'=>round(self::getTime()-self::$_benchs[$tag]['timer'], 4).'s',
        'load'=>self::byte(self::getMemory()-self::$_benchs[$tag]['memory']),
        'peak'=>self::byte(self::getMemory(true)-self::$_benchs[$tag]['peak'])
        );
    return !$use_handler ? self::$_this : new BenchHandler;
  }
  static public function all()
  {
    $_benchs = [];
    foreach (self::$_benchs as $title => $bench) {
      $_benchs += array($title => array(
          'speed'=>round($bench['timer']-self::$_init['timer'], 4).'s',
          'load'=>self::byte($bench['memory']-self::$_init['memory']),
          'peak'=>self::byte($bench['peak']-self::$_init['peak'])
          ));
    }
    return $_benchs;
  }
  static public function getTime()
  {
    return microtime(true);
  }
  static public function getMemory($peak=false)
  {
    return ($peak===true) ? memory_get_peak_usage(true) : memory_get_usage(true);
  }
  static public function flush($tag = null)
  {
    if(is_null($tag)) self::$_benchs = [];
    else unset(self::$_benchs[$tag]);
  }
  static public function byte($byte = 0)
  {
    $a = ['b','Kb','Mb','Gb','Tb'];
    if($byte>0){
      for($i=0; $i<=count($a); $i++){
        if(($byte/pow(2, $i*10)) < 1)
          return round($byte/pow(2, ($i-1)*10), 2).$a[$i-1];
      }
    }
    return round($byte, 2).$a[0];
  }
}

class BenchHandler extends Bench   {

  public function speed()
  {
    return parent::$_this['speed'];
  }
  public function load()
  {
    return parent::$_this['load'];
  }
  public function format()
  {
    return parent::$_this['speed'];
  }
}
