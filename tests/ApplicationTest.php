<?php

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    private $app;

    public function setUp()
    {
        $_SERVER['REQUEST_URI'] = '/';

        $config = $this->getConfig();
        $router = $this->getRouter();
        $router->enableSearchEngineFriendly();

        $this->app = new Piano\Application($config, $router);
    }

    public function testShouldBeInstanceOfPianoApplication()
    {
        $this->assertInstanceOf(get_class($this->app), $this->app);
    }

    public function testSetUrlWithNoSearchEngineFriendlyShouldWork()
    {
        $router = $this->getRouter();
        $router->enableSearchEngineFriendly(true);
        $app = new Piano\Application($this->getConfig(), $router);

        $app->setUrl();
        $app->setUrl('/');
        $app->setUrl('default');
        $app->setUrl('/this/route');
        $app->setUrl('/application/index/index');
        $app->setUrl('/application/index/index/id/2');
        $app->setUrl('/admin/index/index');

        $router->enableSearchEngineFriendly(false);
        $app = new Piano\Application($this->getConfig(), $router);
        $app->setUrl();
        $app->setUrl('/');
        $app->setUrl('default');
        $app->setUrl('/this/route');
        $app->setUrl('/application/index/index');
        $app->setUrl('/application/index/index/id/2');
        $app->setUrl('/admin/index/index');
    }

    public function testGetApplicationFolderNameShouldWork()
    {
        $router = $this->getRouter();
        $config = $this->getConfig();
        $config->setApplicationFolder('appFolder');
        $app = new Piano\Application($config, $router);

        $this->assertEquals('appFolder', $app->getApplicationFolderName());

        $config->setApplicationFolder('application_folder');
        $app = new Piano\Application($config, $router);

        $this->assertEquals('application_folder', $app->getApplicationFolderName());
    }

    public function testGetConfigShouldReturnInstanceOfPianoConfig()
    {
        $this->assertInstanceOf('Piano\Config', $this->app->getConfig());
    }

    public function testGetRouterShouldWork()
    {
        $this->assertInstanceOf('Piano\Router', $this->app->getRouter());
    }

    public function testGetDefaultModuleNameShouldWork()
    {
        $this->assertEquals('testDefaultModuleName', $this->app->getDefaultModuleName());
    }

    public function testGetModuleNameShouldWork()
    {
        $router = $this->getRouter();
        $router->enableSearchEngineFriendly(false);
        $app = new Piano\Application($this->getConfig(), $router);

        $app->setUrl('/admin/index/index');
        $this->assertEquals('admin', $app->getModuleName());

        $app->setUrl('/upload/index/index');
        $this->assertEquals('upload', $app->getModuleName());
    }

    public function testGetControllerNameShouldWork()
    {
        $router = $this->getRouter();
        $router->enableSearchEngineFriendly(false);
        $app = new Piano\Application($this->getConfig(), $router);

        $app->setUrl('/admin/index/index');
        $this->assertEquals('IndexController', $app->getControllerName());

        $app->setUrl('/upload/image/index');
        $this->assertEquals('ImageController', $app->getControllerName());
    }

    public function testGetActionNameShouldWork()
    {
        $router = $this->getRouter();
        $router->enableSearchEngineFriendly(false);
        $app = new Piano\Application($this->getConfig(), $router);

        $app->setUrl('/admin/index/index');
        $this->assertEquals('index', $app->getActionName());

        $app->setUrl('/upload/image/add');
        $this->assertEquals('add', $app->getActionName());
    }

    public function testGetParamsShouldWork()
    {
        $router = $this->getRouter();
        $router->enableSearchEngineFriendly(false);
        $app = new Piano\Application($this->getConfig(), $router);

        $app->setUrl('/admin/index/index');
        $params = $app->getParams();
        $this->assertInternalType('array', $params);
        $this->assertEmpty($params);

        $app->setUrl('/admin/index/index/id/5/value/teste');
        $params = $app->getParams();
        $this->assertInternalType('array', $params);
        $this->assertArrayHasKey('id', $params);
        $this->assertArrayHasKey('value', $params);
        $this->assertEquals('5', $params['id']);
        $this->assertEquals('teste', $params['value']);
    }

    public function testGetParamShouldWork()
    {
        $router = $this->getRouter();
        $router->enableSearchEngineFriendly(false);
        $app = new Piano\Application($this->getConfig(), $router);

        $app->setUrl('/admin/index/index');
        $this->assertFalse($app->getParam());

        $app->setUrl('/admin/index/index/id/5/value/teste');
        $this->assertFalse($app->getParam());
        $this->assertEquals('5', $app->getParam('id'));
        $this->assertEquals('teste', $app->getParam('value'));
    }

    /**
     * @expectedException Exception
     */
    public function testGetParamShouldThrowAnException()
    {
        $this->app->getParam('key_does_not_exist');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRedirectShouldThrowAnInvalidArgumentException()
    {
        $this->app->redirect();
    }

    /**
     * @runInSeparateProcess
     */
    public function testRedirectShouldWork()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'http';
        $_SERVER['HTTP_HOST'] = 'localhost';

        $router = $this->getRouter();
        $router->enableSearchEngineFriendly(false);
        $app = new Piano\Application($this->getConfig(), $router);
        $app->redirect('/application/index/index');
        $app->redirect('/application/index/index', ['id' => 5, 'value' => 'teste']);
        $app->redirect('/application');
        $app->redirect('/');
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
