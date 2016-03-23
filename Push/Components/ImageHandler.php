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

Class ImageHandler
{
  public $cimage, $image, $width, $height;

  function __construct($image_file)
  {
    include_once(LIBRARY.DS.'image_resizer'.DS.'cbImage.php');
    include_once(LIBRARY.DS.'image_resizer'.DS.'ImageResizer.class.php');
    if(!empty($image_file)){
      $this->cimage = new Image($image_file);
      $this->image = new ImageResizer($this->cimage);
      @list($this->width, $this->height) = getimagesize($image_file);
    }
  }

  public static function create($image_file)
  {
    return new self($image_file);
  }

  function resize($width=320, $height=200)
  {
    $this->image->Resize($width,$height);
    return $this;
  }
  function resize_w($width=320)
  {
    $this->image->ResizeWidth($width);
    return $this;
  }
  function resize_h($height=200)
  {
    $this->image->ResizeHeight($height);
    return $this;
  }
  function save($new_image='new_image.jpg', $quality=100)
  {
    $this->image->Save($new_image,$quality);
    return $this;
  }
  function thumb($width=120, $height=120)
  {
    $this->image->Thumbnail($width,$height);
    return $this;
  }
  function show()
  {
    $this->image->show();
    return $this;
  }
  function type()
  {
    $this->cimage->getType();
    return $this;
  }
  function scale($scale=50)
  {
    $this->image->ScalePercentage($scale);
    return $this;
  }
  function end()
  {
    $this->image->Close();
  }
}
