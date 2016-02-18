<?php  

namespace App\Controller;

/**
* Hello Controller
*/
class Hello extends \Push\Controller
{
	
	function index($request, $response)
	{
		$index = $this->model('index');

		$response->render('index');
	}
}