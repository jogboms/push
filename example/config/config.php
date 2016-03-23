<?php
/**
 * New Application-specific configuration parameters could be added here
 * e.g
 * ```
 * 'New_Param_Name' => 'My new config parameter value',
 * ```
 */
return [
	/**
	* Site Configuration variables
	*/
	'APP' => [
		'name' => 'Push',
		'fullname' => 'Push PHP',
		'description' => 'A Minimal PHP-driven platform for rapid web application development',
		'author' => 'Jogboms',
		'version' => '0.0.1',
		'created' => 2015,

		'online' => true,
		// 'debug' => false,
		'debug' => true,
		// 'mode' => \Push\Application::PRODUCTION,
		'mode' => \Push\Application::DEVELOPMENT,
		'strict' => true,

		'use_mvc_routes' => true,
		// 'use_debugger' => false,
		'use_debugger' => true,

    'app_dir' => '/App',
    'app_model_dir' => '/App/Model',
		'app_controller_dir' => '/App/Controller',
    'app_view_dir' => '/App/View',
		'app_includes_dir' => '/App/includes',
    'app_public_dir' => '/www',
		'storage_dir' => '/storage',
		'library_dir' => '/library',
		'assets_dir' => '/www/assets',
		'uploads_dir' => '/www/uploads',

		'timezone' => 'AFRICA/LAGOS',

		'default_controller' => 'index',
		'default_action' => 'index',
	],
	'database' => [
		\Push\Application::DEVELOPMENT => [
		   	'host' => 'localhost',
		   	'username' => 'root',
		   	'password' => '',
		   	'dbname' => 'db_name_local',
		   	'engine' => 'mysqli',
		   	'db_prefix' => 'db_name_',
		   	'debug' => true
		],
		\Push\Application::PRODUCTION => [
		   	'host' => 'localhost',
		   	'username' => 'root',
		   	'password' => '',
		   	'dbname' => 'db_name_prod',
		   	'engine' => 'mysqli',
		   	'db_prefix' => 'db_name_prod_',
		   	'debug' => false
		],
	],

	'mail' => [
	   	'SMTPhost' => 'mail.push.com',
	   	'SMTPusername' => 'info@push.com',
	   	'SMTPpassword' => 'xxxxxxxx',
	   	'SMTPport' => 25,
	   	'SMTPsecure' => 'tsl',
	   	'SMTPauth' => true
	],
];
