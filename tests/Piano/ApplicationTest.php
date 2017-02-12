<?php

use Piano\Application;
use Piano\Container;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    private $class;

    public function setUp()
    {
        $_SERVER['REQUEST_URI'] = '/';

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

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Param url is expected.
     */
    public function testRedirectMustThrowAnInvalidArgumentExceptionWhenNoParametersArePassed()
    {
        $this->assertTrue(
            method_exists($this->class, 'redirect'),
            'Method "redirect()" must exist'
        );

        $this->class->redirect();
    }

    public function testItMustRedirectToUrlWithSearchEngineFriendlyEnabledAndNoParameters()
    {
        $_SERVER['HTTP_HOST'] = 'localhost';

        $di = $this->getTestingContainer($sef = true);
        $class = $this->getMockBuilder('Piano\Application')
            ->setConstructorArgs([$di])
            ->setMethods(['header'])
            ->getMock();

        $class->expects($this->once())
            ->method('header')
            ->with($this->identicalTo('Location: //localhost/admin'));

        $class->redirect('defaultAdmin');
    }

    /**
     * @expectedException Exception
     */
    public function testItMustRedirectToDefaultUrlWithSearchEngineFriendlyEnabledAndWrongParameterNames()
    {
        $di = $this->getTestingContainer($sef = true);
        $class = $this->getMockBuilder('Piano\Application')
            ->setConstructorArgs([$di])
            ->setMethods(['header'])
            ->getMock();

        $params = ['_id' => 5];
        $class->redirect('userEdit', $params);
    }

    public function testItMustRedirectToUrlWithSearchEngineFriendlyEnabledAndParameters()
    {
        $_SERVER['HTTP_HOST'] = 'localhost';

        $di = $this->getTestingContainer($sef = true);
        $class = $this->getMockBuilder('Piano\Application')
            ->setConstructorArgs([$di])
            ->setMethods(['header'])
            ->getMock();

        $class->expects($this->once())
            ->method('header')
            ->with($this->identicalTo('Location: //localhost/users/5'));

        $params = ['id' => 5];
        $class->redirect('userEdit', $params);
    }

    public function testItMustRedirectToUrlWithSearchEngineFriendlyDisabledAndNoParameters()
    {
        $_SERVER['HTTP_HOST'] = 'localhost';

        $di = $this->getTestingContainer($sef = false);
        $class = $this->getMockBuilder('Piano\Application')
            ->setConstructorArgs([$di])
            ->setMethods(['header'])
            ->getMock();

        $class->expects($this->once())
            ->method('header')
            ->with($this->identicalTo('Location: //localhost/admin/index/index'));

        $class->redirect('defaultAdmin');
    }

    public function testItMustRedirectToUrlWithSearchEngineFriendlyDisabledAndParameters()
    {
        $_SERVER['HTTP_HOST'] = 'localhost';

        $di = $this->getTestingContainer($sef = false);
        $class = $this->getMockBuilder('Piano\Application')
            ->setConstructorArgs([$di])
            ->setMethods(['header'])
            ->getMock();

        $class->expects($this->once())
            ->method('header')
            ->with($this->identicalTo('Location: //localhost/application/user/edit/id/5'));

        $params = ['id' => 5];
        $class->redirect('userEdit', $params);
    }

    /**
     * @expectedException Exception
     */
    public function testItMustThrowAnExceptionWhenSearchEngineFriendlyDisabledAndNullParameterValues()
    {
        $di = $this->getTestingContainer($sef = false);
        $class = $this->getMockBuilder('Piano\Application')
            ->setConstructorArgs([$di])
            ->setMethods(['header'])
            ->getMock();

        $params = ['id' => null];
        $class->redirect('userEdit', $params);
    }

    public function testItMustRunTheApplication()
    {
        $_SERVER['REQUEST_URI'] = '/';

        $di = $this->getTestingContainer();
        $class = new Application($di);

        $this->assertTrue(
            method_exists($class, 'run'),
            'Method "run()" must exist'
        );
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
