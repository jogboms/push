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

interface ViewInterface {
  /**
   * Initialize or Boot the View implementation instead of using __construct
   * @return mixed
   */
  public function initialize();
  /**
   * Return Instance of Parser object
   * @return object
   */
  public function getEnvironment();
  /**
   * Parse a template file
   * @param  string $filename Filename of template file
   * @return mixed
   */
  public function parse($filename, $variables = []);
  /**
   * Assign a value to a variable or an Array containing keys => values
   * @param  string|array $key   Name of variable to assign or Array of keys => values
   * @param  mixed $value Value to assign to variable or NULL if $key is an Array
   * @return self
   */
  public function assign($key, $value = null);
}

