<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */
namespace Push\Modules;

use Push\Exceptions\ApiException;

Class Api extends \Push\Core\Module
{
  public function getControllerPath()
  {
    if(!$this->app->request->has('version'))
      throw new ApiException("The API architecture requires that you add a `version` parameter to the address. e.g /api/1.1/create. `version` is `1.1`");

    // Add version parameter
    return 'api\\v'.$this->app->request->params('version').'\\';
  }

  final function start(\Push\Application $app)
  {
    $app->addModule('api', $this);

    $app->router->group('/api:$module', function() use($app) {
      $app->router->group('/(num):$version', function() use($app) {
        $app->router->any('/', 'index');
        $app->router->any('/:$action/', 'index');
        $app->router->any('/:$action/(num):$id', 'index');
        $app->router->any('/:$controller/:$action');
        $app->router->any('/:$controller/:$action/(num):$id');
      });

      $app->router->any('/:$action', 'index');
      $app->router->any('/:$controller/:$action');

      $app->router->any('/(num):$version/:$controller/{json|xml}:$action');
    }, [
      'version' => 1
    ]);

    $this->app = $app;
  }
}
