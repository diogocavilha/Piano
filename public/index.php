<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/routes.php';

if (getenv('APPLICATION_ENV') == 'development') {
    ini_set('display_errors', 1);
    error_reporting(-1);
}

$layoutPerModule = [
    'base' => [
        'application',
    ]
];

$config = new \Piano\Config\Ini(
    __DIR__ . '/../src/app/config/config.ini'
);

$router = new \Piano\Router();
$router->setRoutes($routes)
    ->enableSearchEngineFriendly(true);

$app = new \Piano\Application($config, $router);
$app->registerModulesLayout($layoutPerModule);
$app->run();
