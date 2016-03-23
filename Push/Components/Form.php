<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */
namespace Push\Components;

defined('FORM_SUCCESS') or define('FORM_SUCCESS', 0);
defined('FORM_ERROR') or define('FORM_ERROR', 1);
defined('FORM_INFO') or define('FORM_INFO', 2);
defined('FORM_WARNING') or define('FORM_WARNING', 3);

Class Form
{

  public static $messages = [];

  public static function message($name, $message, $type = FORM_ERROR)
  {
    if(is_array($message)){
      foreach ($message as $message) {
        self::addMessage($name, $message, $type);
      }
    }
    else {
      self::addMessage($name, $message, $type);
    }
  }

  protected static function addMessage(&$name, $message, $type)
  {
    self::$messages[$name][] = array(
        'message' => $message,
        'type' => self::getType($type)
        );
  }

  public static function error($name, $message)
  {
    self::message($name, $message, FORM_ERROR);}
  public static function success($name, $message)
  {
    self::message($name, $message, FORM_SUCCESS);}
  public static function info($name, $message)
  {
    self::message($name, $message, FORM_INFO);}
  public static function warning($name, $message)
  {
    self::message($name, $message, FORM_WARNING);}

  public static function is_ok($name)
  {
    if(self::hasMessage($name)){
      foreach(self::$messages[$name] as $message){
        if($message['type'] == static::getType(FORM_ERROR)
            || $message['type'] == static::getType(FORM_WARNING))
          return false;
      }
    }
    return true;
  }

  public static function hasMessage($name)
  {
    return isset(self::$messages[$name]);
  }

  public static function getType($type)
  {
    switch ($type) {
      case FORM_ERROR: return 'danger';
      case FORM_WARNING: return 'warning';
      case FORM_INFO: return 'info';
      case FORM_SUCCESS: return 'success';
    }
  }

  public function errors($name)
  {
    return self::$messages[$name];
  }
  public static function render($name = null, $as_text = false)
  {
    if(isset(self::$messages[$name])){
      if(count(self::$messages[$name]) > 1) {
        $c = '';
        foreach(self::$messages[$name] as $message)
          $c .= '<li>'.$message['message'].'</li>';
        echo self::formatMessage('<ul style="margin-top:-1.75em">'.$c.'</ul>', $message['type'], $as_text);
      }
      else {
        echo self::formatMessage(self::$messages[$name][0]['message'], self::$messages[$name][0]['type'], $as_text);
      }
    }
  }

  public static function formatMessage($message, $type, $as_text)
  {
    if($as_text)
      $style = 'text-'.$type.' text-center" style="padding:1rem;margin:1rem .5rem;';
    else $style = 'alert alert-'.$type.' center alert-dismissable';
    $html = '<div class="'.$style.'">&nbsp;';
    $html .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    return $html.$message.'</div>';
  }
}
