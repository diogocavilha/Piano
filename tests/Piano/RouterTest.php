<?php

use Piano\Router;

/**
 * @group php7
 * @group url
 */
class RouterTest extends PHPUnit_Framework_Testcase
{
    private $router;

    public function setUp()
    {
        $this->router = new Router();
    }

    public function testItShouldBeInstanceOfPianoRouter()
    {
        $this->assertInstanceOf(get_class($this->router), $this->router);
    }

    public function testSearchEngineFriendlyMustBeEnabledWithNoParameters()
    {
        $this->router->enableSearchEngineFriendly();
        $this->assertTrue(
            $this->router->isSearchEngineFriendly(),
            'SEF must be enabled'
        );
    }

    public function testSearchEngineFriendlyMustBeEnabledWithParameterTrue()
    {
        $this->router->enableSearchEngineFriendly(true);
        $this->assertTrue(
            $this->router->isSearchEngineFriendly(),
            'SEF must be enabled'
        );
    }

    public function testSearchEngineFriendlyMustBeDisabledWithParameterFalse()
    {
        $this->router->enableSearchEngineFriendly(false);
        $this->assertFalse(
            $this->router->isSearchEngineFriendly(),
            'SEF must be disabled'
        );
    }

    public function testSetRoutesMustSetupAnArrayOfRoutes()
    {
        $this->assertTrue(
            method_exists($this->router, 'setRoutes'),
            'Method "setRoutes()" must exist'
        );

        $this->assertTrue(
            method_exists($this->router, 'getRoutes'),
            'Method "getRoutes()" must exist'
        );

        $this->router->setRoutes($this->getRoutes());
        $routes = $this->router->getRoutes();

        $this->assertInternalType('array', $routes);
        $this->assertArraySubset(
            [
                'default' => [
                    'route' => '/',
                    'module' => 'application',
                    'controller' => 'index',
                    'action' => 'index'
                ]
            ],
            $routes,
            'Route "default" must exist'
        );

        $this->assertArraySubset(
            [
                'user_edit' => [
                    'route' => '/users/:id',
                    'module' => 'application',
                    'controller' => 'user',
                    'action' => 'edit',
                    [
                        ':id' => '\d+'
                    ]
                ]
            ],
            $routes,
            'Route "user_edit" must exist'
        );

        $this->assertArraySubset(
            [
                'error_404' => [
                    'route' => '/error',
                    'module' => 'application',
                    'controller' => 'error',
                    'action' => 'error',
                ]
            ],
            $routes,
            'Route "error_404" must exist'
        );

        $this->assertArraySubset(
            [
                'redirect' => [
                    'route' => '/redirect/contact',
                    'module' => 'application',
                    'controller' => 'index',
                    'action' => 'redirectTest',
                ]
            ],
            $routes,
            'Route "redirect" must exist'
        );

    }

    public function testGetRouteMustReturnAllDataInformationFromTheGivenRouteName()
    {
        $this->assertTrue(
            method_exists($this->router, 'getRoute'),
            'Method "getRoute()" must exist'
        );

        $routes = [
            'user_edit' => [
                'route' => '/users/:id',
                'module' => 'application',
                'controller' => 'user',
                'action' => 'edit',
                [
                    ':id' => '\d+'
                ]
            ],
        ];

        $this->router->setRoutes($routes);

        $route = $this->router->getRoute('user_edit');

        $this->assertInternalType('array', $route);
        $this->assertArrayHasKey('route', $route);
        $this->assertArrayHasKey('module', $route);
        $this->assertArrayHasKey('controller', $route);
        $this->assertArrayHasKey('action', $route);

        $this->assertArraySubset(
            [
                [
                    ':id' => '\d+'
                ]
            ],
            $route,
            'The route parameters must exist'
        );

        $this->assertEquals('/users/:id', $route['route']);
        $this->assertEquals('application', $route['module']);
        $this->assertEquals('user', $route['controller']);
        $this->assertEquals('edit', $route['action']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Route config cannot be empty.
     */
    public function testAddRouteMustThrownInvalidArgumentExceptionWhenConfigArrayIsEmpty()
    {
        $this->assertTrue(
            method_exists($this->router, 'addRoute'),
            'Method "addRoute()" must exist'
        );

        $config = [];
        $this->router->addRoute('testRoute', '/test', $config);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp /Route \w+ must have a valid module configuration\./
     */
    public function testAddRouteMustThrownInvalidArgumentExceptionWhenConfigArrayHasNoAModuleName()
    {
        $this->assertTrue(
            method_exists($this->router, 'addRoute'),
            'Method "addRoute()" must exist'
        );

        $this->router->addRoute('testRoute', '/test', [
            'controller' => 'index',
            'action' => 'index',
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp /Route \w+ must have a valid controller configuration\./
     */
    public function testAddRouteMustThrownInvalidArgumentExceptionWhenConfigArrayHasNoAControllerName()
    {
        $this->assertTrue(
            method_exists($this->router, 'addRoute'),
            'Method "addRoute()" must exist'
        );

        $config = [
            'module' => 'index',
            'action' => 'index',
        ];

        $this->router->addRoute('testRoute', '/test', $config);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp /Route \w+ must have a valid action configuration\./
     */
    public function testAddRouteMustThrownInvalidArgumentExceptionWhenConfigArrayHasNoActionName()
    {
        $this->assertTrue(
            method_exists($this->router, 'addRoute'),
            'Method "addRoute()" must exist'
        );

        $config = [
            'module' => 'index',
            'controller' => 'index',
        ];

        $this->router->addRoute('testRoute', '/test', $config);
    }

    public function testAddRouteMustAddARouteWithNoParameters()
    {
        $this->assertTrue(
            method_exists($this->router, 'addRoute'),
            'Method "addRoute()" must exist'
        );

        $this->assertTrue(
            method_exists($this->router, 'getRoute'),
            'Method "getRoute()" must exist'
        );

        $config = [
            'module' => 'application',
            'controller' => 'index',
            'action' => 'index',
        ];

        $this->router->addRoute('testRoute', '/test', $config);

        $route = $this->router->getRoute('testRoute');

        $this->assertInternalType('array', $route);
        $this->assertArrayHasKey('route', $route);
        $this->assertEquals('/test', $route['route']);
    }

    public function testAddRouteMustAddARouteWithParameters()
    {
        $this->assertTrue(
            method_exists($this->router, 'addRoute'),
            'Method "addRoute()" must exist'
        );

        $this->assertTrue(
            method_exists($this->router, 'getRoute'),
            'Method "getRoute()" must exist'
        );

        $config = [
            'module' => 'application',
            'controller' => 'index',
            'action' => 'index',
            [
                ':id' => '\d+',
            ]
        ];

        $this->router->addRoute('testRoute', '/test', $config);

        $route = $this->router->getRoute('testRoute');

        $this->assertInternalType('array', $route);
        $this->assertArrayHasKey('route', $route);
        $this->assertArraySubset(
            [
                'params' => [
                    ':id' => '\d+',
                ]
            ],
            $route,
            'Route must have a subarray with parameters'
        );

        $this->assertEquals('/test', $route['route']);
    }

    public function testAddRouteWithStaticParamsShouldWork()
    {
        $this->assertTrue(
            method_exists($this->router, 'addRoute'),
            'Method "addRoute()" must exist'
        );

        $config = [
            'module' => 'application',
            'controller' => 'index',
            'action' => 'index',
            [
                ':name' => 'Diogo'
            ]
        ];

        $this->router->addRoute('routeTest', '/testparams', $config);

        $route = $this->router->getRoute('routeTest');

        $this->assertInternalType('array', $route);
        $this->assertArrayHasKey('route', $route);
        $this->assertArraySubset(
            [
                'params' => [
                    ':name' => 'Diogo'
                ]
            ],
            $route,
            'Route must have a subarray with correct parameters'
        );
        $this->assertEquals('/testparams', $route['route']);
    }

    public function testGetRouteWithAnInvalidRouteNameMustReturnANullValue()
    {
        $this->assertNull(
            $this->router->getRoute('routeDoesNotExist'),
            'Route "routeDoesNotExist" must not exist'
        );
    }

    public function testGetUrlMustReturnTheUrlWhenSearchEngineFriendlyIsDisabled()
    {
        $this->router->setRoutes($this->getRoutes());
        $this->router->enableSearchEngineFriendly(false);

        $this->assertEquals(
            '/application/index/index/',
            $this->router->getUrl('default'),
            'URL must be "/application/index/index/"'
        );

        $this->assertEquals(
            '/application/user/edit/id/5',
            $this->router->getUrl('user_edit', ['id' => 5]),
            'URL must be "/application/user/edit/id/5"'
        );
    }

    public function testGetUrlMustReturnTheUrlWhenSearchEngineFriendlyIsEnabled()
    {
        $this->router->setRoutes($this->getRoutes());
        $this->router->enableSearchEngineFriendly(true);

        $this->assertEquals(
            '/',
            $this->router->getUrl('default'),
            'URL must be "/"'
        );

        $this->assertEquals(
            '/users/:id',
            $this->router->getUrl('user_edit'),
            'URL must be "/users/:id"'
        );

        $this->assertEquals(
            '/users/5',
            $this->router->getUrl('user_edit', ['id' => 5]),
            'URL must be "/users/5"'
        );
    }

    public function testItMustMatchTheRouteByAGivenUrlWhenSearchEngineFriendlyIsEnabled()
    {
        $this->router->setRoutes($this->getRoutes());
        $this->router->enableSearchEngineFriendly(true);
        $this->assertTrue($this->router->match('/'));
        $this->assertTrue($this->router->match('/users/5'));
        $this->assertTrue($this->router->match('/users/5/nameTest'));
        $this->assertFalse($this->router->match('/route/does/not/exist'));
    }

    public function testItMustMatchTheRouteByAGivenUrlWhenSearchEngineFriendlyIsDisabled()
    {
        $this->router->setRoutes($this->getRoutes());
        $this->router->enableSearchEngineFriendly(false);
        $this->assertTrue($this->router->match('/application/index/index'));
        $this->assertTrue($this->router->match('/application/index/redirectTest'));
        $this->assertTrue(
            $this->router->match('/application/admin/edit/id/5')
        );
        $this->assertFalse(
            $this->router->match('/application/admin/edit/id/3/name/teste/age/50/phone/555')
        );
    }

    public function testGetMatchedRouteMustReturnTheMatchedRouteWhenSearchEngineFriendlyIsEnabled()
    {
        $this->router->enableSearchEngineFriendly(true);

        $this->assertTrue(
            method_exists($this->router, 'getMatchedRoute'),
            'Method "getMatchedRoute()" must exist'
        );

        $this->router->setRoutes([
            'admin_edit' => [
                'route' => '/users/:id/:name',
                'module' => 'application',
                'controller' => 'admin',
                'action' => 'edit',
                [
                    ':id' => '\d+',
                    ':name' => '\w+',
                ]
            ]
        ]);

        $this->assertTrue($this->router->match('/users/5/TestName'));

        $matchedRoute = $this->router->getMatchedRoute();
        $this->assertInternalType(
            'array',
            $matchedRoute,
            'Matched route must be an array'
        );
        $this->assertArrayHasKey('module', $matchedRoute);
        $this->assertArrayHasKey('controller', $matchedRoute);
        $this->assertArrayHasKey('action', $matchedRoute);
        $this->assertEquals('application', $matchedRoute['module']);
        $this->assertEquals('admin', $matchedRoute['controller']);
        $this->assertEquals('edit', $matchedRoute['action']);
    }

    public function testGetMatchedRouteMustReturnTheMatchedRouteWhenSearchEngineFriendlyIsDisabled()
    {
        $this->router->enableSearchEngineFriendly(false);

        $this->assertTrue(
            method_exists($this->router, 'getMatchedRoute'),
            'Method "getMatchedRoute()" must exist'
        );

        $this->router->setRoutes([
            'admin_edit' => [
                'route' => '/admin/:id/:name',
                'module' => 'application',
                'controller' => 'admin',
                'action' => 'edit',
                [
                    ':id' => '\d+',
                    ':name' => '\w+',
                ]
            ]
        ]);

        $this->assertTrue($this->router->match('/application/admin/edit'));

        $matchedRoute = $this->router->getMatchedRoute();

        $this->assertInternalType(
            'array',
            $matchedRoute,
            'Matched route must be an array'
        );
        $this->assertArrayHasKey('module', $matchedRoute);
        $this->assertArrayHasKey('controller', $matchedRoute);
        $this->assertArrayHasKey('action', $matchedRoute);
        $this->assertEquals('application', $matchedRoute['module']);
        $this->assertEquals('admin', $matchedRoute['controller']);
        $this->assertEquals('edit', $matchedRoute['action']);
    }

    public function testGetMatchedRouteParamsMustReturnAllMatchedRouteParamsWhenSearchEngineFriendlyIsEnabled()
    {
        $this->router->enableSearchEngineFriendly(true);

        $this->assertTrue(
            method_exists($this->router, 'getMatchedRouteParams'),
            'Method "getMatchedRouteParams()" must exist'
        );

        $this->router->setRoutes([
            'admin_edit' => [
                'route' => '/admin/:id/:name',
                'module' => 'application',
                'controller' => 'admin',
                'action' => 'edit',
                [
                    ':id' => '\d+',
                    ':name' => '\w+',
                ]
            ]
        ]);

        $this->assertTrue($this->router->match('/admin/5/TestName'));

        $routeParams = $this->router->getMatchedRouteParams();

        $this->assertInternalType('array', $routeParams);
        $this->assertArrayHasKey('id', $routeParams);
        $this->assertArrayHasKey('name', $routeParams);
        $this->assertEquals('5', $routeParams['id']);
        $this->assertEquals('TestName', $routeParams['name']);
    }

    public function testGetMatchedRouteParamsMustReturnAllMatchedRouteParamsWhenSearchEngineFriendlyIsDisabled()
    {
        $this->router->enableSearchEngineFriendly(false);

        $this->assertTrue(
            method_exists($this->router, 'getMatchedRouteParams'),
            'Method "getMatchedRouteParams()" must exist'
        );

        $this->router->setRoutes([
            'admin_edit' => [
                'route' => '/admin/:id/:name',
                'module' => 'application',
                'controller' => 'admin',
                'action' => 'edit',
                [
                    ':id' => '\d+',
                    ':name' => '\w+',
                ]
            ]
        ]);

        $this->assertTrue($this->router->match('/application/admin/edit/id/5/name/TestName'));

        $routeParams = $this->router->getMatchedRouteParams();

        $this->assertInternalType('array', $routeParams);
        $this->assertArrayHasKey('id', $routeParams);
        $this->assertArrayHasKey('name', $routeParams);
        $this->assertEquals('5', $routeParams['id']);
        $this->assertEquals('TestName', $routeParams['name']);
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
            'client_edit' => [
                'route' => '/users/:id/:name',
                'module' => 'application',
                'controller' => 'admin',
                'action' => 'edit',
                [
                    ':id' => '\d+',
                    ':name' => '\w+',
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
