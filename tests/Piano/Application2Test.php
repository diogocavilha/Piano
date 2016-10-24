<?php

use Piano\Application2 as Application;
use Piano\Di;

/**
 * @group php7
 */
class Application2Test extends PHPUnit_Framework_TestCase
{
    private $class;

    public function setUp()
    {
        $di = $this->getTestingContainer();

        $this->class = new Application($di);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Key "config" is missing
     */
    public function testItMustThrowRuntimeExceptionWhenContainerDoesNotHaveAConfigKey()
    {
        $container = new \Piano\Di();

        new Application($container);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Key "router" is missing
     */
    public function testItMustThrowRuntimeExceptionWhenContainerDoesNotHaveARouterKey()
    {
        $container = new \Piano\Di();
        $container['config'] = function () {};

        new Application($container);
    }

    public function testItMustSetTheDIContainerOnConstructor()
    {
        $this->assertTrue(
            method_exists($this->class, 'getDi'),
            'Method "getDi()" must exist'
        );

        $container = $this->class->getDi();

        $this->assertInstanceOf('\Piano\Di', $container);
        $this->assertInstanceOf('\Pimple\Container', $container);
    }

    public function testGetApplicationFolderNameMustReturnTheSetupFolderName()
    {
        $this->assertTrue(
            method_exists($this->class, 'getApplicationFolderName'),
            'Method "getApplicationFolderName()" must exist'
        );

        $this->assertEquals('Piano', $this->class->getApplicationFolderName());
    }

    private function getTestingContainer()
    {
        $di = new \Piano\Di();
        $di['config'] = function () {
            return new \Piano\Config\Ini('tests/configTest.ini');
        };

        $di['router'] = function () {
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
                'user_edit' => [
                    'route' => '/users/:id',
                    'module' => 'application',
                    'controller' => 'user',
                    'action' => 'edit',
                    [
                        ':id' => '\d+'
                    ]
                ],
                'error_404' => [
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

            return $router;
        };

        return $di;
    }
}
