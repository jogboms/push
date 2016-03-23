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

Class Str
{
  static $key;

  /**
  * Replaces string with arguments [translator]
  *
  * First argument is the text `key` then followed by the replacements
  * @uses array(...'error.info' => 'We all at %s really %s to %s for the Web',...)
  * @uses -- OR ---
  * @uses array(...'error.info' => 'We all at %1% really %2% to %3% for the Web',...)
  * @uses Str::__('error.info', 'PUSHMVC', 'love', 'Code')
  */
  public static function __()
  {
    $args = func_get_args();
    if(func_num_args() == 0)
      throw new Exception(__CLASS__.'::'.__FUNCTION__.'() requires at least one Argument => $key');

    $Texts = require_once INCLUDES.DS.'texts.inc.php';

    # Replace using pattern modifiers eg %s, %d
    return vsprintf($Texts[array_shift($args)], $args);
    # --- OR ---
    # Replace using `Str::sprintf` syntax %1%, %2% in increasing order
    # eg Who %1% kept the %2% on the %3%
    // static::$key = $args[0];
    // return call_user_func_array(array(__CLASS__,'sprintf'), ((array)$file[$args[0]] + $args));
  }

  static function sprintf()
  {
    $key = func_get_args();
    return preg_replace_callback('~%(.*?)%~', function($r) use ($key){
      if(!isset($key[$r[1]]))
        throw new Exception(__CLASS__.'::'.__FUNCTION__.'(`'.Str::$key.'`) Variables Error : Requires %'.($r[1]).'% Replacement value');
      return ucwords($key[$r[1]]);
    }, $key[0]);
  }

  /*
  * @see function.inc.php for textCut()
  */
  static function cut($text, $limit = 20, $link = '')
  {
    // return $text = strip_tags($text);
    $text = str_replace(array('<br />','<br >','<br/>'), '<br>', strip_tags($text, '<br><em>>'));
    return textCut($text, $limit, $link);
  }
  static function trunc($text, $limit = 20, $end = '...')
  {
    // $text = strip_tags($text);
    if(strlen(strip_tags($text)) <= $limit) return $text;
    $text = str_replace(array('<br />','<br >','<br/>'), '<br>', strip_tags($text, '<br><em>>'));
    return substr($text, 0, $limit).$end;
  }

  public static function eq($str, $length)
  {
    return strlen($str) === (int)$length;
  }
  public static function gt($str, $length, $orEqual = false)
  {
    if($orEqual) return strlen($str) >= (int)$length;
    return strlen($str) > (int)$length;
  }
  public static function lt($str, $length, $orEqual = false)
  {
    if($orEqual) return strlen($str) <= (int)$length;
    return strlen($str) < (int)$length;
  }

  /*
  * @see function.inc.php for cleanHTML()
  */
  static function in($string)
  {
    $string = cleanHTML($string);
    return htmlentities($string);
  }
  static function out($string, $strip_tags = false)
  {
    $string = html_entity_decode($string);
    if($strip_tags === true) $string = strip_tags($string);
    return $string;
  }
  static function strip($str)
  {
    return strip_tags($str);
  }
  static function countWords($text)
  {
    return count(explode(' ', $text));
  }
  static function br2nl($string)
  {
    return str_replace(array('<br>', '<br />', '<br/>'), '', $string);
  }

  static function parse($string, $type = 'html', $reverse = false)
  {
    return call_user_func_array('self::parse'.$type, array($string, $reverse));
  }
  static function convert($string, $from = false, $to = false)
  {
    switch ($from) {
      case 'bb':
        return ($to == 'md') ?
          self::parseMD(self::parseBB(nl2br($string)), true) : self::parseBB($string);
      case 'md':
        return ($to == 'bb') ?
         self::parseBB(self::parseMD(nl2br($string)), true) : self::parseMD($string);
      default:
        return ($to == 'md') ?
         self::parseMD(nl2br($string), true) : self::parseBB($string, true);
    }
  }
  private static function parseHTML($string, $reverse = false)
  {
    if($reverse === true){
      return strip_tags($string);
    }
    return nl2br(self::in($string));
  }
  private static function parseMD($string, $reverse = false)
  {
    if($reverse === true){
      include_once LIBRARY.DS.'html2markdown'.DS.'HTML_To_Markdown.php';
      $markdown = new HTML_To_Markdown($string);
      return $markdown->output();
    }
    include_once LIBRARY.DS.'markdown'.DS.'markdown.php';
    $parse = new MarkdownExtra_Parser;
    return $parse->transform($string);
  }
  private static function parseBB($string, $reverse = false)
  {
    if($reverse === true){
      include_once LIBRARY.DS.'Converter'.DS.'src'.DS.'Converter'.DS.'Converter.php';
      include_once LIBRARY.DS.'Converter'.DS.'src'.DS.'Converter'.DS.'HTMLConverter.php';
      $converter = new Converter\HTMLConverter($string, 1);
      return $converter->toBBCode();
    }
    include_once LIBRARY.DS.'jbbcode'.DS.'jbbcode'.DS.'Parser.php';
    $parser = new JBBCode\Parser();
    return $parser
      ->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet())
      ->parse($string)
      ->getAsHtml();
      // ->getAsBBCode();
  }
  public static function plural($string, $count)
  {
    /**
     * TODO
     * This approach is totally lame
     */
    $vows = array('a','e','i','o','u');
    // $plurals = array('')
    $l = strlen($string);
    $end = substr($string, $l-1);
    // echo $l.' ';
    if($count > 1){
      $plural = 's';
      if($end == 'y'){
        $b_e = substr($string, $l-2, 1);
        if(!in_array($b_e, $vows))
          $plural = 'ies';
      }
      $string = substr($string,0,$l-1).$plural;
    }

    return $string;
  }
  public static function replace_on_count(array $replacements, $count)
  {
    if($count > 1 && array_key_exists(1, $replacements))
      return $replacements[1];
    if($count == 0 && array_key_exists(2, $replacements))
      return $replacements[2];
    return $replacements[0];
  }

}
