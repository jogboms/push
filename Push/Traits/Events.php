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
namespace Push\Traits;

/**
 * Event Bus Implemetation 
 *
 * within the listener, $this contains the name of the emitted event
 * `$this->event`
 * 
 * @example 
 * Events::on('alert', function($message [, ...]){
 * 		echo($message);
 * });
 * Events::emit('alert', 'I love attending Events');
 * [output] I love attending Events
 *
 * // Multiple Events on a single callback
 * Events::on('alert | keep', function($message [, ...]){
 * 		echo($message);
 * });
 * Events::emit('keep', 'I love attending Events ');
 * Events::emit('alert', 'I love attending Events ');
 * [output] I love attending Events I love attending Events 
 * 
 * // Regex style
 * Events::on('alert.*', function($message [, ...]){
 * 		echo($message);
 * });
 * Events::emit('alert.regex.one', 'I love attending Events ');
 * Events::emit('alert.regex.two', 'I love attending Events ');
 * [output] I love attending Events I love attending Events
 */

Class EventsException extends \Exception {}

trait Events 
{
	protected 
		/**
		* @var array Contains all declared events
		*/
		$__events = [],
		/**
		* @var array Contains all emitted events and its location
		*/
		$__events_log = [],
		/**
		* @var Delimiter used to separate multiple event_names
		*/
		$__separator = '|';

	/**
	 * Listen to an event or multiples of events using | as separator
	 * @param  string $event_name Name of event to listen to
	 * @param  callable $callable   Callable to invoke when the event is emitted
	 * @return self
	 */
	public function on($event_name, $callable, $once = false)
	{
		$event[] = $callable;

		if($once === true)
			$event['once'] = true;
		/* Incase of multiple events seperated by | */
		$events = explode($this->__separator, $event_name);
		foreach ($events as $event_name) {
			$this->__events[trim(str_replace('*', '.*', $event_name))][] = $event;
		}
		return $this;
	}
	
	/**
	 * Listen once to an event or multiples of events using | as separator
	 * @param  string $event_name Name of event to listen to
	 * @param  callable $callable   Callable to invoke when the event is emitted
	 * @return self
	 */
	public function once($event_name, $callable)
	{
		return $this->on($event_name, $callable, true);
	}

	/**
	 * Switch off all listeners on a particular event or all events
	 * @param  string|null $event_name Setting $event_name to null or leaving it empty removes all event listeners.
	 *                                 Other wiseuse the event's name
	 * @return self
	 */
	public function off($event_name = null)
	{
		if($event_name === null){
			$this->__events = [];
		} else {
			unset($this->__events[$event_name]);
		}
		return $this;
	}
	/**
	 * Emit Events
	 * @param  string $event_name Event name
	 * @example ->emit($event_name [, ...])
	 * @return self          
	 * @see function.inc.php for trim_root();
	 */
	public function emit($event_name)
	{
		$args = func_get_args();
		$event_name = array_shift($args);
		
		if($this->debug){
			list($e) = debug_backtrace();
			$this->__events_log[] = [$event_name, basename($e['file']).' ['.$e['line'].']'];
		}

		/* When it is a Regex-called event */
		foreach($this->__events as $key => &$events){
			if(preg_match('~^'.$key.'$~', $event_name)){
				if(empty($events)) return;


				foreach($events as $event){
					if(!is_callable($event[0]))
						throw new EventsException('Event listener `'.$event_name.'` requires a valid event function');

					$class = new \stdClass();
					$class->event = $event_name;

					call_user_func_array($event[0]->bindTo($class), $args);
				}

				/* stops this Event from running Multiple times */
				if(isset($events[0]['once'])){
					unset($events[0]);
				}
			}
		}
		return $this;
	}

	/**
	 * Get names of all listeners
	 * @return array
	 */
	public function events()
	{
		return array_keys($this->__events);
	}

	/**
	 * Get all emitted events and their point of emit
	 * @return array
	 */
	public function events_log()
	{
		return $this->__events_log;
	}
}
