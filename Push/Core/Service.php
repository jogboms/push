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

abstract Class Service extends \Push\Utils\Collection
{
  protected
    /**
     * @var \Push\Application Instance of Push MVC
     */
    $app;

  /**
   * Start the Service
   * @param \Push\Application $app Instance of the Application
   * @return self
   */
  abstract function start(\Push\Application $app);
  /**
   * Stop the Service
   * @param \Push\Application $app Instance of the Application
   * @return self
   */
  function stop(\Push\Application $app) {}
  /**
   * Calls on the Service's boot method when invoked
   * @param \Push\Application $app
   * @return self
   */
  final function __invoke(\Push\Application $app)
  {
    if(method_exists($this, 'stop')){
      $service = $this;
      $app->on('app.after.run', function() use($service, $app){
          return $service->stop($app);
      });
    }
    return $this->start($app);
  }
}
