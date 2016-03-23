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

Class Admin extends \Push\Core\Module
{
  public function getControllerPath()
  {
    return 'admin\\';
  }

  final public function start(\Push\Application $app)
  {
    $app->addModule('admin', $this);

    $app->router->group('/admin:$module', function() use($app) {

      $app->router->any('/', 'index');

      $app->router->group('/:$controller', function() use($app) {
        $app->router->any('/');
        $app->router->any('/(num):$id');

        $app->router->group('/:$action', function() use($app) {
          $app->router->any('/');
          $app->router->any('/(num):$id');

          $app->router->group('/:$option', function() use($app) {
            $app->router->any('/');
            $app->router->any('/(num):$id');
          });
        });
      });
    });

    $this->app = $app;
  }
}
