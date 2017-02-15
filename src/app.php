<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SON\view\ViewRenderer;

require __DIR__ . '/../vendor/autoload.php';


$app = new Silex\Application();

$app['debug'] = true;

$app['view.config'] = [
	'path_templates' => __DIR__ . '/../templates'
];

$app['view.renderer'] = function() use ($app) {
	$pathTemplates = $app['view.config']['path_templates'];
	return new ViewRenderer($pathTemplates);
};



$app->get('/', function() {
	return "Minha primeira aplicação com silex.";
});

$app->get('/home', function() use($app) {
	//Função de debu do symfony/var-dumper
	//dump($app);
	return $app['view.renderer']->render('home');
});


$app->post('/get-name/{param1}', function(Request $request, $param1) use ($app) {
	
	$name = $request->get('name', 'Não informado');
	
	return $app['view.renderer']->render('get-name', [
		'name' => $name,
		'param1' => $param1
	]);
	
});
	

$app->run();