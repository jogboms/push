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

/**
* RouteObject Class
*
* @author Jeremiah Ogbomo | @jogboms
* @package See composer.json
* @version 1.0.2
* @since 1.0.0
*/


/**
 * Returns cast value of input variable
 * @param  mixed $var input
 */
if(!function_exists('format_variable')){
  function format_variable($var){
    if(preg_match('~^[0-9]{0,}\.{1}[0-9]{0,}$~', $var)) settype($var, 'float');
    elseif(preg_match('~^[-]?[0-9]+$~', $var)) settype($var, 'int');
    return $var;
  }
}

Class RouteObject
{

  /**
  * @param object $__init Contains Class Instance
  * @param string $_pattern splits route into its components
  * @param array Contains all Regex pattern modifiers
  * @param array $_routes Contains all Routes
  */
  static protected $__init,
      $_pattern = '~(\(?[a-zA-Z0-9-\*|()?_\~]+\)?)?(?:{([a-z|]+)})?(?:(?:[:][$]([a-zA-Z0-9-_]+)))?~',
      $_patterns = [
        '*'     => '[a-zA-Z0-9-_+%\s\']+', // any characters till the next slash
        '~'     => '[a-zA-Z0-9\.-_+%\s\']+', // all characters till the end of url
        'p' => '[a-zA-Z0-9-_+\s\']+', // Slug-like page links till the next slash
        'alpha' => '[a-z+\s\']+', // lower-case alphabets till the next slash
        'ALPHA' => '[A-Z+\s\']+', // Upper-case alphabets till the next slash
        'num'   => '[0-9\s]*\.*[0-9]+', // integers|digits|numbers till the next slash
        'Alpha' => '[a-zA-Z+\s\']+', // lower|upper case alphabets till the next slash
        'alnum' => '[a-z0-9+\.\s\']+', // lower-case alphabets or numbers till the next slash
        'ALNUM' => '[A-Z0-9+\.\s\']+', // upper-case alphabets or numbers till the next slash
        'Alnum' => '[a-zA-Z0-9+\.\s\']+', // lower|upper case alphabets or numbers till the next slash
        'hash' => '?:#([a-zA-Z0-9-+_%\s]+)' // Matches for hashtags e.g #hashtag till the next slash
        ],
      $_delim = '>',
      $_routes = [];
  /**
  * @param string $_uri Queried URI path
  * @param array $_query Queried URI queries
  * @param array $_match Matched Route
  */
  public $url = [], $match = [];

  function __construct($uri, $method = 'GET')
  {
    if(!is_string($uri))
      throw new Exception('Route::url($uri): Requires a <u>string</u>ed `$uri` e.g http://google.com/search?q=pushMVC');

    $this->url['method'] = $method;
    $this->url['base'] = $uri;
    $uri = parse_url(ltrim($this->url['base'], '/'));
    $this->url['path'] = isset($uri['path']) ? trim($uri['path'], '/') : '';
    $this->url['uri'] = str_replace(dirname(ltrim(strtolower($_SERVER['SCRIPT_NAME']), '/')), '', strtolower($this->url['path']));
    // $this->url['uri'] = $this->url['path'].$this->url['hash'];
    $this->url['query'] = [];

    if(isset($uri['query'])) {
      parse_str($uri['query'], $this->url['query']);
      // alert(format_variable('say'));
      $this->url['query'] = array_map(__NAMESPACE__.'\format_variable', $this->url['query']);
    }
    if(isset($uri['fragment'])) {
      $this->url['hash'] = '#'.$uri['fragment'];
      if(isset($this->url['query'])) $this->url['query'] += (array)$this->url['hash'];
   }
  }

  public function route($route, $controller)
  {
    if(is_string($c = $controller)) {
      $controller = trim(rtrim($c, '@'));
    }

    if(is_string($controller) and !empty($controller)) {
      $e = explode('@', $controller);
      $controller = [$e[0], (isset($e[1]) ? $e[1] : null)];
    }

    return new Route($route, $controller);
  }

  /**
  * Creates a new Pattern modifier
  *
  * @param string $modifier Name of pattern
  * @param string $regex Regex replacement
  */
  public function addPattern($modifier, $regex)
  {
    $l = strlen($regex);
    if(substr($regex, 0, 1) == '(' && substr($regex, $l-1) == ')')
      $regex = substr($regex, 1, $l-2);
    static::$_patterns = array_merge(static::$_patterns, [$modifier=>$regex]);
  }
  /**
   * Get defined patterns
   * @return array
   */
  public function patterns()
  {
    return static::$_patterns;
  }
  /**
  * Creates new Route using a valid Regex
  *
  * @param string $route  Regex pattern e.g (?:(books)/?(title).(?:php|html))
  * @param array $match   Array of Keys to match with Regex
  *
  * todo: $matches - Array of keys and defaults
  */
  public function addRegex($regex, $match = [])
  {
    static::$_routes[$regex] = array(
          'route'=>$regex, 'regex'=>$regex, 'match'=>$match
        );
  }
  /**
  * Creates a `Regex` format of route and `Matches`
  *
  * @param string $route to Regex-ify
  * @param bool $hasRegex if route is already a regex
  * @param array $match if route is already has default values
  *
  * @return array [Route, Regex, Matches]
  */
  private function build($route, $hasRegex = false, $match = [])
  {
    $regex = '';
    $end = '';
    if(true === $hasRegex) return [$route, $route, $match];

    $e = explode('/', trim($route,'/'));
    if(!empty($e[0])){
      $e[0] = str_replace(['[',']'], ['(?|',')'], $e[0]);
      foreach($e as $r){
        preg_match_all(static::$_pattern, $r, $matches, PREG_SET_ORDER);
        if(!empty($matches[0][0])){
          // Start Make Regex
          $matches[0][1] = rtrim(ltrim($matches[0][1], '('), ')');
          $regex .= '(?:';

          if(isset($matches[0][1], static::$_patterns[$matches[0][1]])){
            $regex .= '/?('.static::$_patterns[$matches[0][1]].')';
          }
          elseif(empty($matches[0][1]))
            $regex .= '/?('.static::$_patterns['*'].')';
          else $regex .= '('.$matches[0][1].')';

          // Add Extension Value
          if(!empty($matches[0][2]))
            $regex .= '.('.$matches[0][2].')';
            // $regex .= '.(?:'.$matches[0][2].')';
          $regex .= '/?';
          $end .= ')';
          // $end .= ')?'; // Makes each parameter optional
          // End Make Regex

          // Build Matches
          if(isset($matches[0][3])) $match[] = $matches[0][3];
          else $match[] = $matches[0][1];

          // Add Extension Label
          if(!empty($matches[0][2]))
            $match[] = 'format';
          // End Build
        }
      }
      $regex .= rtrim($end, '?');
    }
    else {
        $regex = '';
        // $regex = '/';
        $match[] = '';
    }
    return [$route, $regex, $match];
  }
  /**
  * Checks if Route matches the current URI
  *
  * @param Route $route Route to check
  * @return Route|boolean Route if match is found or false if otherwise
  */
  public function match(Route $route)
  {
    if(!in_array($this->url['method'], $route['methods']))
      return false;

    list(, $regex, $match) = $this->build($route['route']);

    if(!preg_match('~^'.$regex.'$~', ltrim($this->url['uri'], '/'), $m))
      return false;

    $this->match = [];
    if(empty($regex)){
      /* type-casting & Combine Matches with Values */
      for ($i=0; $i < count($m); $i++) {
        // $this->match[$match[$i]] = format_variable($match[$i]);
        $this->match[$match[$i]] = format_variable($m[$i]);
      }
    }
    elseif(count($m)>1){
      array_shift($m);
      /* type-casting & Combine Matches with Values */
      for ($i=0; $i < count($m); $i++) {
        $this->match[$match[$i]] = format_variable($m[$i]);
      }
    }
    $route['match'] = $this->match;

    // Routes with Controllers and/or actions
    if(is_array($route['controller'])){
      list($c, $m) = $route['controller'];
      $route->setController($c);
      $route->setAction($m);
    }
    // No Callback for Route
    elseif(!$route['controller']) {
      $route->setController();
      $route->setAction();
    }
    // Get Remaining Matched Parameters
    $route->setParams($this->url['query']);

    return $route;
  }

}
