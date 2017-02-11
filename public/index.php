<?php

require __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/', function() {
	return "Minha primeira aplicação com silex.";
});

$app->run();