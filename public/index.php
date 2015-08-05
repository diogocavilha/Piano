<?php

require_once '../vendor/autoload.php';
require_once 'routes.php';

if (getenv('APPLICATION_ENV') == 'development') {
    ini_set('display_errors', 1);
    error_reporting(-1);
}

$layoutPerModule = [
    'base' => [
        'application',
    ]
];

$config = new \Piano\Config();
$config->setApplicationFolder('app')
    ->setDefaultModule('application')
    ->setLayoutPerModule($layoutPerModule);

$router = new \Piano\Router();
$router->setRoutes($routes)
    ->enableSearchEngineFriendly(true);

$app = new \Piano\Application($config, $router);
$app->run();
