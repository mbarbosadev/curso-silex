<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/', function() {
	return "Minha primeira aplicação com silex.";
});

$app->get('/home', function() {
	
	ob_start();
	include __DIR__ . '/../templates/home.phtml';
	$saida = ob_get_clean();
	
	return $saida;
});


$app->post('/get-name/{param1}', function(Request $request, $param1) {
	
	$name = $request->get('name', 'Não informado');
	
	ob_start();
	include __DIR__ . '/../templates/get-name.phtml';
	$saida = ob_get_clean();
	
	return $saida;
});
	

$app->run();