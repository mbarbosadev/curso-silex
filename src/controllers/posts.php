<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

$post = $app['controllers_factory'];
	

$post->get('/create', function() use ($app) {
	return $app['twig']->render('posts/create.html.twig', [
		'post'=>[
			'title'=>'',
			'content'=>''
		]
	]);
});


$constraintsPost = new Assert\Collection(array(
	'title'=>new Assert\NotBlank([
			'message'=>'Campo título é obrigatório.',
			'payload'=>['field'=>'title']

	]),
	'content'=>new Assert\NotBlank([
			'message'=>'Campo conteúdo é obrigatório',
			'payload'=>['field'=>'content']
	])
));
	


$post->post('/create', function(Request $request) use($app, $constraintsPost) {
	
	/** @var Doctrine\DBAL\Connection $db */
	$db = $app['db'];
	$data = $request->request->all();
	
	
	$errors = $app['validator']->validate($data, $constraintsPost);
	
	if(count($errors) > 0) {
		$fieldsError = [];
		
		foreach ($errors as $err) {
			$fieldsError[] = $err->getConstraint()->payload['field'];
		}
		
		return $app['twig']->render('posts/create.html.twig', [
			'errors'=>$errors,
			'post'=>[
				'title'=>$data['title'],
				'content'=>$data['content']
			],
			'fieldsError'=>$fieldsError
		]);
		
	}
	
	$db->insert('posts', [
		'title'=>$data['title'],
		'content'=>$data['content'],
	]);
	
	$app['session']->getFlashBag()->add('message', 'Post cadastrado com sucesso!');
	
	return $app->redirect('/admin/posts');
	
	
});

$post->get('/', function(Request $request) use($app) {
	
	/** @var Doctrine\DBAL\Connection $db */
	$db = $app['db'];
	$sql = "SELECT * FROM posts;";
	$posts = $db->fetchAll($sql);

	return $app['twig']->render('posts/list.html.twig', [
		'posts'=>$posts
	]);

});
	

$post->get('/edit/{id}', function($id) use($app) {
	
	/** @var Doctrine\DBAL\Connection $db */
	$db = $app['db'];
	$sql = "SELECT * FROM posts WHERE id = ?;";
	
	$post = $db->fetchAssoc($sql, [$id]);
	
	
	if(!$post) {
		$app->abort(404, 'Post não encontrado!');
	}

	return $app['twig']->render('posts/edit.html.twig', ['post' => $post]);
});


$post->post('/edit/{id}', function(Request $request, $id) use($app, $constraintsPost) {
	
	/** @var Doctrine\DBAL\Connection $db */
	$db = $app['db'];

	$sql = "SELECT * FROM posts WHERE id = ?;";
	
	$post = $db->fetchAssoc($sql, [$id]);
	
	if(!$post) {
		$app->abort(404, 'Post não encontrado!');
	}
	
	
	$data = $request->request->all();
	
	
	
	$errors = $app['validator']->validate($data, $constraintsPost);
	
	if(count($errors) > 0) {
		$fieldsError = [];
	
		foreach ($errors as $err) {
			$fieldsError[] = $err->getConstraint()->payload['field'];
		}
	
		return $app['twig']->render('posts/create.html.twig', [
				'errors'=>$errors,
				'post'=>[
						'title'=>$data['title'],
						'content'=>$data['content']
				],
				'fieldsError'=>$fieldsError
		]);
	
	}
	
	
	$db->update('posts', [
		'title'=>$data['title'],
		'content'=>$data['content'],
	], ['id' => $id]);

	$app['session']->getFlashBag()->add('message', 'Post atualizado com sucesso!');
	
	return $app->redirect('/admin/posts');

});
	


$post->get('/delete/{id}', function($id) use($app) {

	/** @var Doctrine\DBAL\Connection $db */
	$db = $app['db'];

	$sql = "SELECT * FROM posts WHERE id = ?;";
	
	$post = $db->fetchAssoc($sql, [$id]);
	
	if(!$post) {
		$app->abort(404, 'Post não encontrado!');
	}
	
	
	$db->delete('posts', ['id'=>$id]);

	
	$app['session']->getFlashBag()->add('message', 'Post excluído com sucesso!');
	
	
	return $app->redirect('/admin/posts');

});


return $post;