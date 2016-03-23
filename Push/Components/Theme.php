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

Class Theme
{
  public static $_css = [], $_js = [];

  /* dynamic stylesheets */
  static function setCss($file, $path = null, $remote = false){
    if(func_get_arg(1) === true){
      $path = dirname($file).'/';
      $file = basename($file);
      $remote = true;
    }
    else {
      $file = trim($file, '/');
      if($path === null) $path = ASSET_URI.'/css/';
      else $path = trim($path, '/').'/';
    }
    static::$_css[] = ['file'=>$file, 'path'=>$path, 'remote'=>$remote];
  }
  static function getCss(){
    $link = '<!--// DONE WITH AN ON-THE-FLY CSS INCLUSION PHP SNIPET BY @jogboms //-->';
    foreach(static::$_css as $css){
        if($css['remote'] === false && is_readable(ASSET_PATH.DS.'css'.DS.$css['file']))
          $link .= '<link href="'.$css['path'].$css['file'].'" rel="stylesheet" />';
        elseif($css['remote'] === true)
          $link .= '<link src="'.$css['path'].$css['file'].'" rel="stylesheet" />';
        else $link .= '<style>'.$css['file'].'</style>';
    }
    $link .= '<!--// END INCLUSION //-->';
    return $link;
  }

  /* dynamic javascript */
  static function setJs($file, $position = 'bottom', $path = null, $remote = false){
    if($position === true || $path === true){
      $path = dirname($file).'/';
      $file = basename($file);
      $position = func_get_arg(1) === true ? 'bottom' : func_get_arg(1);
      $remote = true;
    }
    else {
      $file = trim($file, '/');
      if($path === null) $path = ASSET_URI.'/js/';
      else $path = trim($path, '/').'/';
    }
    static::$_js[] = ['file'=>$file, 'pos'=>$position, 'path'=>$path, 'remote'=>$remote];
  }
  static function getJs($position = 'bottom'){
    $script = '<!--// DONE WITH AN ON-THE-FLY JS INCLUSION PHP SNIPET BY @jogboms //-->';
    foreach(static::$_js as $js){
      if($js['pos'] !== $position) continue;
      if($js['remote'] === false && is_readable(ASSET_PATH.DS.'js'.DS.$js['file']))
        $script .= '<script src="'.$js['path'].$js['file'].'"></script>';
      elseif($js['remote'] === true)
        $script .= '<script src="'.$js['path'].$js['file'].'"></script>';
      else $script .= '<script>'.$js['file'].'</script>';
    }
    return $script.'<!--// END INCLUSION //-->';
  }
  static function runJS($file, $path = null, $remote = false){
    return static::setJs($file, 'top', $path, $remote);
  }
  static function dump(){
    return array(
        'CSS' => static::$_css,
        'JS' => static::$_js
        );
  }
}
