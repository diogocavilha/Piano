<?php

class ControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    public function setUp()
    {
        $_SERVER['REQUEST_URI'] = '/';

        $config = $this->getConfig();
        $router = $this->getRouter();
        $router->enableSearchEngineFriendly();

        $app = new Piano\Application($config, $router);

        $this->controller = new Piano\Mvc\Controller($app);
    }

    public function testGetParamsShouldWork()
    {
        $this->assertEmpty($this->controller->getParams());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Param name is expected.
     */
    public function testGetParamShouldThrowAnInvalidArgumentException()
    {
        $this->controller->getParam();
    }

    /**
     * @expectedException Exception
     */
    public function testGetParamShouldWork()
    {
        $this->controller->getParam('teste');
    }

    public function testGetApplicationShouldWork()
    {
        $this->assertInstanceOf('Piano\Application', $this->controller->getApplication());
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
}
