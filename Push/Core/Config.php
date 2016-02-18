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

namespace Push\Core;

use Push\Utils\Collection;

Class Config extends Collection {
	private function __clone(){}
	
	function __construct($config){
		$config['PUSH'] = new Collection(json_decode(file_get_contents(dirname(PUSH).DS.'PUSH.json')), true);
		$config['mail'] = new Collection($config['mail'], true);
		$config['APP'] = new Collection($config['APP'], true);
		$config['db'] = new Collection($config['database'][$config['APP']['mode']], true);

		unset($config['database']);

		parent::__construct($config);
	}

}
