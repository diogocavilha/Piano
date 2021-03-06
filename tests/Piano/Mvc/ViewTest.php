<?php

use Piano\Mvc\View;
use Piano\Application;

class ViewTest extends PHPUnit_Framework_TestCase
{
    private $view;

    public function setUp()
    {
        $_SERVER['REQUEST_URI'] = '/';

        $di = $this->getTestingContainer($sef = true);
        $app = new Application($di);
        $app->setUrl('/');
        $this->view = new View($app);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A route name is expected.
     */
    public function testUrlShouldThrowAnInvalidArgumentException()
    {
        $this->view->url();
    }

    public function testUrlShouldWork()
    {
        $_SERVER['REQUEST_URI'] = '/';

        $di = $this->getTestingContainer($sef = true);
        $app = new Application($di);
        $view = new View($app);

        $this->assertEquals(
            '/',
            $view->url('default')
        );

        $this->assertEquals(
            '/admin',
            $view->url('defaultAdmin')
        );

        $di = $this->getTestingContainer($sef = false);
        $app = new Application($di);
        $view = new View($app);

        $this->assertEquals(
            '/application/index/index',
            $view->url('default')
        );

        $this->assertEquals(
            '/admin/index/index',
            $view->url('defaultAdmin')
        );
    }

    public function testSetVarsMethodShouldWork()
    {
        $this->assertTrue(
            method_exists($this->view, 'setVars'),
            'Method "setVars()" must exist'
        );

        $this->assertTrue(
            method_exists($this->view, 'getVars'),
            'Method "getVars()" must exist'
        );

        $this->view->setVars(['test1' => true, 'test2' => false]);
        $vars = $this->view->getVars();

        $this->assertInternalType('array', $this->view->getVars());
        $this->assertArrayHasKey('test1', $vars, 'Key "test1" must exist');
        $this->assertArrayHasKey('test2', $vars, 'Key "test2" must exist');
        $this->assertTrue($vars['test1'], 'It must be true');
        $this->assertFalse($vars['test2'], 'It must be true');
    }

    public function testAddVarMethodShouldWork()
    {
        $this->assertTrue(
            method_exists($this->view, 'addVar'),
            'Method "addVar()" must exist'
        );

        $this->view->addVar('test');

        $vars = $this->view->getVars();
        $this->assertInternalType('array', $vars);
        $this->assertArrayHasKey('test', $vars, 'Key "test" must exist');
        $this->assertNull($vars['test'], 'It must be null');
    }

    public function testDisableLayoutShouldWork()
    {
        $this->assertTrue(
            method_exists($this->view, 'disableLayout'),
            'Method "disableLayout()" must exist'
        );

        $this->view->disableLayout();
        $this->view->disableLayout(true);
        $this->view->disableLayout(false);
    }

    public function testSetCssShouldReturnAnInstanceOfView()
    {
        $this->assertTrue(
            method_exists($this->view, 'setCss'),
            'Method "setCss()" must exist'
        );

        $expected = $this->view->setCss(['path/to/file.css']);
        $this->assertInstanceOf('Piano\Mvc\View', $expected);
    }

    public function testSetJsShouldReturnAnInstanceOfView()
    {
        $this->assertTrue(
            method_exists($this->view, 'setJs'),
            'Method "setJs()" must exist'
        );

        $expected = $this->view->setJs(['path/to/file.js']);
        $this->assertInstanceOf('Piano\Mvc\View', $expected);
    }

    /**
     * @dataProvider jsPathProvider
     */
    public function testAddJsShouldReturnAnInstanceOfView($expected, $actual)
    {
        $this->assertTrue(
            method_exists($this->view, 'addJs'),
            'Method "addJs()" must exist'
        );

        $this->assertInstanceOf($expected, $this->view->addJs($actual));
    }

    /**
     * @dataProvider cssPathProvider
     */
    public function testAddCssShouldReturnAnInstanceOfView($expected, $actual)
    {
        $this->assertTrue(
            method_exists($this->view, 'addCss'),
            'Method "addCss()" must exist'
        );

        $this->assertInstanceOf($expected, $this->view->addCss($actual));
    }

    public function jsPathProvider()
    {
        return [
            ['Piano\Mvc\View', 'path/to/file.js'],
            ['Piano\Mvc\View', null],
            ['Piano\Mvc\View', ''],
        ];
    }

    public function cssPathProvider()
    {
        return [
            ['Piano\Mvc\View', 'path/to/file.css'],
            ['Piano\Mvc\View', null],
            ['Piano\Mvc\View', ''],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage View name is expected.
     */
    public function testRenderShouldThrowAnInvalidArgumentException()
    {
        $this->assertTrue(
            method_exists($this->view, 'render'),
            'Method "render()" must exist'
        );

        $this->view->render();
    }

    public function testGetCompleteViewPathShouldWork()
    {
        $this->assertTrue(
            method_exists($this->view, 'getCompleteViewPath'),
            'Method "getCompleteViewPath()" must exist'
        );

        $expected = '../src/Piano/layouts/menu.phtml';
        $this->assertEquals($expected, $this->view->getCompleteViewPath('/layouts/menu'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Partial name is expected.
     */
    public function testPartialShouldThrowAnInvalidArgumentException()
    {
        $this->assertTrue(
            method_exists($this->view, 'partial'),
            'Method "partial()" must exist'
        );

        $this->view->partial();
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegExp /Layout not found: \w+./
     */
    public function testPartialShouldThrowARuntimeException()
    {
        $this->assertTrue(
            method_exists($this->view, 'render'),
            'Method "render()" must exist'
        );

        $this->view->render('/teste');
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

        $container['modulesLayout'] = function () {
            return [
                'base' => [
                    'application',
                ],
                'admin' => [
                    'admin',
                ],
            ];
        };

        return $container;
    }
}
