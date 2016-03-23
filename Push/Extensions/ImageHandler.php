<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */

namespace Push\Extensions;

Class Image_handler
{
  public $cimage, $image, $width, $height;

  function Image_handler($image_file)
  {
    include(LIBRARY.DS.'image_resizer'.DS.'cbImage.php');
    include(LIBRARY.DS.'image_resizer'.DS.'ImageResizer.class.php');
    $this->cimage = new Image($image_file);
    $this->image = new ImageResizer($this->cimage);
    $image = getimagesize($image_file);
    $this->width = $image[0];
    $this->height = $image[1];
  }

  function resize($width=320, $height=200)
  {
    $this->image->Resize($width,$height);
  }
  function resize_w($width=320)
  {
    $this->image->ResizeWidth($width);
  }
  function resize_h($height=200)
  {
    $this->image->ResizeHeight($height);
  }
  function save($new_image='new_image.jpg', $quality=100)
  {
    $this->image->Save($new_image,$quality);
  }
  function thumb($width=120, $height=120)
  {
    $this->image->Thumbnail($width,$height);
  }
  function show()
  {
    $this->image->show();
  }
  function type()
  {
      $this->cimage->getType();
  }
  function scale($scale=50)
  {
    $this->image->ScalePercentage($scale);
  }
  function end()
  {
    $this->image->Close();
  }
}
