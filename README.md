# Push PHP MVC Framework

A Minimal PHP platform for rapid web application development.

## Installation

It's most recommended to utilize [Composer](https://getcomposer.org/) for installation.

```bash
$ composer require push/push "@dev"
```

This installs Push and it requires PHP 5.5.0 or newer.

## Usage

Create an index.php file with the following contents:

```php
<?php

include_once __DIR__.'/vendor/autoload.php';

$app = new Push\Application();

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

```

The rest of the Application's configurations and structure is described in th example's directory.

You may quickly test this using the built-in PHP server:
```bash
$ php -S localhost:3000
```

Go to http://localhost:3000 to see Push framework in action.

## Credits

- [Jeremiah Ogbomo](https://github.com/jogboms)

## License

The Push Framework is licensed under the MIT license. See [License File](LICENSE.md) for more information.



