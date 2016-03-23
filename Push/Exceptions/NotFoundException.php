<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */

namespace Push\Exceptions;

Class NotFoundException extends \Exception
{

  public function __construct($message = null)
  {
    if(is_null($message))
      $message = 'A notFound Error was found';

    parent::__construct($message);
  }
}
