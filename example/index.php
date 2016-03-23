<?php

include_once __DIR__.'/vendor/autoload.php';

$app = new Push\Application();

// $app->uses(new \Push\Middlewares\Session);
// $app->uses(new \Push\Middlewares\Flash);
// $app->uses(new \Push\Middlewares\Database);

// Hello world from Hello controller
$app->router->any('/hello/:$input', 'Hello@index');

// Hello world from Callback
$app->router->get('/:$input', function($req, $res){
  $content = '<h1>Hello, '.$req['input'].'!</h1>';
  $content .= '<h2>from Route callback..</h2>';
  $content .= '<a href="hello/world">Goto Hello Controller</a>';

  $res->write($content);
});

$app->run();
