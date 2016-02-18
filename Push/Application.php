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

/*
* @constants
* ==========================================
*	DS -> Default Directory separator (alias: DIRECTORY_SEPARATOR)
*	NL -> Default End Of Line (alias: PHP_EOL)
*	WEB_DOMAIN -> Web domain address
*	WEB_ROOT -> Web Path to project's folder if any
*	ROOT_URI -> Web Path to project's folder if any (alias: WEB_ROOT)
*	PUSH -> Path to Push directory
*	APP -> Path to App directory (if it exists)
*	CONFIG -> Path to Configurations directory
*	STORAGE -> Path to Storage directory
*	LIBRARY -> Path to Libraries directory
*	INCLUDES -> Path to App/Includes directory (if it exists)
*	PUSH_INCLUDES -> Path to Push/Includes directory (if it exists)
*
* 	--- the following could be manipulated within config/config.php ---
* 	
*	ASSET_URI -> Web Path to project's asset files if any
*	ASSET_PATH -> Path to project's asset files 
*	TEMPLATE_PATH -> Path to Application templates directory used by view
*	UPLOADS -> Path to Application uploads directory (if it exists)
*	UPLOADS_URI -> Web Path to Application uploads files if any

* @events
* ==========================================
*	`app.before.run`
*	`app.before.index`
*	`app.before.render`
*	`app.after.render`
*	`app.after.index`
*	`app.after.run`
*
* ==========================================
*/

namespace Push;

if (version_compare(PHP_VERSION, REQUIRED_PHP_VERSION, '<')) 
	die('PHP v'.REQUIRED_PHP_VERSION.'+ is required. You are currently running on v'.PHP_VERSION.'.');

use Push\Utils\Set;
use Push\Utils\Collection;
use Push\Utils\Bench;

use Push\Core\Config;
use Push\Core\MiddlewareQueue;

use Push\Http\Environment;
use Push\Http\Request;
use Push\Http\Response;
use Push\Http\Route;
use Push\Http\Router;
use Push\Http\RouteObject;

use Push\Exceptions\ApiException;
use Push\Exceptions\OfflineException;
use Push\Exceptions\StopException;
use Push\Exceptions\NotFoundException;
use Push\Exceptions\MethodNotAllowedException;
use Push\Exceptions\SkipException;

defined('NL') or define('NL', PHP_EOL);
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

defined('DOC_ROOT') or define('DOC_ROOT', dirname(str_replace('/', DS, $_SERVER['SCRIPT_FILENAME'])));
defined('PUSH') or define('PUSH', dirname(__DIR__).DS.'push');
defined('CONFIG') or define('CONFIG', DOC_ROOT.DS.'config');

defined('PUSH_INCLUDES') or define('PUSH_INCLUDES', PUSH.DS.'includes');

require_once PUSH_INCLUDES.DS.'functions.php';

Class Application extends Collection 
	implements \Push\Interfaces\ApplicationInterface 
	{

	// Boot Events Bus
	use \Push\Traits\Events;

	// Boot Debugger
	use \Push\Traits\Debugger;

	const 
		DEVELOPMENT = 1, 
		PRODUCTION = 2;
	
	protected static 
		$init = null,
		$Modules = [];

	public  
		$onError,
		$onNotFound,
		$onShutdown,
		$onOffline;

	private 
		$debug = true, 
		$ran = false;

	final protected function __clone() {}

	public function __construct(array $params = [])
	{
		Bench::start('application');
		try {
			$this->boot($params);

			static::$init = $this;

		} catch (\Exception $e) {
			$this->onError->__invoke($e);
		}
	}

	public static function init()
	{
		if(isset(static::$init))
			return static::$init;
	}

	public function __call($method, $args)
	{
		throw new \Exception('Undefined Method: Application->'.$method.'()');
	}

	public static function __callStatic($method, $args)
	{
		throw new \Exception('Undefined Method: Application::'.$method.'()');
	}

	/**
	 * Utilities
	 */
		/**
		 * Register a Service|Closure that runs immediately it is added and then `stops` when the Application stops 
		 * in the order it which it was added
		 * 
		 * @param  Service|Closure $service Service to run
		 * @return self
		 */
		final public function register($service)
		{
			$service($this);
			return $this;
		}
		/**
		 * Adds a Middleware to the Application stack
		 * @param  Middleware|Closure $middleware Middleware
		 * @return self
		 */
		final public function uses($middleware)
		{
			$this->MiddlewareQueue->add($middleware);
			return $this;
		}
		/**
		 * Add Application Route Module
		 * @param string            $name   Name of Module
		 * @param \Push\Core\Module $module Module Instance
		 * @return self
		 */
		final public function addModule($name, \Push\Core\Module $module)
		{
			static::$Modules[$name] = $module;
			return $this;
		}
		/**
		 * Stops the Application flow process and displays a custom 404.tpl.php
		 * or the default.
		 * It passes the custom message which can be used within the 404.tpl.php
		 * 
		 * @param  mixed $message Custom error message to pass to page
		 * @throws OfflineException
		 * @return void
		 */
		final public function offline($message = null)
		{
			throw new OfflineException($message);
		}
		/**
		 * Stops the Application flow process and displays a custom 404.tpl.php
		 * or the default.
		 * It passes the custom message which can be used within the 404.tpl.php
		 * 
		 * @param  mixed $message Custom error message to pass to page
		 * @throws StopException
		 * @return void
		 */
		final public function stop($message = null)
		{
			throw new StopException($message);
		}
		/**
		 * Ends the Application by throwing an Exception that when caught, executes the NotFound handler
		 * @param  mixed $message Message to forward to NotFound Handler
		 * @throws NotFoundException
		 * @return void
		 */
		final public function show404($message = null)
		{
			throw new NotFoundException($message);
		}
		/**
		 * Skips a Found Route to the next found Route if any
		 * @throws SkipException
		 * @return void
		 */
		final public function skip()
		{
			throw new SkipException();
		}
		/**
		 * Adds a notFound Handler to the Application
		 * @param  Closure $callable [description]
		 * @return self
		 */
		public function notFound(\Closure $callable)
		{
			$req = $this->request; $res = $this->response;
			$this->onNotFound = function($e) use($req, $res, $callable){
				// ob_end_clean();
				$err = ['message' => $e->getMessage()];
				$callable($req, $res->status(404), $err);
			};
			return $this;
		}
		/**
		 * Adds a Shutdown Handler to the Application
		 * @param  Closure $callable [description]
		 * @return self
		 */
		public function onShutdown(\Closure $callable)
		{
			$req = $this->request; $res = $this->response;
			$this->onShutdown = function(array $err) use($req, $res, $callable){
				// ob_end_clean();
				$callable($req, $res->status(500), $err);
				echo $res;
			};
			return $this;
		}
		/**
		 * Adds an Error Handler to the Application
		 * @param  Closure $callable [description]
		 * @return self
		 */
		public function onError(\Closure $callable)
		{
			$req = $this->request; $res = $this->response;
			$this->onError = function(\Exception $e) use($req, $res, $callable){
				// ob_end_clean();
				$err = ['message' => $e->getMessage()];

				if(isset($t[0])){
					$err['folder'] = dirname(isset($t[0]['file']) ? $t[0]['file'] : $e->getFile());
					$err['file'] = basename(isset($t[0]['file']) ? $t[0]['file'] : $e->getFile());
					$err['line'] = isset($t[0]['line']) ? $t[0]['line'] : $e->getLine();
				}
				else {
					$err['folder'] = dirname($e->getFile());
					$err['file'] = basename($e->getFile());
					$err['line'] = $e->getLine();
				}

				$err['trace'] = ($t = $e->getTrace());
				$err['trace_string'] = ($t = $e->getTraceAsString());

				$callable($req, $res->status(500), $err);
			};
			return $this;
		}
		/**
		 * Adds a Offline Handler to the Application
		 * @param  Closure $callable [description]
		 * @return self
		 */
		public function onOffline(\Closure $callable)
		{
			$req = $this->request; $res = $this->response;
			$this->onOffline = function() use($req, $res, $callable){
				$callable($req, $res->status(503));
			};
			return $this;
		}

		final public function env($title=null, $value = null)
		{
			if($title === null)
				return $this->environment;
			if($value != null)
				return $this->environment->set($title, $value);
			return $this->environment[$title];
		}
		
		/**
		 * Set Database connection (optional)
		 * @param \Closure $callable A callback function that returns an Instance of the Database Access Model
		 * @example 
		 * $this->setDB(function(Collection $configuration){
		 * 	return new \PDO([$configuration]);
		 * });
		 */
		final public function setDB(\Closure $callable){	

			return $this->set('db', $callable($this->app['config']['db']));
		}
		/**
		 * Get Database Access
		 * If no database object exists, it creates a new Collection as the database option
		 * @return object Gets the Database Access passed into self::setDB() or Defaults to a new Collection
		 */
		final public function getDB()
		{
			if(!$this->has('db')){
				trigger_error('No Database was included into this Project using the Application $app->setDB() method.');
				$db = new Collection;
				$this->set('db', $db);
				return $db;
			}
				
			return $this->get('db');
		}

		final public function model($model)
		{
			$m = '\\'.basename(APP).'\\Model\\'.$model;
			return new $m($this->getDB());
		}
		final public function controller($controller)
		{
			$c = '\\'.basename(APP).'\\Controller\\'.$controller;
			return new $c($this);
		}

		final public function config($name)
		{
			return $this->config->get($name, null);
		}

		final public function request()
		{
			return $this->request;
		}
		final public function response()
		{
			return $this->response;
		}
		final public function view()
		{
			return $this->view;
		}

	/**
	 * Implementation from Set
	 */
		public function normalize($name)
		{
			return strtolower($name);
		}

	/**
	 * Application Boot
	 */
	final private function boot($params)
	{
		// Boot Request
		$this->request = new Request;

		$config = require CONFIG.DS.'config.php';

		$domain = $this->request->getUri()->getScheme().'://'.$this->request->getHost();
		$root = str_replace('/index.php', '', $this->request->server('SCRIPT_NAME'));

		defined('WEB_DOMAIN') or define('WEB_DOMAIN', $domain);
		defined('WEB_ROOT') or define('WEB_ROOT', empty($root) ? '/': $root);

		defined('ROOT_URI') or define('ROOT_URI', rtrim(WEB_DOMAIN.WEB_ROOT, '/'));

		$this->config = new Config($config += [
			'domain' => WEB_DOMAIN,
			'root' => WEB_ROOT,
			]);

		$this->debug = $this->config->debug;

		// Boot Environment
		$this->environment = function($c){
			return new Environment($c['config'], $c['request']);
		};

		defined('APP') or define('APP', RootPath($this->environment['APP_DIR']));
		defined('STORAGE') or define('STORAGE', RootPath($this->environment['APP_STORAGE_DIR']));
		defined('LIBRARY') or define('LIBRARY', RootPath($this->environment['APP_LIBRARY_DIR']));
		defined('INCLUDES') or define('INCLUDES', APP.DS.'includes');

		// alert(get_defined_constants());
		// Add App and Library to global Autoload Namespace if default Autoload class is included
		if(class_exists('Autoload')){
			\Autoload::import([APP, dirname(APP), LIBRARY]);
		}

		// Set Debugger Mode
		$this->debug_mode($this->environment['APP_USE_DEBUGGER']);

		// Boot Uploads
		defined('UPLOADS') or define('UPLOADS', RootPath($this->environment['APP_UPLOADS_DIR'], true));
		defined('UPLOADS_URI') or define('UPLOADS_URI', ROOT_URI.'/'.trim($this->environment['APP_UPLOADS_DIR'], '/'));

		// Boot View and Templating
		defined('ASSET_URI') or define('ASSET_URI', ROOT_URI.'/'.trim($this->environment['APP_ASSET_DIR'], '/'));
		defined('ASSET_PATH') or define('ASSET_PATH', RootPath($this->environment['APP_ASSET_DIR'], true));
		defined('TEMPLATE_PATH') or define('TEMPLATE_PATH', RootPath($this->environment['APP_TEMPLATE_DIR']));

		// Allow Configurable options via Application::__construct(Array $options)
		$this->set($params);

		if(!isset($this->view)){
			$this->view = function($c){
				return new \Push\Views\Php($c['environment'], $c['request']);
			};
		}

		// Boot Response
		$this->response = function($c){
			return new Response($c['request'], $c['view']);
		};
		
		// Boot Middleware Runner
		$this->MiddlewareQueue = new MiddlewareQueue($this);

		// Boot Errors Manager (optional)
		if($this->environment['APP_DEBUG']){
			$this->register(new \Push\Services\Error);
		}

		// Boot Route Manager
		$routeObject = new RouteObject($this->request->url(), $this->request->getMethod());
		$this->router = new Router($routeObject);

		// Boot Application Events
		if(file_exists(INCLUDES.DS.'events.php'))
			include INCLUDES.DS.'events.php';

		return $this;
	}

	final private function prepareRoute(Route $R)
	{
		// Set Route parameters into Request object
		$req = $this->request->set($R['params']);

		// Route with Controllers and/or Actions
		if(is_int($R['controller']) || is_string($R['controller'])){
			$_ctrl = strtolower($R['controller']);
			$ctrl = '\\'.basename(APP).'\\Controller\\';

			if($req->has('module')){
				foreach (self::$Modules as $name => $module) {
					if(strtolower($req->params('module')) === strtolower($name)){
						// Add Controller Path from module
						$ctrl .= trim($module->getControllerPath(), '\\').'\\';

						// Add controller parameter or use DEFAULT 
						$ctrl .= ($_ctrl == strtolower($name) ? $this->env('APP_DEFAULT_CONTROLLER') : $_ctrl);
						break;
					}
				}
			}
			// Add controller parameter or use DEFAULT 
			else $ctrl .= $_ctrl ?: $this->env('APP_DEFAULT_CONTROLLER');

			if(!class_exists($ctrl))
				$this->show404('Controller: `'.$_ctrl.'` does not exists in '.trim_root(dirname($ctrl)).' directory [OR] No matching `Route` was available for it.');

			// Use Action parameter or use DEFAULT 
			$act = $R['action'] ? strtolower($R['action']): $this->env('APP_DEFAULT_ACTION');

			$this->env('__CONTROLLER__', $ctrl);
			$this->env('__ACTION__', $act);
			$this->env('__PARAMS__', $req->params(true));

			unset($root);unset($ctrl);unset($_ctrl);unset($act);
		}
		// Route with Callbale Closure functions
		elseif(is_callable($R['controller'])){
			$this->env('__CONTROLLER__', function() use($R){
				return $R['controller'];
			});
		}
	}

	/**
	 * Call function since the App object also serves as a Middleware
	 * @return Response
	 */
	final public function call()
	{
		$this->ran = true;
		$req = $this->request; $res = $this->response;

		/* Create App Global Variable for Javascript */
		$JS = new Collection($this->env()->params());

		$this->view->JS("
			var Push = {};
			Push.App = ".json_encode(
				$JS->remove([
					'APP_DEFAULT_CONTROLLER', 'APP_DEFAULT_ACTION', 'APP_DB_ENGINE',
					'APP_USE_THEME','AUTO_LOGOUT','STRICT_DEBUG','REFRESH_CSS',
				])
				->set('Root', ROOT_URI)
				->set('Assets', ASSET_URI)
				->params())
			." ; Push.Req = ".json_encode($req->params(true))." ;
		", 'top');

		/* Here comes the magic :-) */
		// For Closures-type controllers
		if($this->env('__CONTROLLER__') instanceof \Closure){
			$res = $this->environment['__CONTROLLER__']($req, $res);
		}
		// For Object-type controllers
		else {
			$obj = $this->controller = new $this->environment['__CONTROLLER__']($this);
		}

		if(isset($obj)){
			/* Run onBefore method on every Controller if it exists */
			if(method_exists($obj, 'onBefore')) $obj->onBefore($req, $res);

			/* Run Initialize/Constructor method on every Controller if it exists */
			if(method_exists($obj, 'initialize')) 
				$obj->initialize($req, $res);

			/* load onAjax method if it exists */
			if($req->isAjax() && method_exists($obj, 'onAjax')) 
				$obj->onAjax($req, $res);

			if($req->isGet()) $rM = 'onGet';
			else if($req->isPost()) $rM = 'onPost';
			else if($req->isPut()) $rM = 'onPut';
			else if($req->isPatch()) $rM = 'onPatch';
			else if($req->isDelete()) $rM = 'onDelete';

			// load the REST methods; 
			if(method_exists($obj, $rM)) $obj->$rM($req, $res);
		}

		$this->emit('app.before.action');

		if(isset($obj)){
			// Route Action page for non-RESTful Controllers
			if(!$obj->isRESTful()){
				if(!method_exists($obj, $this->env('__ACTION__'))){
					throw new \Exception($this->env('__CONTROLLER__').': `'.$this->env('__ACTION__').'` action does not exist.');
				}
				$obj->{$this->env('__ACTION__')}($req, $res);
			}
		}

		$this->emit('app.after.action');

		if(isset($obj)){
			/* Run onAfter method on every Controller if it exists */
			if(method_exists($obj, 'onAfter')) $obj->onAfter($req, $res);
		}
		/* end magic */
		unset($obj); unset($req); unset($res);
		return $this->response;
	}

	final public function run()
	{
		ob_start();
		$this->emit('app.before.run');

		$env = $this->env();

		try {
			if(!in_array($this->request->getMethod(), $this->request->getAllowedMethods())){
				throw new MethodNotAllowedException();
			}

			if(!$this->env('APP_LIVE')){
				$this->debug('Application couldn\'t RUN because it was set to offline Mode in the `config.php` file or the $app->offline() method was called');
				$this->offline();
			}

			if($this->env('APP_DEBUG')){
				$this->router->any('/push_mvc_environment', function($req, $res) use($env){
					// $res->send(pre($env->params()));
					dump($env->params());
				});
			}

			// Use the `controller/action/[params...]` pattern as default route
			if($this->env('APP_USE_MVC')){
				$this->router->any(':$controller/:$action/:$id');
				$this->router->any(':$controller/:$action');
				$this->router->any(':$controller');
			}

			// Default Start Page
			$this->router->any('/', function($req, $res) use($env){
					$res->send('
						<html> <body> <style>body{font-family:"Segoe UI";max-width:600px;width:100%;margin:2em auto;padding:15px;}
						h1{font-size:4rem;}h2{font-weight:500;}h1,h2{line-height:1.55;letter-spacing:1.1;font-family:"Segoe UI Light"}</style>
						<h1>Hey! It Works.</h1> <h2>This is the lightweight Push MVC framework for Rapid Web Development everyone is talking about.</h2>
						<p>Get Started!<br /><a href="push_mvc_environment">Environment Variables!</a>&nbsp;/&nbsp;<a href="world">Hello World!</a></p><br /><br />'.$env->get('LICENSE').'</body></html>'
						);
				});

			foreach ($this->router->routes() as $_r) {
				try {
					if(!($r = $this->router->dispatch($_r))) 
						continue;

					$env->set('APP_ROUTE', $r['route']);
					$this->prepareRoute($r);

					// Attach Route Middlewares
					foreach ($r['middlewares'] as $m){
						$this->uses($m);
					}

					// Add application as final Middleware
					$this->uses($this);
					// Run middleware process
					$this->response = $this->MiddlewareQueue->run();
					break;
				} catch (SkipException $e){ 
					$this->MiddlewareQueue->quit();
					// Remove maiddleware for skipped Route
					for($i = (count($r['middlewares'])+1);$i>0;$i--){
						$this->MiddlewareQueue->queue->pop();
					}
					continue;
				}
			}

			// No found Route
			if(!$r){
				$this->show404();
			}

			// Break in Middleware $next chain
			if(!$this->ran){
				throw new \Exception('Application did not `run`, It seems one of your `Middlewares` refused to do either `$this->next->call()` or `$next()`.');
			}

			$this->debug('Application RAN successfully at '.Date('Y-m-d H:m:s A'));
		}
		catch(\Exception $e){
			// Handle All Exceptions
			$this->exceptions($e);
		}

		$this->emit('app.before.render');

		$this->display($this->response);

		$this->emit('app.after.render');

		$this->end();
		$this->emit('app.after.run');
	}

	final private function exceptions(\Exception $e)
	{
		$env = $this->env();
		if($e instanceof NotFoundException) {
			if(!$this->onNotFound){
				// Default notFound Handler
				$this->notFound(function($req, $res, $err) use($env){
						$res->send('<div style="font-family:\'Segoe UI Light\';text-align:center;overflow-wrap:break-word;max-width:640px;margin:3em auto">
							<h2 style="color:red;font-size:250%;font-weight:500">Ooops! 404 Error</h2> <p style="line-height:1.75;">The page you requested for is not available on our server.</p>
							<br /><pre style="line-height:1.75;background:#eee;padding:15px">'.$err['message'].'</pre><br /><br />'.$env->get('LICENSE').'</div>'
						);
					});
			}
			$this->onNotFound->__invoke($e);
		}
		elseif($e instanceof MethodNotAllowedException) {
			$this->response->status(405)->send('<div style="font-family:\'Segoe UI Light\';text-align:center;overflow-wrap:break-word;max-width:640px;margin:3em auto">
						<h1 style="color:red;font-size:250%;font-weight:500">Method Is Not Allowed</h1><p>The Request Method used is not supported.</p><br /><br />'.$env->get('LICENSE').'</div>');
		}
		elseif($e instanceof StopException) {
			// Nothing actually happends
			// It stops every other Application Process
		}
		elseif($e instanceof OfflineException) {
			if(!$this->onOffline){
				// Default offline Handler
				$this->onOffline(function($req, $res) use($env){
					$res->send('<div style="font-family:\'Segoe UI Light\';text-align:center;overflow-wrap:break-word;max-width:640px;margin:3em auto">
						<h1 style="color:red;font-size:250%;font-weight:500">Temporarily Offline</h1><p>Application couldn\'t RUN because it was 
						set to offline Mode in the `config.php` file</p><br /><br />'.$env->get('LICENSE').'</div>'
					);
				});
			}
			$this->onOffline->__invoke();
		}
		elseif($e instanceof \Exception) {
			if(!$this->onError){
				// Default Error Handler
				$this->onError(function($req, $res, $err) use($env){
					$res->send('<div style="font-family:\'Segoe UI Light\';text-align:center;overflow-wrap:break-word;max-width:640px;margin:3em auto">
						<h1 style="color:red;font-size:250%;font-weight:500">IT IS REQUIRED THAT ALL ERRORS BE FIXED BEFORE SWITCHING OFF DEBUG MODE.</h1>
						<pre style="line-height:1.75;background:#eee;padding:15px">'.$err['message'].'</pre></div>
						');
				});
			}
			// Handles any error generated by our application
			$this->onError->__invoke($e);
		}
	}

	final private function display(Response $response)
	{
        // stop PHP sending a Content-Type automatically
        // ini_set('default_mimetype', '');
		$ob = ob_get_contents();

		if(ob_get_length()) ob_end_clean();

		$body = '';
		if(!$response->isEmpty()){
			$body = $response->getBody();

			if(!is_string($body)){
				$body = json_encode($body);
				$isJson = true;
			}

			if($this->request->wantsJson() || isset($isJson)){
				$response->setContentType('application/json');
			}

			// $response->setContentType('text/html');
		}

		if(stripos($this->request->accepts(), '/html')){
			$body .= $ob;
		}

		if(!headers_sent()){
			// Set main header
			header(sprintf('HTTP/%s %s %s', 
				$response->getVersion(), $response->getStatusCode(), $response->getStatusText()
				));

			// Set all other corresponding headers
			foreach($response->getHeaders() as $header){
				header($header);
			}
		}

		echo $body;
	}

	final public function end()
	{
		$this->debug('Application `ended` sucessfully');
		if($this->env('APP_USE_DEBUGGER')){
			$this->dump();
		}
	}

	private function dump()
	{
		pre([
			'App' => Bench::stop('application'), 
			'Router' => $this->router->dump(),
			'Debug'	=> $this->debug_last(), 
			'Event'	=> $this->events_log(),
			'Events' => $this->events(),
			// 'Debug'	=> $this->debug_render(), 
			]);
		pre('You can disable this by setting `use_debugger` => `false` within the `config.php` file');
	}
}
