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

namespace Push\Http;

Class Uri 
{

	public function __construct()
	{
		$isSecure = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : null;
		$scheme = (is_null($isSecure) || $isSecure === 'off') ? 'http' : 'https';

		$_h = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
		$h = explode(':', $_h);
		$port = isset($h[1]) ? $h[1] : $_SERVER['SERVER_PORT'];
		$host = trim($h[0].':'.$port, '/');
		$ru = $_SERVER['REQUEST_URI'];
		$su = dirname($_SERVER['SCRIPT_NAME']);
		// alert($ru, $su);
		// $uri = str_replace($su, '', $ru);
		$uri = $ru;
		$uri = trim($uri, '/');

		$u = parse_url($scheme.'://'.$host.'/'.$uri);

		// alert($u);
		$this->scheme = isset($u['scheme']) ? $u['scheme'] : '';
		$this->host = isset($u['host']) ? $u['host'] : '';
		$this->port = isset($u['port']) ? $u['port'] : null;
		$this->path = isset($u['path']) ? $u['path'] : '';
		$this->query = isset($u['query']) ? $u['query'] : '';
		$this->fragment = isset($u['fragment']) ? $u['fragment'] : '';
		$this->user = isset($u['user']) ? $u['user'] : '';
		$this->pass = isset($u['pass']) ? $u['pass'] : '';
	}

	public function getScheme()
	{
		return $this->scheme;
	}
	public function getHost()
	{
		return $this->host;
	}
	public function getPort()
	{
		return $this->port;
	}
	public function getPath()
	{
		return $this->path;
	}
	public function getQuery()
	{
		return $this->query;
	}
	public function getFragment()
	{
		return $this->fragment;
	}

	function __toString()
	{
		return $this->getScheme().'://'.$this->getHost().$this->getPath();
	}
}
	