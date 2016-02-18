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

namespace Push\Utils;

Class Encrypt 
{

   private static $init;
   private $iv, $key = 'w890sqre234svdkueg37t4bjz7937rgzf63';
   
   private function __construct(){
      if(function_exists('hash')){
         $this->key = hash('sha256', $this->key, true);
         $this->iv = mcrypt_create_iv(32, MCRYPT_RAND);
      }
   }
   private function __clone(){}

   private static function _init(){
      return (!isset(self::$init)) ? self::$init = new self: self::$init;
   }
      
   private static function _check(){
      if(function_exists('hash'))
         return self::_init();
      return false;
   }
   public static function _encrypt($value){
      if($crypt = self::_check())
         return strtr(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $crypt->key, $value, MCRYPT_MODE_ECB, $crypt->iv)), '+/=', '-_#');
      return $value;
   }
   
   public static function _decrypt($value){
      if($crypt = self::_check())
         return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $crypt->key, base64_decode(strtr($value, '-_#', '+/=')), MCRYPT_MODE_ECB, $crypt->iv));
      return $value;
   }
}
