<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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



$app->get('/', function() {
	return "Minha primeira aplicação com silex.";
});

$app->get('/home', function() use($app) {
	//Função de debu do symfony/var-dumper
	//dump($app);
	return $app['view.renderer']->render('home');
});


$app->get('/create-table', function(Application $app) {
	
	$file = fopen(__DIR__. '/../data/schema.sql', 'r');
	
	while ($line = fread($file, 4096)) {
		$app['db']->executeQuery($line);
	}
	
	fclose($file);
	
	return "Tabelas criadas";
});
	
$app->get('/posts/create', function() use ($app) {
	return $app['view.renderer']->render('posts/create');
});


$app->post('/posts/create', function(Request $request) use($app) {
	
	/** @var Doctrine\DBAL\Connection $db */
	$db = $app['db'];
	$data = $request->request->all();

	
	$db->insert('posts', [
		'title'=>$data['title'],
		'content'=>$data['content'],
	]);
	
	return $app->redirect('/posts');
	
});

$app->get('/posts', function(Request $request) use($app) {

	/** @var Doctrine\DBAL\Connection $db */
	$db = $app['db'];
	$sql = "SELECT * FROM posts;";
	$posts = $db->fetchAll($sql);

	return $app['view.renderer']->render('posts/list', [
		'posts'=>$posts
	]);

});
	

$app->get('/posts/edit/{id}', function($id) use($app) {
	
	/** @var Doctrine\DBAL\Connection $db */
	$db = $app['db'];
	$sql = "SELECT * FROM posts WHERE id = ?;";
	
	$post = $db->fetchAssoc($sql, [$id]);
	
	if(!$post) {
		$app->abort(404, 'Post não encontrado!');
	}
	
	return $app['view.renderer']->render('posts/edit', ['post' => $post]);
});

$app->post('/posts/edit/{id}', function(Request $request, $id) use($app) {

	/** @var Doctrine\DBAL\Connection $db */
	$db = $app['db'];

	$sql = "SELECT * FROM posts WHERE id = ?;";
	
	$post = $db->fetchAssoc($sql, [$id]);
	
	if(!$post) {
		$app->abort(404, 'Post não encontrado!');
	}
	
	
	$data = $request->request->all();
	
	$db->update('posts', [
		'title'=>$data['title'],
		'content'=>$data['content'],
	], ['id' => $id]);

	return $app->redirect('/posts');

});
	

$app->get('/posts/delete/{id}', function($id) use($app) {

	/** @var Doctrine\DBAL\Connection $db */
	$db = $app['db'];

	$sql = "SELECT * FROM posts WHERE id = ?;";
	
	$post = $db->fetchAssoc($sql, [$id]);
	
	if(!$post) {
		$app->abort(404, 'Post não encontrado!');
	}
	
	
	$db->delete('posts', ['id'=>$id]);

	return $app->redirect('/posts');

});


$app->error(function(\Exception $e, Request $request, $code) use($app) {
	
	switch ($code) {
		case 404:
			return $app['view.renderer']->render('errors/404', [
				'message'=>$e->getMessage()
			]);
			break;
	}
	
});


$app->post('/get-name/{param1}', function(Request $request, $param1) use ($app) {
	
	$name = $request->get('name', 'Não informado');
	
	return $app['view.renderer']->render('get-name', [
		'name' => $name,
		'param1' => $param1
	]);
	
});
	

$app->run();