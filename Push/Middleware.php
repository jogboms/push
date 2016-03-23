<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */
namespace Push;

abstract class Middleware
  implements \Push\Interfaces\MiddlewareInterface {

  // These variables have to be also implemented and made public to avoid uncertain issues
  public
    /**
     * Instace of Application
     * @var null
     */
    $app = null,
    /**
     * Instance of Next Middleware in the Queue
     * @var null
     */
    $next = null;

  final public function __invoke($request = null, $response = null, $app = null){
    if($app) {
      if($request) $app->request = $request;
      if($response) $app->response = $response;
      $this->app = $app;
    }

    return $this->call();
  }
}
