<?php
/**
 * PUSH MVC Framework.
 * @package PUSH MVC Framework
 * @version See PUSH.json
 * @author See PUSH.json
 * @copyright See PUSH.json
 * @license See PUSH.json
 * PUSH MVC Framework is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See PUSH.json for copyright notices and details.
 */

namespace Push\Views;

Class Twig extends \Push\View {
	protected 
		$twig = null,
		$extension = '.twig';

	public function getEnvironment(){
		if(!$this->twig){
			$loader = new \Twig_Loader_Filesystem(TEMPLATE_PATH);
			$this->twig = new \Twig_Environment($loader, [
				'cache' => TEMPLATE_PATH.DIRECTORY_SEPARATOR.'compilation_cache',
				'auto_reload' => true,
			]);
		}
		return $this->twig;
	}
	
	public function setExtensions(array $extensions){
		foreach ($extensions as $value) {
			$this->getEnvironment()->addExtension($value);
		}
		return $this;
	}
	public function createFilter($filter_name, $filter_function){
		$this->setFilter(new \Twig_SimpleFilter($filename, $filter_function));
		return $this;
	}
	public function setFilter(\Twig_SimpleFilter $filter){
		$this->getEnvironment()->addFilter($filter);
		return $this;
	}
	public function setFilters(array $filters){
		foreach ($extensions as $filter) {
			$this->setFilter($filter);
		}
		return $this;
	}
	public function initialize(){
	}

	public function parse($filename, $variables = []){
		return $this->getEnvironment()->render($filename, $variables);
	}

	public function assign($key, $value = ''){
		if(is_array($key)){
			foreach ($key as $k => $v) {
				$this->assign($k, $v);
			}
		}
		else {
			$this->getEnvironment()->addGlobal($key, $value);
		}
		return $this;
	}

}
