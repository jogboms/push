<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */

namespace Push\Core;

use Push\Utils\Collection;

Class Config extends Collection {
    private function __clone(){}

    public function __construct($config){
    $push = json_decode(file_get_contents(dirname(PUSH).DS.'composer.json'));
        $push->name = ucfirst(explode('/', $push->name)[1]);
    $config['PUSH'] = new Collection($push, true);
        $config['mail'] = new Collection($config['mail'], true);
        $config['APP'] = new Collection($config['APP'], true);
        $config['db'] = new Collection($config['database'][$config['APP']['mode']], true);

        unset($config['database']);

        parent::__construct($config);
    }
}
