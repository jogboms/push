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

interface ViewInterface {
	/**
	 * Initialize or Boot the View implementation instead of using __construct
	 * @return mixed
	 */
	public function initialize();
	/**
	 * Return Instance of Parser object
	 * @return object
	 */
	public function getEnvironment();
	/**
	 * Parse a template file
	 * @param  string $filename Filename of template file
	 * @return mixed
	 */
	public function parse($filename, $variables = []);
	/**
	 * Assign a value to a variable or an Array containing keys => values 
	 * @param  string|array $key   Name of variable to assign or Array of keys => values
	 * @param  mixed $value Value to assign to variable or NULL if $key is an Array
	 * @return self
	 */
	public function assign($key, $value = null);
}

