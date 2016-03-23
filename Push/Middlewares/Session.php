<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */

namespace Push\Middlewares;

Class Session extends \Push\Utils\Set
{

  public $app;
  public $next;

  protected $expire = 3600; // 60 minutes
  protected $_vars;
  protected $Controller_Uri_Ignore = ['auth', 'api'];
  protected $config = ['debug' => true ];

  public function call()
  {
      $this->app->session = $this;

      /* Creates last visited URI */
      if($this->has('last-url')){
        $this->app->env('URI_BACK', $this->get('last-url'));
      }

      $this->next->call();

      if(!in_array($this->app->env('__CONTROLLER__'), $this->Controller_Uri_Ignore)){
        $this->set('last-url', $this->app->env('URI'));
      }

      if($this->app->env('APP_USE_DEBUGGER') == true){
        echo '<pre><h3>Session Middleware</h3>';
        // print(json_encode($this->params()));
        print_r($this->params());
        echo '</pre>';
      }
  }

  /**
   *
   * @param array $config Configuration parameters
   * 'debug' => true | false  Defaults to false
   */
  public function __construct($config = [])
  {
    session_name('PUSH_MVC');
    @session_start();

    $this->id = session_id();

    $this->config = array_merge($this->config, $config);
    // if(!isset($this->id))
      // alert('n');
    // alert($this->id);
    // $this->_gbc();
  }

  #--[ Methods ]---------------#
  public function id()
  {
    return $this->id;
  }

  public function set($name, $value)
  {
    $_SESSION[$name] = $value;
    return $this;
  }
  public function get($name, $default = null)
  {
    if(isset($_SESSION[$name]))
      return $_SESSION[$name];
    if($default !== '')
      return $default;
    throw new Exception('Session Error: $_SESSION['.$name.'] does not Exist');
  }


  public function delete($name)
  {
    if(is_array($name)){
      foreach ($name as $n)
        unset($_SESSION[$n]);
    } else {
      unset($_SESSION[$name]);
    }
    return $this;
  }
  public function flush()
  {
    $_SESSION = [];
    $this->restart();
  }
  public function _gbc()
  {
    $time = time();
    $this->flush();
    self::$db->delete($this->table,"WHERE ($time-expire)>$this->expire");
  }
  public function restart()
  {
    session_regenerate_id();
    $this->id = session_id();
  }

  /*=-- Helpers --=*/
  public function remove($name)
  {
    return $this->delete($name);
  }
  public function has($name)
  {
    return isset($_SESSION[$name]);
  }
  public function params()
  {
    return $_SESSION;
  }
}

