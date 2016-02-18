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

class Request implements \ArrayAccess 
{
	
	/**
	 * Implementation && Magic functions
	 */
		public function __get($name)
		{
			return $this->params($name);
		}
		public function __set($name, $value)
		{
			$this->set($name, $value);
		}
		public function __isset($name)
		{
			return isset($this->_PARAMS_[$name]);
		}
		public function __unset($name)
		{
			unset($this->_PARAMS_[$name]);
		}
		public function offsetExists($name)
		{
			return isset($this->$name);
		}
		public function offsetGet($name)
		{
			return $this->$name;
		}
		public function offsetSet($name, $value)
		{
			$this->$name = $value;
		}
		public function offsetUnset($name)
		{
			unset($this->$name);
		}

	protected 
		$_PROTOCOL_, 
		$_PROTOCOL_TYPE_, 
		$_PROTOCOL_VERSION_, 
		$_METHOD_, 
		$_PARAMS_ = [], 
		$_URI_ = '', 
		$_TEMP_ = [], 
		$_HEADERS_, 
		$_METHODS_ = [
			'CONNECT' => true,
			'DELETE' => true,
			'GET' => true,
			'HEAD' => true,
			'OPTIONS' => true,
			'PATCH' => true,
			'POST' => true,
			'PUT' => true,
			'TRACE' => true,
		];

	public function __construct($_PARAMS_ = [])
	{
		$_GET = $this->stripslashes($_GET);
		$_POST = $this->stripslashes($_POST);
		$_COOKIE = $this->stripslashes($_COOKIE);

		parse_str(file_get_contents('php://input'), $_INPUT);
		$_INPUT = $this->stripslashes($_INPUT);

		/*
		@todo Add fragments to URI
		 */
		if($this->server('SERVER_PROTOCOL')){
			list($type, $ver) = explode('/', $this->getProtocol());
			$this->_PROTOCOL_TYPE_ = $type;
			$this->_PROTOCOL_VERSION_ = $ver;
		}

		$this->_URI_ = new \Push\Http\Uri();
		$this->_PARAMS_ = array_merge($this->_PARAMS_, $_GET, $_POST, $_INPUT, (array)$_PARAMS_);
		$this->_METHOD_ = $this->getMethod();
		$this->_HEADERS_ = function_exists('apache_request_headers') ? apache_request_headers() : [];
	}

	public function set($name, $value = null)
	{
		// TODO: Validation here
		if(is_array($name)) {
			// $this->_PARAMS_ = array_merge($this->_PARAMS_, $this->stripslashes($name));
			foreach ($name as $key => $value){
				if(!empty($key)) {
					$this->set($key, $value);
				}
			}
		}
		else {
			$this->_PARAMS_[$name] = $this->stripslashes($value);
		}
		return $this;
	}
	/*
	* @see function.inc.php | cleanHTML()
	*/
	public function stripslashes($value)
	{
		$type = gettype($value);

		if(is_array($value)) return array_map([$this, 'stripslashes'], $value);

		if(is_string($value)) return stripslashes(cleanHTML($value));

		$value = stripslashes(cleanHTML($value));
		settype($value, $type);

		return $value;
	}

	public function getHost()
	{
		return $this->server('HTTP_HOST');
	}
	public function getProtocol()
	{
		return $this->server('SERVER_PROTOCOL');
	}
	public function getProtocolVersion()
	{
		return $this->server('HTTP_HOST');
	}
	public function getMethod()
	{
		return $this->params('_method_', $this->server('REQUEST_METHOD'));
	}
	public function getUri()
	{
	    return $this->_URI_;
	}
	public function getIp()
	{
	    return $this->server('REMOTE_ADDR');
	}
	public function getAllowedMethods()
	{
		return $this->_METHODS_;
	}
	public function uri()
	{
		return $this->server('REQUEST_URI');
	}
	public function url()
	{
		return (string)$this->_URI_;
	}
	public function isMethod($method)
	{
		return $this->_METHOD_ == $method;
	}
	public function headers()
	{
		return $this->_HEADERS_;
	}
    public function isGet()
    {
    	return $this->isMethod('GET');
    }
    public function isPost()
    {
    	return $this->isMethod('POST');
    }
    public function isPut()
    {
    	return $this->isMethod('PUT');
    }
    public function isPatch()
    {
    	return $this->isMethod('PATCH');
    }
    public function isDelete()
    {
    	return $this->isMethod('DELETE');
    }

    public function isAjax($method = null)
    {
    	return is_null($method) ? $this->isXhr(): (
    			$this->isMethod($method) && $this->isXhr()
    		);
    }
    public function isJson()
    {
    	return $this->server('CONTENT_TYPE') == 'application/json';
    }
    public function isXhr()
    {
        return (bool)$this->params('isajax') == true || $this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest';
    }


	/*
	* @see function.inc.php |  clean_output()
	*/
	public function params($name=null, $default = null)
	{
		return ($name === true) ? $this->_PARAMS_ : ((is_null($name) || is_array($name)) ? new RequestParams($name, $this->_PARAMS_) : (
				isset($this->_PARAMS_[$name]) ? clean_output($this->_PARAMS_[$name]) : $default
			));
	}
	public function get($name = null, $default = null)
	{
		return is_null($name) || $name === true ? $this->params($name) : (
				isset($this->$name) ? $this->$name : $default
			);
	}
	public function post($name = null, $default = null)
	{
		return is_null($name) || $name === true ? $this->params($name) : (
				isset($this->$name) ? $this->$name : $default
			);
	}
	public function put($name = null, $default = null)
	{
		return is_null($name) || $name === true ? $this->params($name) : (
				isset($this->$name) ? $this->$name : $default
			);
	}
	public function json($name = null, $default = null)
	{
		return is_null($name) || $name === true ? $this->params($name) : (
				isset($this->$name) ? $this->$name : $default
			);
	}
	public function delete($name = null, $default = null)
	{
		return is_null($name) || $name === true ? $this->params($name) : (
				isset($this->$name) ? $this->$name : $default
			);
	}
	public function files($name = null)
	{
		// return isset($_FILES[$name]) ? $_FILES[$name] : $this->params($_FILES);
		return ($name === true) ? $_FILES : (isset($_FILES[$name]) ? $_FILES[$name] : $this->params($_FILES));
	}
	public function server($name = null, $default = null)
	{
		return ($name === true) ? $_SERVER : (is_null($name) ? $this->params($_SERVER) : (
			isset($_SERVER[$name]) ? $_SERVER[$name] : (
				isset($_SERVER[strtoupper($name)]) ? $_SERVER[strtoupper($name)] : $default
				)
			));
	}

	public function accepts()
	{
		return $this->server('HTTP_ACCEPT');
	}

	public function has($name, $method = 'POST')
	{
		switch ($method) {
			case 'POST': return !is_null($this->post($name));
			case 'PUT': return !is_null($this->put($name));
			case 'DELETE': return !is_null($this->delete($name));
			default: return !is_null($this->get($name));
		}
	}
	public function wantsJson()
	{
		return (stripos($this->accepts(), '/json') || $this->has('isJson'))? true : false;
	}

}
