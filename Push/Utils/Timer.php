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

Class Timer
{
  public $start=0, $end=0, $speed=0;
  static public $init;

  static function init()
  {
    return (isset(self::$init)) ? self::$init: self::$init = new self;
  }
  static function start()
  {
    return self::init()->start = microtime(true);
  }
  static function end()
  {
    return self::init()->end = microtime(true);
  }
  static function speed()
  {
    return self::init()->speed = round(self::init()->end()-self::init()->start, 4);
  }
}
