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

/**
 * Class Application {
 * 	function __construct(){
 * 		$this->request = [];
 * 		$this->response = [];
 * 		// or
 * 		$this->request = new \stdClass;
 * 		$this->response = new \stdClass;
 * 	}
 * }
 *
 * $application = new Application;
 * 
 * or
 * 
 * $application = new \stdClass()
 * $application->request = new \stdClass()
 * $application->response = new \stdClass()
 * 
 * -------------------------------------------
 * 
 * $MiddlewareQueue = new MiddlewareQueue($application);
 * 
 * $MiddlewareQueue->add(function($req, $res, $next){
 *    echo 'A ';
 *    $next();
 *    echo 'A_2 ';
 * });
 *
 * Class OneMiddleware extends Middleware {
 *    public function call(){
 *       echo 'B ';
 *       $this->next->call();
 *       echo 'B_2 ';
 *	}
 * }
 * $MiddlewareQueue->add(new OneMiddleware);
 *
 * Class Two {
 *    public function call(){
 *       echo 'C ';
 *       $this->next->call();
 *       echo 'C_2 ';
 * 	}
 * }
 * $MiddlewareQueue->add(new Two);
 * 
 * $MiddlewareQueue->add(function($req, $res, $next){
 *    echo 'D ->';
 *    $next();
 *    echo 'D_2 ';
 * });
 *
 * $MiddlewareQueue->run();
 *
 * RESULT:
 * A B C D -> D_2 C_2 B_2 A_2
 * 
 */

Class MiddlewareException extends \Exception {}

Class MiddlewareQueue 
{
	private 
		/**
		 * Middleware queue processing
		 * @var boolean
		 * @access Private
		 */
		$seeded = false,
		/**
		 * Application instance to include within Middleware Instances
		 * @var null
		 * @access Private
		 */
		$app = null,
		/**
		 * Middleware queue count
		 * @var integer
		 * @access Private
		 */
		$count = 0;

	/**
	 * Create a new Middleware Queue Processor
	 * @param $application Application-like object containing Request an Response parameters
	 * @throws MiddlewareException If $application is not an Object
	 * @throws MiddlewareException If $application does not contain Request and Response parameter
	 */
	final function __construct($application)
	{
		if(!is_object($application)){
			throw new MiddlewareException('Middleware Runner: $application parameter should be of an `Object`type.');
		}
		if(!isset($application->request) || !isset($application->response)){
			throw new MiddlewareException('Middleware Runner: $application `Object` should contain `$request` & `$response` parameters.');
		}
		$this->setApp($application);

		$this->queue = new \SplQueue;
	}

	/**
	 * Sets Application Instance
	 * @param mixed $app Application's Instance
	 */
	final public function setApp($app)
	{
		$this->app = $app;
	}

	/**
	 * Gets Application Instance
	 * @return mixed
	 */
	final private function getApp()
	{
		return $this->app;
	}

	/**
	 * Prevent Adding a new Middleware within another Middleware call scope
	 * Prevent having an Empty Queue as the last Middleware call
	 * Prevent Re-run of Middleware queue after first run by emptying queue
	 * 
	 * @param bool $state The state of the seed. Defaults to true.
	 * @return void
	 */
	final private function seed($state = true)
	{
		if($state === true){
			$this->count = $this->queue->count();
			// Add empty Middleware to run last
			$this->add(function(){});
		} else {
			$this->queue = new \SplQueue;
		}
		$this->seeded = $state;
	}

	/**
	 * Confirms if the Middleware queue process has Started
	 * @return boolean
	 */
	final private function isSeeded()
	{
		return !!$this->seeded;
	}

	/**
	 * Gets the number of Middleware loaded to the queue
	 * @return int
	 */
	final public function count()
	{
		if(!$this->count && !$this->isSeeded())
			return $this->queue->count();
		return $this->count;
	}

	/**
	 * Add a new Middleware
	 * 
	 * @param mixed $middleware This should be a Callable. Either a Closure or Middleware Class.
	 * @throws MiddlewareException When attempting to add a new Middleware withing another Middleware call scope
	 * @throws MiddlewareException When Middlewareis not compartible eith the system
	 * @return MiddlewareQueue
	 */
	final public function add($middleware)
	{
		if($this->isSeeded())
			throw new MiddlewareException('Can\'t add a new `Middleware` within a running `Middleware` process');

		if(
			$middleware instanceof \Closure 
			|| $middleware instanceof MiddlewareInterface 
			|| (is_object($middleware) && method_exists($middleware, 'call'))
			) {

			$previous = (!$this->queue->isEmpty()) ? $this->queue->top() : null;

			$this->queue[] = new MiddlewareObject($middleware, $this->getApp(), $previous);

			return $this;
		} else {
			throw new MiddlewareException(
				'The Callable provided is not a valid Middleware. Middleware can either be `Closure`, extend from `Middleware` or implement `MiddlewareInterface` or contain a `call` method'
				 );
		}
	}

	/**
	 * Quit Running proccess
	 * @return void
	 */
	final public function quit()
	{
		$this->seeded = false;
		// Remove last empty Middleware
		$this->queue->pop();
		$this->count-1;
	}

	/**
	 * Starts the Middleware queue processing by calling the First added Middleware [FIFO]
	 * @return Response
	 */
	final public function run()
	{
		$this->seed();

		$this->queue->dequeue()->call();

		$this->seed(false);

		return $this->getApp()->response;
	}
}

