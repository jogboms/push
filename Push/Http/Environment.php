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

Class Environment extends \Push\Utils\Collection 
{
	
	function __construct(\Push\Utils\Collection $config, $request)
	{
		$env = [];

		$env['APP_NAME'] = $config['APP']->get('name', $config['PUSH']['name']);
		$env['APP_FULLNAME'] = $config['APP']->get('fullname', $config['PUSH']['fullname']);
		$env['APP_DESC'] = $config['APP']->get('description', $config['PUSH']['description']);
		$env['APP_TIMEZONE'] = $config['APP']['timezone'];
		$env['APP_AUTHOR'] = $config['APP']['author'];
		$env['APP_VERSION'] = $config['APP']->get('version', $config['PUSH']['version']);
		$env['APP_MODE'] = $config['APP']['mode'];
		$env['APP_DATECREATED'] = $config['APP']->get('created', $config['PUSH']['created']);
		$env['APP_DEBUG'] = !!$config['APP']['debug'];
		$env['APP_LIVE'] = !!$config['APP']['online'];
		$env['APP_DIR'] = str_replace('/', DIRECTORY_SEPARATOR, $config['APP']['app_dir']);
		$env['APP_LIBRARY_DIR'] = str_replace('/', DIRECTORY_SEPARATOR, $config['APP']['library_dir']);
		$env['APP_STORAGE_DIR'] = str_replace('/', DIRECTORY_SEPARATOR, $config['APP']['storage_dir']);
		$env['APP_SECURE'] = !(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
		$env['APP_DEFAULT_CONTROLLER'] = $config['APP']->get('default_controller', 'index');
		$env['APP_DEFAULT_ACTION'] = $config['APP']->get('default_action', 'index');
		$env['APP_TEMPLATE_DIR'] = str_replace('/', DIRECTORY_SEPARATOR, $config['APP']['templates_dir']);
		$env['APP_ASSET_DIR'] = $config['APP']['assets_dir'];
		$env['APP_UPLOADS_DIR'] = $config['APP']['uploads_dir'];
		$env['APP_USE_MVC'] = !!$config['APP']['use_mvc_routes'];

		$env['APP_USE_DEBUGGER'] = !!$config['APP']['use_debugger'];

		$env['STRICT_DEBUG'] = !!$config['APP']['strict'];

		$env['METHOD'] = $request->getMethod();
		$env['URI'] = $request->url();
		$env['URI_SELF'] = $env['URI'].'/?'.$request->server('QUERY_STRING');
		$env['URI_REFERER'] = $request->server('HTTP_REFERER ', false);

		$env['FILENAME'] = basename($request->server('SCRIPT_FILENAME'));
		$env['IP'] = $request->getIp();
		$env['LICENSE'] = '<small style="color:#999;">Powered by <a target="_" href="'.$config['PUSH']['link'].'" title="'.$config['PUSH']['description'].'">'.$config['PUSH']['name'].'</a> v'.$config['PUSH']['version'].'</small>';

		// App Controller, App Action & App Parametes
		$env['__CONTROLLER__'] = null;
		$env['__ACTION__'] = null;
		$env['__PARAMS__'] = null;

		// DEFAULT TIMEZONE
		if(!date_default_timezone_get('date.timezone'))
			date_default_timezone_set($env['APP_TIMEZONE']);

		parent::__construct($env);
	}

}
