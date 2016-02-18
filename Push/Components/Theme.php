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

namespace Push\Components;

Class Theme {
	public static $_css = [], $_js = [];
	
	/* dynamic stylesheets */
	static function setCss($file, $path = null, $remote = false){
		if(func_get_arg(1) === true){
			$path = dirname($file).'/';
			$file = basename($file);
			$remote = true;
		}
		else {
			$file = trim($file, '/');
			if($path === null) $path = ASSET_URI.'/css/';
			else $path = trim($path, '/').'/';
		}
		static::$_css[] = ['file'=>$file, 'path'=>$path, 'remote'=>$remote];
	}
	static function getCss(){
		$link = '<!--// DONE WITH AN ON-THE-FLY CSS INCLUSION PHP SNIPET BY @jogboms //-->';
		foreach(static::$_css as $css){
			if($css['remote'] === false && is_readable(ASSET_PATH.DS.'css'.DS.$css['file']))
				$link .= '<link href="'.$css['path'].$css['file'].'" rel="stylesheet" />';
			elseif($css['remote'] === true)
			 $link .= '<link src="'.$css['path'].$css['file'].'" rel="stylesheet" />';
			else $link .= '<style>'.$css['file'].'</style>';
		}
		$link .= '<!--// END INCLUSION //-->';
		return $link;
	}

	/* dynamic javascript */
	static function setJs($file, $position = 'bottom', $path = null, $remote = false){
		if($position === true || $path === true){
			$path = dirname($file).'/';
			$file = basename($file);
			$position = func_get_arg(1) === true ? 'bottom' : func_get_arg(1);
			$remote = true;
		}
		else {
			$file = trim($file, '/');
			if($path === null) $path = ASSET_URI.'/js/';
			else $path = trim($path, '/').'/';
		}
		static::$_js[] = ['file'=>$file, 'pos'=>$position, 'path'=>$path, 'remote'=>$remote];
	}
	static function getJs($position = 'bottom'){
		$script = '<!--// DONE WITH AN ON-THE-FLY JS INCLUSION PHP SNIPET BY @jogboms //-->';
		foreach(static::$_js as $js){
			if($js['pos'] !== $position) continue;
			if($js['remote'] === false && is_readable(ASSET_PATH.DS.'js'.DS.$js['file']))
				$script .= '<script src="'.$js['path'].$js['file'].'"></script>';
			elseif($js['remote'] === true)
			 $script .= '<script src="'.$js['path'].$js['file'].'"></script>';
			else $script .= '<script>'.$js['file'].'</script>';
		}
		return $script.'<!--// END INCLUSION //-->';
	}
	static function runJS($file, $path = null, $remote = false){
		return static::setJs($file, 'top', $path, $remote);
	}
	static function dump(){
		return array(
			'CSS' => static::$_css,
			'JS' => static::$_js
			);
	}
}