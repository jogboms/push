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

Class Uploads extends \Push\Model implements \ArrayAccess {
  protected static $table = 'uploads', $params;
  private $error = false;
  public $file = [];


  public function create($params = null)
  {
    self::$params = $params;
    self::$params['desc'] = '';

    $upload = new \Push\Http\Upload($params['upload']);
    if($upload->success()){
      if(isset($params['deleteExist']) && $params['deleteExist'] == true && $this->exists($upload['title'])){
        $this->delete($upload);
      }
      if($id = $this->insert($upload)){
        return $this->done(
          array(
              'id' => $id,
              'file' => $upload['url'],
              'title' => $upload['title'],
              'location' => $upload['location'],
              'size' => $upload['size']
          ), true);
      }
    }
    return $this->done($upload->errors());
  }
  private function done($file, $is_ok = false)
  {
    $this->error = ($is_ok === false);
    $this->file = ($this->error) ? ['errors' => $file] : $file;
    return $this;
  }
  public function exists($name)
  {
    $sql = 'SELECT * FROM '.$this->tb().' where uploads_filetitle ="'.$name.'" and user_id = '.self::$params['user_id'];
    return $this->db->numRows($sql) != 0;
  }
  public function insert($info)
  {
    $keys = ['users_id', 'uploads_filetitle', 'uploads_filename', 'uploads_fileext', 'uploads_filesize', 'uploads_filetype', 'uploads_filedir', 'uploads_desc', 'uploads_created'];
    $values = [self::$params['user_id'], $info['title'], $info['name'], $info['ext'], $info['size'], $info['type'], $info['url'], self::$params['desc'], $this->db->datetime];
    return $this->db->insert($this->tb(), $keys, $values);
  }
  public function delete($file = [])
  {
    @unlink($file['location']);
    return $this->db->delete($this->tb(), 'WHERE uploads_filetitle = "'.$file['title'].'" and user_id = '.$this->arams['user_id']);
  }


  public function images()
  {
    return $this->db->select($this->tb(), '*', 'WHERE '.$this->col('filetype').' = "image"')->fetchAll(FETCH_OBJECT);
  }
  public function videos()
  {
    return $this->db->select($this->tb(), '*', 'WHERE '.$this->col('filetype').' = "video"')->fetchAll(FETCH_OBJECT);
  }
  public function audios()
  {
    return $this->db->select($this->tb(), '*', 'WHERE '.$this->col('filetype').' = "audio"')->fetchAll(FETCH_OBJECT);
  }
  public function others()
  {
    return $this->db->select($this->tb(), '*', 'WHERE '.$this->col('filetype').' = "application"')->fetchAll(FETCH_OBJECT);
  }

  public function is_ok()
  {
    return $this->error === false;
  }
  public function file()
  {
    return $this->file;
  }

  function offsetExists($key)
  {
    return isset($this->file[$key]);
  }
  function offsetGet($key)
  {
    return isset($this->file[$key]) ? $this->file[$key] : null;
  }
  function offsetSet($key, $value){}
  function offsetUnset($key){}

}
