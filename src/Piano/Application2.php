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
    private $controllerName;
    private $actionName;
    private $urlParams = [];

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

    public function getControllerName() : string
    {
        return $this->controllerName;
    }

    public function getActionName() : string
    {
        return $this->actionName;
    }

    public function getParams() : array
    {
        return $this->urlParams;
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

        if (!$routeExists) {
            return $this->dispatchNotFoundRoute();
        }

        $routeFound = $router->getMatchedRoute();
        if ($router->isSearchEngineFriendly() || $urlPath == '/') {
            $this->setupUrl();
            return;
        }

        $urlPieces = explode('/', $urlPath);
        if (count($urlPieces) <= 3) {
            $this->setupUrl();
            return;
        }

        $this->moduleName = $urlPieces[1];
        $this->controllerName = sprintf('%sController', ucfirst($urlPieces[2]));
        $this->actionName = $urlPieces[3];

        unset($urlPieces[0], $urlPieces[1], $urlPieces[2], $urlPieces[3]);

        if (!is_null($args)) {
            $this->urlParams = $args;
            return;
        }

        $args = [];
        foreach ($urlPieces as $key => $value) {
            if ($key == 0 || $key % 2 == 0) {
                $args[$value] = (!isset($urlPieces[$key+1]) || empty($urlPieces[$key+1])) ? '' : $urlPieces[$key+1];
            }
        }

        $this->urlParams = $args;
    }

    private function setupUrl()
    {
        $router = $this->getDi()['router'];
        $routeFound = $router->getMatchedRoute();

        $this->moduleName = $routeFound['module'];
        $this->controllerName = sprintf(
            '%sController',
            ucfirst($routeFound['controller'])
        );
        $this->actionName = $routeFound['action'];
        $this->urlParams = $router->getMatchedRouteParams();
    }

    protected function dispatchNotFoundRoute()
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
