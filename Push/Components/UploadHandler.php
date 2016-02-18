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
namespace Push\Components;

Class UploadHandler {
   private static $table = 'dream_uploads', $dbh, $params;
   
   public static function upload($params){
      self::$dbh = \Application::init()->getDB();
      self::$params = $params;
      self::$params['desc'] = '';

      $upload = new \Push\Http\Upload($params['upload']);
      if($upload->success()){
         if(isset($params['deleteExist']) && $params['deleteExist'] == true
            && self::exists($upload['title'])){
            self::delete($upload);
         }
         if($id = self::insert($upload)){
            return new UploadResults(
               array(
                  'id' => $id,
                  'file' => $upload['url'],
                  'title' => $upload['title'],
                  'location' => $upload['location'],
                  'size' => $upload['size']
               ), true);
         }
      }
      return new UploadResults($upload->errors());
   }

   protected static function exists($name){
      $sql = 'SELECT * FROM '.self::$table.' where upload_filetitle ="'.$name.'" and user_id = '.self::$params['user_id'];
      return self::$dbh->numRows($sql) != 0; 
   }
   protected static function insert($info){
      $keys = array('user_id', 'upload_filetitle', 'upload_filename', 'upload_fileext', 'upload_filesize', 'upload_filetype', 'upload_filedir', 'upload_desc', 'upload_created');
      $values = array(self::$params['user_id'], $info['title'], $info['name'], $info['ext'], $info['size'], $info['type'], $info['url'], self::$params['desc'], self::$dbh->datetime);
      return self::$dbh->insert(self::$table, $keys, $values);
   }

   protected static function delete($file){
      @unlink($file['location']);
      return self::$dbh->delete(self::$table, 'WHERE upload_filetitle = "'.$file['title'].'" and user_id = '.self::$params['user_id']);
   }

}

Class UploadResults extends Collection {
   private $error = false;

   function __construct($file, $is_ok = false){
      $this->error = ($is_ok === false);
      $file = ($is_ok === false) ? array('errors' => $file) : $file;
      parent::__construct($file);
   }
   public function is_ok(){
      return $this->error === false;
   }
   public function file(){
      return $this->params();
   }
}