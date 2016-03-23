<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */


namespace Push\Http;

Class Upload implements \ArrayAccess
{

  const CONFIG_ERROR = 'Please Ensure you use the right `Upload Configurations`',
    NO_FILE_ERROR = 'Please a valid FILE selection is required',
    IMAGE_SIZE_ERROR = 'The Image is too Large : limit is %f',
    FILE_SIZE_ERROR = 'The file Size is too Large : limit is %f',
    UPLOAD_ERROR = 'An Error Occured during the Upload',
    NOT_ALLOWED_ERROR = 'Sorry!, but this service does not permit the upload of such file formats &mdash; Please Contact an Administrator.',
    WRITABLE_ERROR = 'Please Ensure if the file `Upload` location is `writable`',
    EXISTS_ERROR = 'This file already exists in the system, do rename and try again';

  const MAX_SIZE = 8000000,
    IMAGE_MAX_SIZE = 2000000;

  public static
    // default prefix for renaming files in server
    $prefix = 'uploads-',
    // default allowed extensions
    $extensions = ['zip', 'gif', 'png', 'jpg', 'rar', 'tar', 'bz', '7z', 'epub', 'doc', 'docx', 'ppt', 'pptx', 'pot', 'potx', 'pps', 'ppsx', 'pdf', 'chm', 'sis', 'sisx', 'jar', 'txt', 'htm', 'html', 'tar.gz', 'tgz'],
    // default allowed mimes
    $types = ['image', 'audio', 'video', 'application'];

  // class variables
  private static $_vars = [], $success, $temporary;

  public $file = [], $error = [], $directory, $dynamicName = true;

  /**
  !! --[ Methods ]-------- !!
  **/
  function __construct($upload)
  {
    if(!is_array($upload)){
      $this->error[] = self::CONFIG_ERROR;
      return false;
    }

    $this->directory = isset($upload['location']) ? $upload['location'] : UPLOADS;
    $this->dynamicName = isset($upload['dynamicName']) ? $upload['dynamicName'] : true;

    if(!isset($upload['file']['name'])){
      $this->error[] = self::NO_FILE_ERROR;
      return false;
    }

    $this->input($upload['file']);

    if($this->hasError()) return false;

    $this->rename($upload);

    if($this->hasError()) return false;

    $this->move();

    return $this->hasError() ? false : (self::$success = true);
  }

  protected function input($info = [])
  {
    $this->file['original'] = is_array($info['name']) ? strtolower(basename($info['name'][0])) : strtolower(basename($info['name']));
    self::$temporary = is_array($info['tmp_name']) ? $info['tmp_name'][0] : $info['tmp_name'];
    $this->errNo = is_array($info['error']) ? $info['error'][0] : $info['error'];
    $this->file['size'] = is_array($info['size']) ? $info['size'][0] : $info['size'];
    $this->file['mime'] = is_array($info['type']) ? $info['type'][0] : $info['type'];

    if(!empty($this->file['mime'])){
      list($this->file['type'], $this->file['format']) = explode('/', $this->file['mime']);
    }

    $parts = pathinfo($this->file['original']);

    $this->file['extension'] = !empty($parts['extension']) ? strtolower($parts['extension']) : 'txt';

    $this->file['title'] = $this->convert(strlen($parts['filename']) > 0 ? strtolower($parts['filename']) : 'no_name').'.'.$this->file['extension'];

    $this->checkError();
  }
  protected function rename($upload)
  {
    self::$prefix = isset($upload['prefix']) ? $upload['prefix'] : self::$prefix;

    $this->file['name'] = $this->convert(self::$prefix);

    if($this->dynamicName) $this->file['name'] .= uniqid().date("dmyGis");

    $this->file['name'] .= '.'.$this->file['extension'];

    if(!file_exists($this->directory)) $this->mkdir($this->directory, 0777);

    if(!isset($this->file['type'])) return false;

    $dir = $this->directory.DS.$this->file['type'].'s';

    if(!file_exists($dir)) $this->mkdir($dir, 0777);

    $this->file['location'] = $dir.DS.$this->file['name'];
    $this->file['url'] = UPLOADS_URI.str_replace(DS, '/', trim_domain($this->file['location']));

    if(file_exists($this->file['location']))
      $this->error[] = self::EXISTS_ERROR;
  }
  protected function move()
  {
    if(!move_uploaded_file(self::$temporary, $this->file['location']))
      $this->error[] = self::WRITABLE_ERROR;
  }

  protected function testType()
  {
    if(!isset($this->file['type']))
      return false;
    if($this->file['type'] == 'image' && !$this->is_img())
      return false;
    return (in_array($this->file['extension'], self::$extensions)
      && in_array($this->file['type'], self::$types) && $this->type_full === $this->full);
  }

  protected function testSize()
  {
    if($this->is_img() && $this->file['size'] > self::IMAGE_MAX_SIZE)
      return false;
    return $this->file['size'] < self::MAX_SIZE;
  }

  public function success()
  {
    return self::$success;
  }
  public function errors()
  {
    return $this->error;
  }
  public function file()
  {
    return $this->file;
  }

  protected function hasError()
  {
    return count($this->error) > 0;
  }
  protected function checkError()
  {
    if(strlen(trim($this->file['original'])) < 0)
      $this->error[] =  self::NO_FILE_ERROR;
    elseif($this->errNo != 0)
      $this->error[] =  self::UPLOAD_ERROR;
    elseif(!$this->testSize() && $this->is_img())
      $this->error[] = sprintf(self::IMAGE_SIZE_ERROR, self::byte(self::IMAGE_MAX_SIZE));
    elseif(!$this->testSize())
      $this->error[] =  sprintf(self::FILE_SIZE_ERROR, self::byte(self::MAX_SIZE));
    elseif(!$this->testType())
      $this->error[] = self::NOT_ALLOWED_ERROR;
  }

  protected function is_img($image = null)
  {
    if($image===null) $image = self::$temporary;
    return @getimagesize($image) ? @getimagesize($image): false;
  }

  public static function extensions($ext = [])
  {
    if(is_array($ext)){
      self::$extensions = [];
      self::$extensions = $ext;
    }
  }

  public static function types($types = []){
    if(is_array($types)){
      self::$types = [];
      self::$types = $types;
    }
  }

  protected function mkdir($path, $mode = 0755){
    return mk_dir($path, $mode);
  }
  protected function convert($url){
    return str_convert($url, '_');
  }

  protected function byte($size){
    return byte_size($size, 1);
  }

  function __set($name, $value){
    self::$_vars[$name] = $value;
  }

  function __get($name){
    return isset(self::$_vars[$name]) ? self::$_vars[$name]: null;
  }

  function __unset($name){
    unset(self::$_vars[$name]);
  }

  function __isset($name){
    return isset(self::$_vars[$name]);
  }

  function offsetSet($name, $value){}
  function offsetUnset($key){}
  function offsetGet($name){
    return isset($this->file[$name]) ? $this->file[$name]: null;
  }
  function offsetExists($name){
    return isset($this->file[$name]);
  }

}

