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

    public function testItMustSetupTheModuleControllerActionWhenSearchEngineFriendlyIsEnabledAndNotInformedUrl()
    {
        $di = $this->getTestingContainer($sef = true);
        $class = new Application($di);

        $this->assertTrue(
            method_exists($class, 'getModuleName'),
            'Method "getModuleName()" must exist'
        );

        $this->assertTrue(
            method_exists($class, 'getControllerName'),
            'Method "getControllerName()" must exist'
        );

        $this->assertTrue(
            method_exists($class, 'getActionName'),
            'Method "getActionName()" must exist'
        );

        $this->assertTrue(
            method_exists($class, 'setUrl'),
            'Method "setUrl()" must exist'
        );

        $_SERVER['REQUEST_URI'] = 'http://thatstest.com/route/doesnot/exit';
        $class->setUrl();
        $this->assertEquals('application', $class->getModuleName());
        $this->assertEquals('ErrorController', $class->getControllerName());
        $this->assertEquals('error', $class->getActionName());
        $this->assertInternalType('array', $class->getParams());
        $this->assertEmpty($class->getParams(), 'Parameter must be an empty array');
    }

    public function testItMustSetupTheNotFoundRouteWhenSearchEngineFriendlyIsEnabledAndUrlDoesNotExist()
    {
        $di = $this->getTestingContainer($sef = true);
        $class = new Application($di);

        $this->assertTrue(
            method_exists($class, 'getModuleName'),
            'Method "getModuleName()" must exist'
        );

        $this->assertTrue(
            method_exists($class, 'getControllerName'),
            'Method "getControllerName()" must exist'
        );

        $this->assertTrue(
            method_exists($class, 'getActionName'),
            'Method "getActionName()" must exist'
        );

        $this->assertTrue(
            method_exists($class, 'setUrl'),
            'Method "setUrl()" must exist'
        );

        $class->setUrl('/route/doesnot/exit');
        $this->assertEquals('application', $class->getModuleName());
        $this->assertEquals('ErrorController', $class->getControllerName());
        $this->assertEquals('error', $class->getActionName());
        $this->assertInternalType('array', $class->getParams());
        $this->assertEmpty($class->getParams(), 'Parameter must be an empty array');
    }

    public function testItMustSetupTheModuleControllerActionWhenSearchEngineFriendlyIsEnabledAndUrlIsRoot()
    {
        $di = $this->getTestingContainer($sef = true);
        $class = new Application($di);

        $this->assertTrue(
            method_exists($class, 'getModuleName'),
            'Method "getModuleName()" must exist'
        );

        $this->assertTrue(
            method_exists($class, 'getControllerName'),
            'Method "getControllerName()" must exist'
        );

        $this->assertTrue(
            method_exists($class, 'getActionName'),
            'Method "getActionName()" must exist'
        );

        $this->assertTrue(
            method_exists($class, 'setUrl'),
            'Method "setUrl()" must exist'
        );

        $class->setUrl('/');
        $this->assertEquals('application', $class->getModuleName());
        $this->assertEquals('IndexController', $class->getControllerName());
        $this->assertEquals('index', $class->getActionName());
        $this->assertInternalType('array', $class->getParams());
        $this->assertEmpty($class->getParams(), 'Parameter must be an empty array');
    }

    public function testItMustSetupTheModuleControllerActionWhenSearchEngineFriendlyIsDisabledAndUrlHasNoParameters()
    {
        $di = $this->getTestingContainer($sef = false);
        $class = new Application($di);

        $this->assertTrue(
            method_exists($class, 'getModuleName'),
            'Method "getModuleName()" must exist'
        );

        $this->assertTrue(
            method_exists($class, 'getControllerName'),
            'Method "getControllerName()" must exist'
        );

        $this->assertTrue(
            method_exists($class, 'getActionName'),
            'Method "getActionName()" must exist'
        );

        $this->assertTrue(
            method_exists($class, 'setUrl'),
            'Method "setUrl()" must exist'
        );

        $class->setUrl('/admin');
        $this->assertEquals('admin', $class->getModuleName());
        $this->assertEquals('IndexController', $class->getControllerName());
        $this->assertEquals('index', $class->getActionName());
        $this->assertInternalType('array', $class->getParams());
        $this->assertEmpty($class->getParams(), 'Parameter must be an empty array');
    }

    public function testItMustSetupTheModuleControllerActionWhenSearchEngineFriendlyIsDisabledAndUrlHasParametersOnIt()
    {
        $di = $this->getTestingContainer($sef = false);
        $class = new Application($di);

        $this->assertTrue(
            method_exists($class, 'getModuleName'),
            'Method "getModuleName()" must exist'
        );

        $this->assertTrue(
            method_exists($class, 'getControllerName'),
            'Method "getControllerName()" must exist'
        );

        $this->assertTrue(
            method_exists($class, 'getActionName'),
            'Method "getActionName()" must exist'
        );

        $this->assertTrue(
            method_exists($class, 'setUrl'),
            'Method "setUrl()" must exist'
        );

        $class->setUrl('/users/1');
        $this->assertEquals('application', $class->getModuleName());
        $this->assertEquals('UserController', $class->getControllerName());
        $this->assertEquals('edit', $class->getActionName());
        $params = $class->getParams();
        $this->assertInternalType('array', $params, 'Parameter must be an array');
        $this->assertArrayHasKey('id', $params);
        $this->assertEquals(1, $params['id']);
    }

    private function getTestingContainer($searchEngineFriendly = true)
    {
        $container = new Container();
        $container['config'] = function () {
            return new \Piano\Config\Ini('tests/configTest.ini');
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
