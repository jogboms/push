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
namespace Push\Services;

Class Error extends \Push\Core\Service 
{

	final public function start(\Push\Application $app)
	{
		$err = $this;
		$err->debug = $app->env('APP_DEBUG');

		$app->onError(function($req,$res,$error) use($err){
			return $err->onError($req,$res,$error);
		})
		->onShutdown(function($req,$res,$error) use($err){
			return $err->onShutdown($req,$res,$error);
		});

		$onError = $app->onError;
		$onShutdown = $app->onShutdown;

		$report = ($app->env('STRICT_DEBUG') == true) ? E_ALL: E_ALL & (~E_NOTICE & ~E_WARNING);
		
		set_error_handler(function() use($onError){
			$onError(new \ErrorException(func_get_arg(1)));
		}, $report);

		set_exception_handler(function($e) use($onError){
			$onError($e);
		});

		register_shutdown_function(function() use($onShutdown){
			if($err = error_get_last()) {
				$onShutdown($err);
			}
		}, $report);

		error_reporting(0);
		ini_set('display_errors', 0); // Set SERVER Environment 
		ini_set('log_errors', 1);
		ini_set('error_log', STORAGE.DS.'errors_log.txt');
		
		if($err->debug){
			// ini_set('display_errors', 1); // Set SERVER Environment 
			error_reporting($report);
		}
	}

	public function onShutdown($req, $res, $err)
	{
		switch($err['type']){
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_NOTICE:
			case E_USER_ERROR:
				if(ob_get_length()) ob_end_clean();
				$content = '';
				if($req->isAjax()){
					$content .= '&middot;|O_o|&middot;';
					$content .= "\n".'================================='."\n";
					$content .= 'Message : "'.$err['message'].'"'."\n";
					$content .= "\nFile : [".$err['line'].'] '.basename($err['file'])."\n";
					$content .= "\n".'Folder : '.trim_root($err['file']);
					$content .= "\n".'================================='."\n";
				} else {
					$content .= '<!doctype html> <html> <head> <style type="text/css">
								.main {font-family:"Segoe UI Light";text-align:center;overflow-wrap:break-word;max-width:640px;margin:3em auto;}
								.main h1 {color:red;font-size:250%;font-weight:500;}
								.main pre {line-height:1.75;background:#eee;padding:15px;}
							</style> </head> <body> <div class="main"> <h1>&middot;|O_o|&middot;</h1> <div>';
					$content .= '<pre>'.strtoupper(preg_replace('~`(.*?)`~', '<code>`\\1`</code>', $err['message'])).'</pre> <pre>';
					$content .= basename($err['file']).'</pre> <pre>['.$err['line'].'] '.trim_root($err['file']);
					$content .= '</pre> </div> </div> </body> </html>';
				}
				$res->write($content);
			break;
		}
	}

	public function onError($req, $res, $err)
	{
		// Very important : clears all already sent headers
		if(ob_get_length()) ob_end_clean();

		$content = '';
		if($req->isAjax()){
			$content .= '&middot;|O_o|&middot;'.NL.'================================='.NL;
			$content .= 'Message : "'.\Push\Utils\Str::strip(\Push\Utils\Str::br2nl($err['message'])).'"'.NL;
			if(($c = array_shift($err['trace'])) && isset($c['file'])){
				$content .= 'File : ['.$err['line'].'] '.basename($c['file']).NL;
				$content .= 'Folder : '.trim_root($c['file']).NL;
			}
			$content .= NL.'================================='.NL;

			$i = 1;
			foreach($err['trace'] as $e){
				if($i>5) break;
				if(isset($e['file'])){
					$content .= 'File : ['.$e['line'].'] '.basename($e['file']).''.NL;
					$content .= 'Folder : '.trim_root($e['file']).''.NL;
					$content .= 'Function : '.(isset($e['class'])?$e['class'].'::':'').$e['function'].'()'.NL;
					$content .= '---------------------------------'.NL;
				}
				$i++;
			}
			$content .= NL;
		} else {
			$content .= '<!doctype html> <html> <head> <style type="text/css">
						.main {font-family:"Segoe UI Light";text-align:center;overflow-wrap:break-word;max-width:640px;margin:3em auto;}
						.main h1 {color:red;font-size:250%;font-weight:500;}
						.main pre {line-height:1.75;background:#eee;padding:15px;}
					</style> </head> <body> <div class="main"> <h1>&middot;|O_o|&middot;</h1><br /> <section> <pre>';
				$content .= preg_replace('~`(.*?)`~', '<code>`\\1`</code>', $err['message']);
				$content .= '</pre>';
			if(($c = array_shift($err['trace'])) && isset($c['file'])){
				$content .= '<pre>'. trim_root($c['file']). '</pre>';
				$content .= '<pre>['.$c['line'].'] '. basename($c['file']) .'</pre>';
			}
			$content .= '</section> <br /> -------------&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong style="color:red">&middot;|&bull;_&bull;|&middot;</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;------------- <br /> <br />';
		
			$i = 1;
			foreach($err['trace'] as $e){
				if($i>5) break;
				if(isset($e['file'])){
					$content .= '<pre style="text-align:left">';
					$content .= (isset($e['class'])?$e['class'].'::':'').$e['function'].'()<br />';
					$content .= '['.$e['line'].'] '.trim_root($e['file']).' ~ '.basename($e['file']);
					$content .= '</pre>';
				}
				$i++;
			}
			$content .= '</div> </body> </html>';
		}
		$res->write($content);
	}
}
