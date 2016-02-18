<?php

include_once __DIR__.'/vendor/autoload.php';

$app = new Push\Application();

// $app->uses(new \Push\Middlewares\Session);
// $app->uses(new \Push\Middlewares\Flash);
// $app->uses(new \Push\Middlewares\Database);

// Hello world from Hello controller
$app->router->get('/hello/:$input', 'Hello@index');

// Hello world from Callback
$app->router->get('/:$input', function($req, $res){
	$content = '<h1>Hello, '.$req['input'].'</h1>';
	$content .= '<h2>From a callback..<a href="hello/world">Hello World</a></h2>';

	$res->write($content);
});

$app->run();
