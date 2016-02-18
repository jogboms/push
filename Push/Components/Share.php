<?php
/**
* @version      $Id: share.class.php 2014-01-02 07:16:57 Jeremiah Ogbomo $
* @package      1WebCMS.Framework
* @subpackage   Application
* @copyright   Copyright (C) 2012 - 2014 1Web Concepts. All rights reserved.
* @license      GNU/GPL, see LICENSE.txt
* 1WebCMS is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.txt for copyright notices and details.
*/
namespace Push\Components;

Class Share {
   static $width = '18';
   static $imgLink = '/img/social';
   static $imgStyle = ' align ="middle" border ="0" style="margin:2pt 2pt 2pt 1pt;"';

   static function selfURL() {
      $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? 's' : '';
      $protocol = self::strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
      $port = ($_SERVER["SERVER_PORT"] == "80") ? '' : (':'.$_SERVER["SERVER_PORT"]);
      return urlencode($protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI']);
   }
   static function strleft($s1, $s2){
      return substr($s1, 0, strpos($s1, $s2));
   }
   static function link($title, $href, $src, $alt,$width=''){
      $width = empty($width) ? self::$width : $width;
      return '<a target="_blank" title="'.$title.'" href="'.$href.'"><img width="'.$width.'" src="/'.self::$imgLink.'/'.$src.'" '.self::$imgStyle.' alt ="'.$alt.'" /></a>';
   }   
   static function twitter($text, $width='', $link = ''){
      $link = ($link !== '') ? $link : self::selfURL();
      return self::link('Post on twitter?', 'https://twitter.com/intent/tweet?text='.urlencode($text).'&url='.$link, 'twitter.png', 'twitter',$width);
   }
   static function facebook($text, $width='', $link = ''){
      $link = ($link !== '') ? $link : self::selfURL();
      return self::link('Share on Facebook?', 'https://www.facebook.com/sharer.php?u='.$link.'&t='.urlencode($text), 'facebook.png', 'facebook',$width);
   }
   static function gplus($text, $width='', $link = ''){
      $link = ($link !== '') ? $link : self::selfURL();
      return self::link('Share on Google plus?', 'https://plusone.google.com/_/+1/confirm?hl=en-US&url='.$link, 'gplus.png', 'google plus',$width);
   }
   static function digg($text, $title = 'my+title',$width='', $link = ''){
      $link = ($link !== '') ? $link : self::selfURL();
      return self::link('Post on digg?', 'http://digg.com/submit?phase=2&url='.$link.'/&title='.urlencode($text), 'digg.png', 'digg',$width);
   }
   static function delicious($text, $width='', $link = ''){
      $link = ($link !== '') ? $link : self::selfURL();
      return self::link('Post on delicious?', 'http://del.icio.us/save?url='.$link, 'delicious.png', 'delicious',$width);
   }

}
