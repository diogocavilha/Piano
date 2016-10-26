<?php

declare(strict_types=1);

namespace Piano;

use \Piano\Container;

/**
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 */
class Application2
{
    private $container;
    private $moduleName;

    public function __construct(Container $container)
    {
        if (!isset($container['config'])) {
            throw new \RuntimeException('Key "config" is missing');
        }

        if (!isset($container['router'])) {
            throw new \RuntimeException('Key "router" is missing');
        }

        $this->container = $container;
    }

    public function getDi() : Container
    {
        return $this->container;
    }

    public function getApplicationFolderName() : string
    {
        return $this->getDi()['config']->get('defaultDirectory');
    }

    public function getDefaultModuleName() : string
    {
        return $this->getDi()['config']->get('defaultModule');
    }

    public function getModuleName() : string
    {
        return $this->moduleName;
    }

    /**
     * Sets the requested URL.
     *
     * In case the URL does not exist, sets the default URL to the default module.
     * @access public
     */
    public function setUrl($urlPath = null, array $args = null)
    {
        $router = $this->getDi()['router'];

        if (is_null($urlPath)) {
            $urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }

        $routeExists = $router->match($urlPath);
        if ($router->isSearchEngineFriendly() && !$routeExists) {
            return $this->dispatchRouteDefault();
        }

        if ($router->isSearchEngineFriendly() && $routeExists) {
            $routeFound = $router->getMatchedRoute();
            $this->moduleName = $routeFound['module'];
            $this->controllerName = ucfirst($routeFound['controller']) . 'Controller';
            $this->actionName = $routeFound['action'];
            $this->urlParams = $router->getMatchedRouteParams();
            return;
        }

        if ($urlPath == '/') {
            $this->moduleName = $this->getDefaultModuleName();
            $this->controllerName = 'IndexController';
            $this->actionName = 'index';
            return;
        }

        $arrayUrl = explode('/', $urlPath);

        if (count($arrayUrl) <= 3) {
            return $this->dispatchRouteDefault();
        }

        $this->moduleName = $arrayUrl[1];
        $this->controllerName = ucfirst($arrayUrl[2]) . 'Controller';
        $this->actionName = $arrayUrl[3];

        unset($arrayUrl[0], $arrayUrl[1], $arrayUrl[2], $arrayUrl[3]);

        if (is_null($args)) {
            $args = [];
            foreach ($arrayUrl as $key => $value) {
                if ($key == 0 || $key % 2 == 0) {
                    $args[$value] = (!isset($arrayUrl[$key+1]) || empty($arrayUrl[$key+1])) ? '' : $arrayUrl[$key+1];
                }
            }
        }

        $this->urlParams = $args;
    }

    protected function dispatchRouteDefault()
    {
        $router = $this->getDi()['router'];
        $route404 = $router->getRoute('error_404');

        if (is_null($route404)) {
            die('404 - Route not found!'); // @codeCoverageIgnore
        }

        $this->moduleName = $route404['module'];
        $this->controllerName = ucfirst($route404['controller']) . 'Controller';
        $this->actionName = $route404['action'];

        return;
    }
}
