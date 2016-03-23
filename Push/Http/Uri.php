<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
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

    // $su = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
    $su = str_replace(DIRECTORY_SEPARATOR, '/', rtrim(dirname($_SERVER['SCRIPT_NAME']), DIRECTORY_SEPARATOR));

    $uri = $ru;
    $uri = trim($uri, '/');

    $u = parse_url($scheme.'://'.$host.'/'.$uri);

    // alert($u);
    $this->base = $su;
    $this->scheme = isset($u['scheme']) ? $u['scheme'] : '';
    $this->host = isset($u['host']) ? $u['host'] : '';
    $this->port = isset($u['port']) ? $u['port'] : null;
    $this->path = isset($u['path']) ? $u['path'] : '';
    $this->query = isset($u['query']) ? $u['query'] : '';
    $this->fragment = isset($u['fragment']) ? $u['fragment'] : '';
    $this->user = isset($u['user']) ? $u['user'] : '';
    $this->pass = isset($u['pass']) ? $u['pass'] : '';
  }

  public function getBasePath()
  {
    return $this->base;
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

