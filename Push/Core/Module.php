<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */



namespace Push\Core;

abstract Class Module extends \Push\Core\Service
{
  /**
   * Start the Service
   * @return string Namespace Path on how to get to its Controllers from App\Controller
   * @example return 'api\v2\'
   */
  abstract function getControllerPath();

  // abstract function getRoutes();
}
