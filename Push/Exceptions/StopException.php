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

Class StopException extends \Exception
{

  public function __construct($message = null)
  {
    if(is_null($message))
      $message = 'This Application was stopped';

    parent::__construct('Quit?! '.$message);
  }
}
