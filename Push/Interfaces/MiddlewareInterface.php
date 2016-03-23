<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */
namespace Push\Interfaces;

interface MiddlewareInterface {
  /**
  * Required for transversing down the Middleware queue
  * @return mixed
  */
  public function call();
}

