<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */

namespace Push\Utils;

Class Bread
{

  public static
    // Contains all breadcrumbs
    $crumbs = [],
    // HTML to Prepend to Bread Crumbs
    $prefix = '<ul class="breadcrumb">',
    // HTML to Append to Bread Crumbs
    $suffix = '</ul>',
    // HTML links separator
    $separator = '&nbsp;';

  /**
  * Adds a new breadcrumb to existing
  *
  * @param string $title : Link Title
  * @param string $url : Link URL
  * @param string $priority : List position URL
  */
  public static function set($title=null, $url=null, $priority = null)
  {
    /**
    * @todo
    *
    * add tag called `priority`
    * sort by `priority` at `get()`
    */
    if(!is_null($priority)){
      $priority--;
      if(isset(self::$crumbs[$priority])){
        $a = array_splice(self::$crumbs, $priority);
      }

      self::$crumbs[$priority] = ['t' => $title, 'l' => $url];

      if(isset($a)) self::$crumbs = array_merge(self::$crumbs, $a);
    } else {
      self::$crumbs[] = ['t' => $title, 'l' => $url];
    }
  }
  /**
  * Returns saved breadcrumbs
  *
  * @return array
  */
  public static function crumbs()
  {
    return self::$crumbs;
  }
  /**
   * Alter the Breadcrumbs Prefix
   * @param  string:html $prefix
   * @return void
   */
  public static function prefix($prefix)
  {
    static::$prefix = $prefix;
  }
  /**
   * Alter the Breadcrumbs Suffix
   * @param  string:html $suffix
   * @return void
   */
  public static function suffix($suffix)
  {
    static::$suffix = $suffix;
  }
  /**
  * Returns total number of breadcrumbs
  *
  * @return int
  */
  public static function count()
  {
    return count(self::$crumbs);
  }
  /**
  * if crumb is last item in breadcrumbs
  *
  * @param int $id crumb's key
  * @return bool
  */
  public static function isLast($id)
  {
    return $id === (self::count()-1);
  }
  /**
  * Returns saved breadcrumbs links
  *
  * @param mixed $separator HTML link's separators
  * @return mixed
  */
  public static function get($separator = null)
  {
    if(self::count()){
      $html = static::$prefix;
      if(!is_null($separator))
        self::$separator = $separator;

      foreach(self::crumbs() as $k => $c) {
        $html .= '<li class="'.(self::isLast($k)?'active':'').'">';
        if(self::isLast($k)) {
          $html .= ucfirst($c['t']);
        } else {
          if($c['l']){
            $html .= '<a href="'.$c['l'].'">'.ucfirst($c['t']).'</a>';
          }
          else {
            $html .= ucfirst($c['t']);
          }
          $html .= self::$separator;
        }
        $html .= '</li>';
      }
      return rtrim($html,self::$separator).static::$suffix;
    }
  }

}
