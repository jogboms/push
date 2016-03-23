<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */

namespace Push\Middlewares;

Class Database extends \Push\Middleware
{
  private $debug = true;

  function __construct($debug = true)
  {
    $this->debug = $debug;
  }

  /*
  Fix for whether Development or Production
   */
  final public function call()
  {
    // Set Database
    $app = $this->app->setDB(function($config){
      $db = new \DB\Drivers\Mysqli(
              $config['host'],
              $config['username'],
              $config['password'],
              $config['dbname'],
              $config['debug'] ? \DB\DB::DEVELOPMENT : \DB\DB::PRODUCTION
          );
      return $db->charset('utf8');
    });

    $this->next->call();

    if($app->env('APP_USE_DEBUGGER')){
      echo '<pre><h3>Database Middleware</h3>';
      print_r($app->db->dump());
      echo '</pre>';
    }
  }
}
