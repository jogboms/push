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

namespace Push;
use Push\Components\Theme;
use Push\Exceptions\ViewException;

abstract class View extends \Push\Utils\Collection 
	implements \Push\Interfaces\ViewInterface {

	protected 
		$extension = '.tpl.php', 
		$templateDir = '', 
		$_sections = [];

	public function __construct(\Push\Http\Environment $env, \Push\Http\Request $req){
		$this->initialize($env, $req);
		$this->assign('env', $env)->assign('request', $req);
	}

	public function setExtension($extension){
		$this->extension = $extension;
		return $this;
	}

	public function setRoot($directory = ''){
		$this->templateDir = rtrim(str_replace('/', DS, $directory), DS);
		return $this;
	}

	public function render($filename, $variables = []){
		if(!is_string($filename) || empty($filename))
			throw new ViewException('Invalid `template` name : `'.$filename.'`');

		$filename = str_replace('/', DIRECTORY_SEPARATOR, trim($filename)).$this->extension;

		$args = func_get_args();
		$args[0] = $filename;

		return call_user_func_array([$this, 'parse'], $args);
	}

	/**
	 * Initialize or Boot the View implementation instead of using __construct
	 * @return mixed
	 */
	public function initialize(){}
	/**
	 * Should return the instance of the object that does the actual rendering
	 * @return object 
	 */
	abstract public function getEnvironment();
	/**
	 * Parse a template file
	 * @param  string $filename Filename of template file
	 * @return mixed
	 */
	abstract public function parse($filename, $variables = []);
	/**
	 * Assign a value to a variable or an Array containing keys => values 
	 * @param  string|array $key   Name of variable to assign or Array of keys => values
	 * @param  mixed $value Value to assign to variable or NULL if $key is an Array
	 * @return self
	 */
	abstract public function assign($key, $value = null);

	/**
	 * Extensions
	 * @todo Move thes to a ViewExtension implemented class
	 */
		public function TITLE($title, $append = false, $delimiter = '> '){
			if(is_null($title) or empty($title)) return;
			if($append) $this->__TITLE .= ' '.$delimiter.ucfirst($title);
			else $this->__TITLE = ucfirst($title);
			return $this;
		}
		public function DESC($description, $append = false){
			if(is_null($description) or empty($description)) return;
			if($append) $this->__DESC .= ' '.ucfirst($description);
			else $this->__DESC = ucfirst($description);
			return $this;
		}
		public function AUTHOR($author){
			if(is_null($author) or empty($author)) return;
			$this->__AUTHOR = ucfirst($author);
			return $this;
		}

		public function CSS($filename = null, $path = null, $remote = false){
			if(is_null($filename)) return Theme::getCss();
			Theme::setCss($filename, $path, $remote);
			return $this;
		}
		public function JS($filename = null, $position = 'bottom', $path = null, $remote = false){
			if(is_null($filename)) return Theme::getJs($position);
			Theme::setJs($filename, $position, $path, $remote);
			return $this;
		}
		public function runJS($filename = null, $path = null, $remote = false){
			Theme::runJs($filename, $path, $remote);
			return $this;
		}

	public function __toString(){
		return '';
	}
}
