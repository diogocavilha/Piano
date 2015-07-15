<?php

class RouterTest extends PHPUnit_Framework_Testcase
{
    private $router;

    public function setUp()
    {
        $this->router = new Piano\Router();
    }

    public function testItShouldBeInstanceOfPianoRouter()
    {
        $this->assertInstanceOf(get_class($this->router), $this->router);
    }

    public function testEnableSearchEngineFriendlyShouldWork()
    {
        $this->router->enableSearchEngineFriendly();
        $this->assertTrue($this->router->isSearchEngineFriendly());

        $this->router->enableSearchEngineFriendly(true);
        $this->assertTrue($this->router->isSearchEngineFriendly());

        $this->router->enableSearchEngineFriendly(false);
        $this->assertFalse($this->router->isSearchEngineFriendly());
    }

    public function testSetRoutesShouldWork()
    {
        $this->router->setRoutes($this->getRoutes());
        $this->assertInternalType('array', $this->router->getRoutes());
        $this->assertInternalType('array', $this->router->getRoute('redirect'));

        return $this->router;
    }

    /**
     * @depends testSetRoutesShouldWork
     */
    public function testAddRouteShouldWork($router)
    {
        $router->addRoute('routeTest', '/test', []);
        $this->assertInternalType('array', $router->getRoute('routeTest'));
        $this->assertEquals('/test', $router->getRoute('routeTest')['route']);

        $router->addRoute('routeTest2', '/test/:id', [':id' => '\d+']);
        $this->assertInternalType('array', $router->getRoute('routeTest2'));
        $this->assertEquals('/test/:id', $router->getRoute('routeTest2')['route']);
    }

    /**
     * @depends testSetRoutesShouldWork
     */
    public function testAddRouteWithStaticParamsShouldWork($router)
    {
        $router->addRoute('routeTest3', '/testparams', [':id' => ['name' => 'Diogo']]);
        $this->assertInternalType('array', $router->getRoute('routeTest3'));
        $this->assertInternalType('array', $router->getRoute('routeTest3')['params']);
        $this->assertEquals('/testparams', $router->getRoute('routeTest3')['route']);
        $this->assertEquals('Diogo', $router->getRoute('routeTest3')['params']['name']);
    }

    public function testGetRouteShouldReturnNull()
    {
        $this->assertNull($this->router->getRoute());
        $this->assertNull($this->router->getRoute('routeDoesNotExist'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Param name is expected.
     */
    public function testGetUrlShouldThrowAnInvalidArgumentException()
    {
        $this->router->getUrl();
    }

    /**
     * @depends testSetRoutesShouldWork
     */
    public function testGetUrlShouldWork($router)
    {
        $router->enableSearchEngineFriendly(false);
        $this->assertEquals('/application/index/index', $router->getUrl('default'));
        $this->assertEquals('/application/user/edit/id/', $router->getUrl('user_edit'));

        $router->enableSearchEngineFriendly(true);
        $this->assertEquals('/', $router->getUrl('default'));
        $this->assertEquals('/users/:id', $router->getUrl('user_edit'));
        $this->assertEquals('/users/5', $router->getUrl('user_edit', ['id' => 5]));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Param url is expected.
     */
    public function testShouldThrowAnInvalidArgumentException()
    {
        $this->router->match();
    }

    /**
     * @depends testSetRoutesShouldWork
     */
    public function testMethodMatchShouldWork($router)
    {
        $this->assertTrue($router->match('/'));
        $this->assertTrue($router->match('/users/5'));
        $this->assertFalse($router->match('/route/does/not/exist'));
    }

    /**
     * @depends testSetRoutesShouldWork
     */
    public function testGetMatchedRouteShouldWork($router)
    {
        $this->assertTrue($router->match('/users/5'));
        $matchedRoute = $router->getMatchedRoute();

        $this->assertInternalType('array', $matchedRoute);
        $this->assertArrayHasKey('module', $matchedRoute);
        $this->assertArrayHasKey('controller', $matchedRoute);
        $this->assertArrayHasKey('action', $matchedRoute);
    }

    /**
     * @depends testSetRoutesShouldWork
     */
    public function testGetMatchedRouteParamsShouldWork($router)
    {
        $this->assertTrue($router->match('/users/5'));
        $routeParams = $router->getMatchedRouteParams();
        $this->assertInternalType('array', $routeParams);
        $this->assertArrayHasKey('id', $routeParams);
    }

    /**
     * @depends testSetRoutesShouldWork
     */
    public function testGetMatchedRouteNameShouldWork($router)
    {
        $this->assertTrue($router->match('/users/5'));
        $routeParams = $router->getMatchedRouteName();

        $this->assertInternalType('array', $routeParams);
        $this->assertArrayHasKey('module', $routeParams);
        $this->assertArrayHasKey('controller', $routeParams);
        $this->assertArrayHasKey('action', $routeParams);
        $this->assertArrayHasKey(0, $routeParams);
    }

    private function getRoutes()
    {
        return $routes = [
            'default' => [
                'route' => '/',
                'module' => 'application',
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
    }
}
