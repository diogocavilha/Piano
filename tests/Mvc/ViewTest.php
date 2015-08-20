<?php

class ViewTest extends PHPUnit_Framework_TestCase
{
    private $view;

    public function setUp()
    {
        $_SERVER['REQUEST_URI'] = '/';

        $config = $this->getConfig();
        $router = $this->getRouter();
        $router->enableSearchEngineFriendly();

        $app = new Piano\Application($config, $router);

        $this->view = new Piano\Mvc\View($app);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage View name is expected.
     */
    public function testRenderShouldThrowAnInvalidArgumentException()
    {
        $this->view->render();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Partial name is expected.
     */
    public function testPartialShouldThrowAnInvalidArgumentException()
    {
        $this->view->partial();
    }

    public function testDisableLayoutShouldWork()
    {
        $this->view->disableLayout();
        $this->view->disableLayout(true);
        $this->view->disableLayout(false);
    }

    public function testGetCompleteViewPathShouldWork()
    {
        $expected = '../src/application/menu.phtml';
        $this->assertEquals($expected, $this->view->getCompleteViewPath('/menu'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Param route name is expected.
     */
    public function testUrlShouldThrowAnInvalidArgumentException()
    {
        $this->view->url();
    }

    public function testUrlShouldWork()
    {
        $_SERVER['REQUEST_URI'] = '/';

        $config = $this->getConfig();
        $router = $this->getRouter();
        $router->enableSearchEngineFriendly();

        $app = new Piano\Application($config, $router);

        $view = new Piano\Mvc\View($app);

        $this->assertEquals('/', $this->view->url('default'));
        $this->assertEquals('/admin', $this->view->url('defaultAdmin'));

        $router->enableSearchEngineFriendly(false);

        $app = new Piano\Application($config, $router);

        $view = new Piano\Mvc\View($app);

        $this->assertEquals('/', $this->view->url('default'));
        $this->assertEquals('/admin', $this->view->url('defaultAdmin'));
    }

    private function getRouter()
    {
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
    }

    private function getConfig()
    {
        $config = new \Piano\Config();
        $config->setApplicationFolder('application')
            ->setDefaultModule('testDefaultModuleName')
            ->setLayoutPerModule([
                'base' => [
                    'application',
                ],
                'admin' => [
                    'admin',
                ],
            ]);


        return $config;
    }

    /**
     * @dataProvider jsPathProvider
     */
    public function testAddJsShouldReturnAnInstanceOfView($expected, $actual)
    {
        $this->assertInstanceOf($expected, $this->view->addJs($actual));
    }

    public function testSetJsShouldReturnAnInstanceOfView()
    {
        $expected = $this->view->setJs(['path/to/file.js']);
        $this->assertInstanceOf('Piano\Mvc\View', $expected);
    }

    /**
     * @dataProvider cssPathProvider
     */
    public function testAddCssShouldReturnAnInstanceOfView($expected, $actual)
    {
        $this->assertInstanceOf($expected, $this->view->addCss($actual));
    }

    public function testSetCssShouldReturnAnInstanceOfView()
    {
        $expected = $this->view->setCss(['path/to/file.css']);
        $this->assertInstanceOf('Piano\Mvc\View', $expected);
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
}
