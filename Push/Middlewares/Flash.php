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
namespace Push\Middlewares;

defined('FLASH_SUCCESS') or define('FLASH_SUCCESS', 0);
defined('FLASH_ERROR') or define('FLASH_ERROR', 1);
defined('FLASH_INFO') or define('FLASH_INFO', 2);
defined('FLASH_WARNING') or define('FLASH_WARNING', 3);

Class Flash 
{
	const ERROR = FLASH_ERROR,
		SUCCESS = FLASH_SUCCESS,
		WARNING = FLASH_WARNING,
		INFO = FLASH_INFO;
	public $init, $msg, $code, $type;
	private $style = array(
			FLASH_SUCCESS => array('success', 'ok'),
			FLASH_ERROR => array('danger', 'remove'),
			FLASH_INFO => array('info', 'info-sign'),
			FLASH_WARNING => array('warning', 'warning-sign')
		);


	// This allows the use of setting flash messages within controllers with
	// $response->flash->message(...);
	// 
	// and displaying them within templates with
	// echo $this->flash or echo $this->flash->render(...)
	public function call()
	{
		// alert('hey');
		$this->session = $this->app->session;
		$this->app->view->flash = $this->app->response->flash = $this;

		$this->next->call();
	}

	/**
	 * Display or Create new Flash message [Generic]
	 * 
	 * @param  int  $code Type of Flash Message [Use CONSTANTS above]
	 * @param  string|array  $msg  Message to create
	 * @param  boolean $text Format of message display, as TEXT or as BLOCK
	 * @return boolean  false  If current message is empty
	 */
	public function message($code = null, $msg = null, $text = false)
	{
		$this->session->set('messageCode', FLASH_SUCCESS);
		if($code === null && $msg === null){
			if(!empty($this->msg)){

				if(preg_match('/&#/', $this->msg)){
					$e = explode('&#', $this->msg);
					$a = '<ul class="fa-ul">';
					foreach($e as $m) $a .= '<li><i class="fa-li fa-'.$this->getType(1).' fa-lg"></i>&nbsp;'.$m.'</li>';
					$a .= '</ul>';
				}
				else $a = '<i class="icon-'.$this->getType(1).' fa-lg"></i>&nbsp;'.$this->msg;

				if($this->type or $text === true)
					$style = 'text-'.$this->getType().' text-center" style="padding:.5rem;margin:.5rem;';
				else $style = 'alert alert-'.$this->getType().' center alert-dismissable';
				$html = '<div class="'.$style.'">';
				$html .= '&nbsp;<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
				return $html.$a.'</div>';
			}
			return false;
		}
		else if(is_array($code)) $msg = implode('&#', $code);
		else if(is_string($code)) $msg = $code;
		else $this->session->set('messageCode', $code);

		$this->session->set('message', $msg)->set('messageType', $text);
	}
	public function getType($index = 0)
	{
		return $this->style[$this->code][$index];
	}
	public function hasMessage()
	{
		return $this->session->has('message');
	}

	public function success($message, $uri = null, $text = false)
	{
		return $this->message(FLASH_SUCCESS, $message, $uri, $text);}

	public function error($message, $uri = null, $text = false)
	{
		return $this->message(FLASH_ERROR, $message, $uri, $text);}

	public function info($message, $uri = null, $text = false)
	{
		return $this->message(FLASH_INFO, $message, $uri, $text);}

	public function warning($message, $uri = null, $text = false)
	{
		return $this->message(FLASH_WARNING, $message, $uri, $text);}

	public function append($msg)
	{
		$this->session->set('message', $this->session->get('message').$msg);
	}

	public function render($text = false)
	{
		if($this->hasMessage()){
			$this->msg = $this->session->get('message');
			$this->code = $this->session->get('messageCode');
			$this->type = $this->session->get('messageType', false);
			$this->session->delete('message');
			return $this->message(null,null,$text);
		}
		return '';
	}

	public function __toString()
	{
		return $this->render();
	}

}
