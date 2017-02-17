<?php

use SON\view\ViewRenderer;
use Silex\Application;


require __DIR__ . '/../vendor/autoload.php';


$app = new Silex\Application();

$app['debug'] = true;

$app['view.config'] = [
	'path_templates' => __DIR__ . '/../templates'
];

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
	'db.options' => array(
		'driver' => 'pdo_mysql',
		'host' => 'localhost',
		'dbname' => 'son_silex_basico',
		'user' => 'root',
		'password' => 'dev'
	)
));


$app['view.renderer'] = function() use ($app) {
	$pathTemplates = $app['view.config']['path_templates'];
	return new ViewRenderer($pathTemplates);
};


$app->get('/create-table', function(Application $app) {
	
	$file = fopen(__DIR__. '/../data/schema.sql', 'r');
	
	while ($line = fread($file, 4096)) {
		$app['db']->executeQuery($line);
	}
	
	fclose($file);
	
	return "Tabelas criadas";
});


$post = include __DIR__ . '/controllers/posts.php';
$app->mount('/posts', $post);
	

$app->error(function(\Exception $e, Request $request, $code) use($app) {
	
	switch ($code) {
		case 404:
			return $app['view.renderer']->render('errors/404', [
				'message'=>$e->getMessage()
			]);
			break;
	}
	
});
	

$app->run();