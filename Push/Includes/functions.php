<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */

// APP SPECIFIC FUNCTIONS
function push($function = null){
  return !is_null($function) ? \Push\Application::init()->$function() : \Push\Application::init();
}
function url(){}
function RootPath($path, $replace = false){
  if($replace == true)
    $path = str_replace('/', DS, $path);
  return DOC_ROOT.DS.trim($path, DS);
}

function pre($args){
  echo '<pre>';
  if(func_num_args()>1)
    foreach (func_get_args() as $array){
      print_r(empty($array)?'NULL':$array); echo '<br />';
    }
  else print_r($args);
  echo '</pre>';
}
function _d($type, $value){
  if($value>1)
    foreach ($value as $array)
      pre(($type === 'alert') ? $array : var_dump($array));
  else pre(($type === 'alert') ? $value : var_dump($value));

  list($b,$a) = debug_backtrace();
  pre('<br /> Function <strong>'.$type.'()</strong> called in <strong>'.basename($a['file']).'</strong> on line <strong>'.$a['line'].'</strong><br /> Location : <strong>'.str_replace(DOC_ROOT,'',dirname($a['file'])).'</strong>');
  exit;
}
function alert($args = ''){
  _d('alert', func_get_args());
}
function dump(){
  echo '<pre>', _d('dump', func_get_args()), '</pre>';
}
function debug($args = ''){
  if(ob_get_length()) ob_end_clean();
  _d('alert', func_get_args());
}
function _debug(){
   // pre(debug_print_backtrace());
   // pre(debug_backtrace());
   echo '<pre>', debug_print_backtrace(), '</pre>';
}

function now(){
  return Date('y-m-d H:i:s');
}
function clean_input($value){
  return (is_array($value) or is_object($value)) ?
    array_map('clean_input', $value): ((is_string($value) ? addslashes($value) : $value));
}
function clean_output($value){
   return (is_array($value) or is_object($value)) ?
    array_map('clean_output', $value): ((is_string($value) ? stripslashes($value) : $value));
}
function cleanHTML($string){
  return strip_tags($string, '<a><abbr><acronym><area><article><aside><b><big><blockquote><caption><cite><class><code><col><del><details><dd><div><dt><em><figure><figcaption><footer><font><h1><h2><h3><h4><h5><h6><header><hgroup><hr><i><img><ins><kbd><li><map><ol><p><pre><q><s><section><small><span><strike><strong><sub><summary><sup><table><tbody><td><tfoot><th><thead><tr><tt><u><ul><var>');
}

function is_empty($var){
  if(is_string($var) && trim($var) === '')
    return true;
  return isset($var) && empty($var);
}
function is_true($var){
  return $var === true;
}

function fb_app_id(){
  return push()->config['fb_app_id'];
}

function trim_root($path){
  return is_file($path) ? str_replace(DOC_ROOT,'', dirname($path)) :
  str_replace(DOC_ROOT,'', $path);
}
function trim_domain($path){
  return is_file($path) ? str_replace(ROOT,'', dirname($path)) :
  str_replace(ROOT,'', $path);
}

function sortBy($key) {
  return function ($o, $p) use ($key) {
    return strnatcmp($o[$key], $p[$key]);
  };
}
function array_col_sort(&$array, $col, $method = SORT_ASC){
   $s = array();
   foreach ($array as $k => $v) $s[$k] = $v[$col];
   array_multisort($s, $method, $array);
   return $array;
}
function array_val_sort(&$array, $method = SORT_ASC){
  uasort($array, function($a, $b) use($method) {
    if($a == $b) return 0;
    if($method == SORT_ASC)
      return ($a < $b) ? -1 : 1;
    return ($a > $b) ? -1 : 1;
  });
  return $array;
}
function textCut($text, $limit = 50, $link = ''){
  $x = explode(' ', $text);
  $a = '';
  if(count($x) > $limit){
    for($i = 0 ;$i < $limit; $i++) $a .= $x[$i].' ';
      return $a.'[&hellip;]'.(!empty($link)?' &nbsp; <a href="'.$link.'">continue reading</a>':'');
  }
  return $text;
}

/**
 * Generate random string
 * @param  integer $min Minimum Length of string
 * @param  integer $max Maximum Length of string
 * @return sting
 */
function random($min=6,$max=null){
  if($max === null) $max=$min;
  $validChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
  $maxChars = strlen($validChars)-1;
  $length = mt_rand($min,$max);
  $password = '';
  for($i=0;$i<$length;$i++) $password .= $validChars[mt_rand(0,$maxChars)];
  return $password;
}
/**
 * Generate password string
 * @param  integer $min Minimum Length of string
 * @param  integer $max Maximum Length of string
 * @return sting
 */
function password($min=6,$max=30){
  return random($min, $max);
}

/**
* Parses text with @handles and #hashtags
*
* Default Parameters are compatible with Twitter
*
* @param string $text
* @param string $domain
* @param string $handleAppend
*        example "?username=" or "users/"
* @param string $hashAppend
*        example "?search_query=" or "search/"
*/
function Handle_Hashtag($text, $domain, $handleAppend = '', $hashAppend = '?search&amp;q='){
  return preg_replace_callback(array('~(?<!\S)#[a-zA-Z_0-9-]+~','~(?<!\S)@[a-zA-Z_0-9-]+~'),
    function($m) use($domain, $handleAppend, $hashAppend){
      return (
        '<a target="_blank" href="'.rtrim($domain, '/').'/'.
        ($_ = (substr($m[0],0,1)=='#') ? ltrim($hashAppend, '/') : ltrim($handleAppend, '/')).
        substr($m[0], 1).
        ($_ ? '&amp;s=hash' : '').
        ('"title="'.$m[0]).
        '" id="hash_tag">'.$m[0].'</a>'
      );
    }
    ,$text);
}

/**
 * Create a recursive directory path
 * @param  string  $path
 * @param  integer $mode CHMOD value
 * @return void
 */
function mk_dir($path, $mode = 0755){
  $_ = DOC_ROOT;
  $path = trim(trim_root($path), DS);
  foreach(explode(DS, $path) as $path){
    if(!file_exists($_.DS.$path)) mkdir($_.DS.$path, $mode);
    $_ .= DS.$path;
  }
}
/**
 * Create a URL-friendly string
 * @param  string $str
 * @param  string $_   String to serve as Glue
 * @return string
 */
function str_convert($str, $_ = '_'){
  $str = strtolower($str);
  $str = str_replace(
  array(' ','.','@','/','\\',';',':','+','-'), $_, $str);
  $str = str_replace(array('&amp','&'), $_.'and'.$_, $str);
  $str = preg_replace('~[^a-zA-Z0-9-]~', $_, $str);
  return str_replace(array($_.$_.$_,$_.$_,$_.$_), $_, $str);
}
/**
 * Create a Human-friendly BYTE representation
 * @param  mixed|int|float  $size
 * @param  integer $precision Round-off precision
 * @return mixed
 */
function byte_size($size, $precision = 2){
  $s = array('b', 'Kb', 'Mb', 'Gb', 'Tb');
  for ($i = 0; $i <= 5; $i++) {
    if(($size/pow(2, ($i+1)*10)) > 1) continue;
    return round($size/pow(2, $i*10), $precision).$s[$i];
  }
}

function image($link, $size = 'md'){
  if(is_int(strpos($link, 'default_'))){
    return $link;
  }
  if(strpos($link, ROOT_URI) !== ''){
    $remote = str_replace(array(ROOT_URI, '/'), array(ROOT, DS), $link);

    if(!file_exists($remote)) return $link;
    if($size){
      $sizes = array(
        'lg' => 98,
        'md' => 50,
        'sm' => 25,
        'xs' => 9,
        );

      $folder = $size;

      if(is_string($size) && array_key_exists($size, $sizes)){
        $size = $sizes[$size];
      }

      $path = pathinfo($remote);

      if(!file_exists($dir = $path['dirname'].DS.$folder)){
        mkdir($dir, 0775);
      }

      if(file_exists($image = $dir.DS.$path['basename'])){
        return pathinfo($link, PATHINFO_DIRNAME).'/'.$folder.'/'.$path['basename'];
      }

      Image_handler::create($remote)->scale($size)->save($image);

      return pathinfo($link, PATHINFO_DIRNAME).'/'.$folder.'/'.$path['basename'];
    }
  }
return $link;
}


function redirect($url,$time="3",$message=null) {
  if(!is_null($message)) echo $message;
  echo "<meta http-equiv='refresh' CONTENT='$time; URL=$url'>"; exit;
}

function sqlTime(){
  return Date("y-m-d H:i:s");
}
function mix_array($keys, $values){
  $new = array();
  foreach($keys as $a => $b) $new[$b] = $values[$a];
  return $new;
}
function solveMath($a=1,$b=1,$c){
  $a = (float)$a;$b = (float)$b;$c = (string)$c;
  switch($c){
    case '*': return $a*$b;
    case '/': return $a/$b;
    case '-': return $a-$b;
    default: return $a+$b;
  }
}
function mail_conv($arg){
  $new = str_replace('@', '[at]', $arg);
  $new = str_replace('.', '[dot]', $new);
  return $new;
}
function br2nl($html){
  return str_replace('&lt;br /&gt;', ' ', $html);
}

