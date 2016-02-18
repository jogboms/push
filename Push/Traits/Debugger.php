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
namespace Push\Traits;

trait Debugger 
{

	private static 
		$debug_mode = true,
		$debugger = [];

	/**
	 * Set Debugger Mode
	 * @param  int $debug_mode true|false
	 * @return [type]       [description]
	 */
	public function debug_mode($debug_mode = true)
	{
		static::$debug_mode = $debug_mode;
	}
	/**
	 * Adds a Debug message
	 * @param string $message Message to add to Debug
	 * @see trim_root() includes/functions.php
	 */
	public function debug($message)
	{
		// Save CPU the stress of adding
		if(static::$debug_mode === true)
			return;

		$trace = debug_backtrace();
		$info = $trace[0];
		$debug = $trace[1];

		$return = function($debug, $back = null){
			if(is_null($back))
				return (isset($debug['class']) ? $debug['class'].$debug['type'] : '').$debug['function'].'()';
			if(isset($debug['file']))
				return basename($debug['file']).'['.$debug['line'].']'.(isset($back['file']) ? ' -> '.basename($back['file']).'['.$back['line'].']' : '');
		};
		static::$debugger[] = array(
			'message' => $message,
			'folder' => trim_root($info['file']),
			'file' => basename($info['file']),
			'function' => $return($debug),
			'line' => $info['line'],
			'trace' => $return($debug, isset($trace[2]) ? $trace[2] : null)
			);
	}
	public function debug_last()
	{
		return array_pop(static::$debugger);
	}
	public function debug_render()
	{
		echo '<pre style="max-width: 640px;margin: 2em auto;background: white;border: solid 1pt #ddd;padding: 1em;word-wrap: break-word"';
		echo '<h1>Debug Info</h1><hr />';
		print_r(static::$debugger);
		echo '</pre>';
	}
}