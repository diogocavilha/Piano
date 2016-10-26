<?php

use Piano\Application2 as Application;
use Piano\Container;

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
        $container = new Container();

        new Application($container);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Key "router" is missing
     */
    public function testItMustThrowRuntimeExceptionWhenContainerDoesNotHaveARouterKey()
    {
        $container = new Container();
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

        $this->assertInstanceOf('\Piano\Container', $container);
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

    public function testGetDefaultModuleNameMustReturnTheSetupDefaultModuleName()
    {
        $this->assertTrue(
            method_exists($this->class, 'getDefaultModuleName'),
            'Method "getDefaultModuleName()" must exist'
        );

        $this->assertEquals('authentication', $this->class->getDefaultModuleName());
    }

    public function testItMustReturnTheModuleName()
    {
        $this->assertTrue(
            method_exists($this->class, 'getModuleName'),
            'Method "getModuleName()" must exist'
        );

        $this->assertTrue(
            method_exists($this->class, 'setUrl'),
            'Method "setUrl()" must exist'
        );

        $this->class->setUrl('/admin/index/index');
        $this->assertEquals('admin', $this->class->getModuleName());

        $this->class->setUrl('/upload/index/index');
        $this->assertEquals('upload', $this->class->getModuleName());
    }

    private function getTestingContainer()
    {
        $container = new Container();
        $container['config'] = function () {
            return new \Piano\Config\Ini('tests/configTest.ini');
        };

        $container['router'] = function () {
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

        return $container;
    }
}
