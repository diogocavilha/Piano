<?php

use Piano\Mvc\View;
use Piano\Mvc\Controller;
use Piano\Application;

class ControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    public function setUp()
    {
        $_SERVER['REQUEST_URI'] = '/';

        $di = $this->getTestingContainer($sef = true);
        $app = new Application($di);

        $this->controller = new Controller($app);
    }

    public function testItCanReturnTheDiContainer()
    {
        $this->assertTrue(
            method_exists($this->controller, 'getDi'),
            'Method "getDi()" must exist'
        );

        $this->assertInstanceOf(
            'Piano\Container',
            $this->controller->getDi()
        );
    }

    public function testItMustGetUrlParams()
    {
        $this->assertTrue(
            method_exists($this->controller, 'getParams'),
            'Method "getParams()" must exist'
        );

        $this->assertInternalType(
            'array',
            $this->controller->getParams()
        );
    }

    public function testItMustGetUrlParamByName()
    {
        $_SERVER['REQUEST_URI'] = '/';

        $di = $this->getTestingContainer($sef = false);
        $app = new Application($di);
        $app->setUrl('/application/user/edit/id/5');
        $controller = new Controller($app);

        $this->assertTrue(
            method_exists($controller, 'getParam'),
            'Method "getParam()" must exist'
        );

        $this->assertEquals('5', $controller->getParam('id'));
    }

    private function getTestingContainer($searchEngineFriendly = true)
    {
        $container = new Piano\Container();
        $container['config'] = function () {
            return new Piano\Config\Ini('tests/configTest.ini');
        };

        $container['router'] = function () use ($searchEngineFriendly) {
            $routes = [
                'default' => [
                    'route' => '/',
                    'module' => 'application',
                    'controller' => 'index',
                    'action' => 'index'
                ],
                'defaultAdmin' => [
                    'route' => '/admin',
                    'module' => 'admin',
                    'controller' => 'index',
                    'action' => 'index'
                ],
                'userEdit' => [
                    'route' => '/users/:id',
                    'module' => 'application',
                    'controller' => 'user',
                    'action' => 'edit',
                    [
                        ':id' => '\d+'
                    ]
                ],
                'error404' => [
                    'route' => '/error',
                    'module' => 'application',
                    'controller' => 'error',
                    'action' => 'error',
                ],
                'redirect' => [
                    'route' => '/redirect/contact',
                    'module' => 'application',
                    'controller' => 'index',
                    'action' => 'redirectTest',
                ]
            ];

            $router = new Piano\Router();
            $router->setRoutes($routes);
            $router->enableSearchEngineFriendly($searchEngineFriendly);

            return $router;
        };

        return $container;
    }
}
