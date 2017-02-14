<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (getenv('APPLICATION_ENV') == 'development') {
    ini_set('display_errors', 1);
    error_reporting(-1);
}

$di = new \Piano\Container();
$di['config'] = function () {
    return new \Piano\Config\Ini(__DIR__ . '/../src/app/config/config.ini');
};

$di['router'] = function () {
    $routes = [
        'default' => [
            'route' => '/',
            'module' => 'application',
            'controller' => 'index',
            'action' => 'index',
        ],
    ];

    return (new \Piano\Router())
        ->setRoutes($routes)
        ->enableSearchEngineFriendly(false);
};

$di['modulesLayout'] = function () {
    return [
        'base' => [
            'application',
        ]
    ];
};

$app = new \Piano\Application($di);
$app->run();
