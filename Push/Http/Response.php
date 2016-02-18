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


namespace Push\Http;

class Response 
{

	protected static 
		$request, 
		$view, 
		$header; 

	protected 
		// $view,
		$version = '1.1', 
		$status = 200, 
		$statusText = 'OK', 
		$body = '', 
		$lockSend = false, 
		$statusTexts = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			200 => 'OK',
			201 => 'Created',
	        202 => 'Accepted',
			204 => 'No Content',
			301 => 'Moved Permanently',
			302 => 'Found',
			307 => 'Temporary Redirect',
			308 => 'Permanent Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			408 => 'Request Timeout',
	        409 => 'Conflict',
			410 => 'Gone',
	        423 => 'Locked',
	        429 => 'Too Many Requests',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
	        505 => 'HTTP Version Not Supported',
	        511 => 'Network Authentication Required',
			),
		$headers = [];

	/**
	 * Construct the Response Object
	 * @param Request                        $req  Request object
	 * @param \Push\Interfaces\ViewInterface $view View Engine Object
	 */
	final public function __construct(Request $req, \Push\Interfaces\ViewInterface $view)
	{
		$headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
		static::$header = array_merge($req->headers(), $headers);
		static::$request = $req;
		static::$view = $view;

		$this->body = '';
		// $this->body = fopen('php://temp', 'r+');
	}
	/**
	 * Overload View methods to View Class
	 * @return ViewInterface  Instance of View class for methods fluency
	 */
	final public function __call($method, $args)
	{
		if(method_exists($this->view(), $method))
			return call_user_func_array([$this->view(), $method], $args);
	}
	/**
	 * Get Contents of Response Body
	 * @return string Return the Contents
	 */
	final public function __toString()
	{
		if(is_string($this->getBody()))
			return $this->getBody();
	}
	/**
	 * Get HTTP Headers
	 * @return Array
	 */
	final public function headers()
	{
		return static::$header;
	}
	/**
	 * Get HTTP request object
	 * @return Request Request Object
	 */
	final public function request()
	{
		return static::$request;
	}
	/**
	 * Get View Engine
	 * @return ViewInterface
	 */
	final public function view()
	{
		return static::$view;
	}
	/**
	 * Get empty status on Response body
	 * @return boolean 
	 */
	final public function isEmpty()
	{
		return empty($this->body);
	}
	/**
	 * Get HHTP status if its a Redirect
	 * @return boolean 
	 */
	final public function isRedirection()
	{
		return $this->getStatusCode() >= 300 && $this->getStatusCode() < 400;
	}

	/**
	 * Setters
	 */
		/**
		 * Alias to Set HTTP status of response
		 * @param  int $code Status code
		 * @return self
		 */
		final public function status($code)
		{
			return $this->setStatus($code);
		}
		/**
		 * Set HTTP response type
		 * @param  string $type Response type
		 * @return self
		 */
		final public function type($type)
		{
			return $this->setContentType($type);
		}
		/**
		 * Set HTTP status of response
		 * @param int $code Status code
		 * @throws \Exception If the status code entered does not exist within the Response object
		 * @return self
		 */
		final public function setStatus($code)
		{
			if(!array_key_exists($code, $this->statusTexts)){
				throw new \Exception("Response Status code of `$code` does not exist on this server. See `response.class.php` for available status codes or add more. :)");
			}
			$this->status = $code;
			$this->statusText = $this->statusTexts[$code];
			return $this;
		}
		/**
		 * Set HTTP version
		 * @param mixed $version HTTP version
		 * @return self
		 */
		final public function setVersion($version)
		{
			$this->version = $version;
			return $this;
		}
		/**
		 * Set content type of Response
		 * @param string $type Content type
		 * @return self
		 */
		final public function setContentType($type)
		{
			return $this->setHeader('Content-type', $type);
		}
		/**
		 * Set HTTP header
		 * @param string $type  Type of Header type
		 * @param string $value Value of Header type
		 * @return self
		 */
		final public function setHeader($type, $value)
		{
			$this->headers[] = $type.': '.$value;
			return $this;
		}

	/**
	 * Getters
	 */
		/**
		 * Get content of the Response body
		 * @return mixed Response Body
		 */
		final public function getBody()
		{
			return $this->body;
		}
		/**
		 * Get HTTP Response headers
		 * @return mixed Response Headers
		 */
		final function getHeaders()
		{
			return $this->headers;
		}
		/**
		 * Get HTTP Response protocol
		 * @return mixed Response protocol
		 */
		final function getProtocol()
		{
			return $this->protocol;
		}
		/**
		 * Get HTTP Response version
		 * @return mixed Response version
		 */
		final function getVersion()
		{
			return $this->version;
		}
		/**
		 * Get HTTP Response Status code
		 * @return int Response status code
		 */		
		final function getStatusCode()
		{
			return (int)$this->status;
		}
		/**
		 * Get HTTP Response Status text translation of the status code input
		 * @return string Response status text
		 */		
		final function getStatusText()
		{
			return $this->statusText;
		}

	/**
	 * Write to Response body
	 * @param  mixed  $content Content to write to body
	 * @param  boolean $replace Choice to overwrite previous content
	 * @return self
	 */
	final public function write($content, $replace = false)
	{
		if($this->lockSend === true)
			return $this;

		if(is_string($content) && $replace === false){
			$this->body .= $content;
		}
		else {
			$this->body = $content;
		}
		return $this;
	}
	/**
	 * Write content to the Response body that overwrite previous content and also with option to allow further manipulation or not
	 * @param  mixed  $content  Content to write to body
	 * @param  boolean $lockSend Set to `true` to prevent further manipulation or `false` to allow
	 * @return self
	 */
	final public function send($content, $lockSend = false)
	{
		$this->write($content, true);
		$this->lockSend = $lockSend;
		return $this;
	}
	/**
	 * Write content to Response body that overrides previous content and also cannot be further manipulated
	 * @param  mixed  $content  Content to write to body
	 * @return [type]          [description]
	 */
	final public function end($content)
	{
		return $this->send($content, true);
	}
	/**
	 * Write JSON content to Response body. This overwrites previous contents and cannot be written to afterwards
	 * @param  mixed $content Array or Object that would be converted to JSON string
	 * @return self
	 */
	final public function json($content)
	{
		$this->setContentType('application/json');
		$this->request()->set('isJson', true);
		return $this->end(json_encode($content));
	}
	/**
	 * Render a Template, Depends on the View engine being Used
	 * @return mixed ViewInterface::render()
	 */
	final public function render()
	{
		$e = call_user_func_array([$this->view(), 'render'], func_get_args());
		if(!is_string($e)){}
		$this->write($e);
		return $e;
	}
	/**
	 * Redirect to a different URL
	 * @param  string  $uri      URL to redirect to
	 * @param  boolean $relative Whether to treat as in-app redirection
	 * @param  integer $status   status code to send
	 * @return self
	 */
	final public function redirect($uri = '', $relative = true, $status = 302)
	{
		if(is_int($relative)){
			$status = $relative;
			$relative = true;
		}

		$uri = (empty($uri)) ? $this->request()->url() : ($relative === true ? ROOT_URI.'/'.ltrim($uri, '/') : $uri);

		return $this->status($status)->setHeader('Location', htmlspecialchars_decode($uri));
	}
}
