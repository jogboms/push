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

	public function stop(\Push\Application $app){}
}
