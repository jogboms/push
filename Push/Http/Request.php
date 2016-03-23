<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */


namespace Push\Http;

Class RequestParams extends \Push\Utils\Collection {}

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
    $_TYPE_,
    $_VERSION_,
    $_METHOD_,
    $_URI_ = '',
    $_HEADERS_,
    $_PARAMS_ = [],
    $_GET = [],
    $_POST = [],
    $_INPUT = [],
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

  /**
   * Create a Request Object
   * @param array $_PARAMS_ Defaults parameters to append to Request
   */
  public function __construct($_PARAMS_ = [])
  {
    $_COOKIE = $this->stripslashes($_COOKIE);
    $this->_PARAMS_ = $this->stripslashes((array)$_PARAMS_);
    parse_str(file_get_contents('php://input'), $_INPUT);

    $this->_GET = $this->stripslashes($_GET);
    $this->_POST = $this->stripslashes($_POST);
    $this->_INPUT = $this->stripslashes($_INPUT);
    $this->_PARAMS_ = array_merge($this->_GET, $this->_POST, $this->_INPUT, $this->_PARAMS_);

    $this->_URI_ = new Uri();
    $this->_METHOD_ = $this->getMethod();

    $this->_HEADERS_ = function_exists('apache_request_headers') ? apache_request_headers() : [];

    if($this->server('SERVER_PROTOCOL')){
      list($type, $ver) = explode('/', $this->getProtocol());
      $this->_TYPE_ = $type;
      $this->_VERSION_ = $ver;
    }
  }

  /**
   * Add new parameters to the Request
   * @param string|array $name  Add an array containing parameters all at once or a parameter name with its $value
   * @param mixed $value value of new parameter of $name
   * @return self
   */
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
      $this->_GET[$name] = $this->_PARAMS_[$name] = $this->stripslashes($value);
    }
    return $this;
  }
  /*
  * @see function.php for cleanHTML()
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

  public function getDomain()
  {
    return $this->getUri()->getScheme().'://'.$this->getHost();
  }
  public function getBasePath()
  {
    return $this->getUri()->getBasePath();
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
  public function accepts()
  {
    return $this->server('HTTP_ACCEPT');
  }

  public function has($name, $method = 'GET')
  {
    switch ($method) {
      case 'POST':
        return !is_null($this->post($name));
      case 'GET':
        return !is_null($this->get($name));
      case 'PUT':
        return !is_null($this->put($name));
      case 'PATCH':
        return !is_null($this->patch($name));
      case 'DELETE':
        return !is_null($this->delete($name));
      default:
        return !is_null($this->params($name));
    }
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
  public function isXhr()
  {
    return (bool)$this->params('isajax') == true || $this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest';
  }
  public function isAjax($method = null)
  {
    return is_null($method) ? $this->isXhr() : ($this->isMethod($method) && $this->isXhr());
  }
  public function isJson()
  {
    return $this->server('CONTENT_TYPE') == 'application/json';
  }
  public function wantsJson()
  {
    return !!(stripos($this->accepts(), '/json') || $this->has('isJson'));
  }


  /**
   * Request Parameter Getters
   */
    /**
     * Get Request parameters. Acts as generic method for other Request parameter methods
     *
     * @param  string|boolean|null $name    Name of Request parameter
     * @param  mixed $default Default parameter to get if it does not exist
     * @param  Array $params Parameters to request from. Defaults to Request Parameters
     * @example
     *   $req->params() Returns all parameters as a RequestParams Object
     *
     *   $req->params(true) Returns all parameters as a an Array
     *
     *   $req->params('name', $default) Returns value of `name` parameter
     *   or value of `$default` variable with defaults to `null` if left empty
     *
     *   $req->params('name', $default, $params = [...]) Returns value of `name` parameter gotten from the particular `$params` array passed into the method
     *   or value of `$default` variable with defaults to `null` if left empty
     *
     * @return mixed|null|Array|RequestParams
     * @see function.php for clean_output()
     */
    public function params($name=null, $default = null, Array $params = null)
    {
      if(is_null($params))
        $params = $this->_PARAMS_;

      if($name === true)
        return $params;

      if(is_null($name) || is_array($name))
        return new RequestParams($params);

      return isset($params[$name]) ? clean_output($params[$name]) : $default;
    }
    /**
     * Get one or all GET parameters as Array or RequestParams
     */
    public function get($name = null, $default = null)
    {
      return $this->params($name, $default, $this->_GET);
    }
    /**
     * Get one or all POST parameters as Array or RequestParams
     */
    public function post($name = null, $default = null)
    {
      return $this->params($name, $default, $this->_POST);
    }
    /**
     * Get one or all PUT parameters as Array or RequestParams
     */
    public function put($name = null, $default = null)
    {
      return $this->params($name, $default, $this->_INPUT);
    }
    /**
     * Get one or all JSON payload parameters as Array or RequestParams
     */
    public function json($name = null, $default = null)
    {
      return $this->params($name, $default, $this->_INPUT);
    }
    /**
     * Get one or all PATCH parameters as Array or RequestParams
     */
    public function patch($name = null, $default = null)
    {
      return $this->params($name, $default, $this->_INPUT);
    }
    /**
     * Get one or all DELETE parameters as Array or RequestParams
     */
    public function delete($name = null, $default = null)
    {
      return $this->params($name, $default, $this->_INPUT);
    }
    /**
     * Get one or all FILES parameters as Array or RequestParams
     */
    public function files($name = null, $default = null)
    {
      return $this->params($name, $default, $_FILES);
    }
    /**
     * Get one or all SERVER parameters as Array or RequestParams
     */
    public function server($name = null, $default = null)
    {
      return $this->params($name, $default, $_SERVER);
    }
}
