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
