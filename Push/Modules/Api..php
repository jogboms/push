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

	public function stop(\Push\Application $app){}
}
