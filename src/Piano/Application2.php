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
    public function setUrl(string $urlPath = '')
    {
        $router = $this->getDi()['router'];

        if (empty($urlPath)) {
            $urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }

        if (!$router->match($urlPath)) {
            return $this->dispatchNotFoundRoute();
        }

        $routeFound = $router->getMatchedRoute();
        $this->moduleName = $routeFound['module'];
        $this->controllerName = $routeFound['controller'];
        $this->actionName = $routeFound['action'];
        $this->urlParams = $router->getMatchedRouteParams();
    }

    protected function dispatchNotFoundRoute()
    {
        $router = $this->getDi()['router'];
        $route404 = $router->getRoute('error404');

        if (is_null($route404)) {
            die('404 - Route not found!'); // @codeCoverageIgnore
        }

        $this->moduleName = $route404['module'];
        $this->controllerName = sprintf(
            '%sController',
            ucfirst($route404['controller'])
        );
        $this->actionName = $route404['action'];

        return;
    }

    public function redirect(string $urlPath = '', array $args = [])
    {
        if (empty($urlPath)) {
            throw new \InvalidArgumentException('Param url is expected.');
        }

        $router = $this->getDi()['router'];
        $route = $router->getRoute($urlPath);
        if (!is_null($route) && $router->isSearchEngineFriendly() && empty($args)) {
            $url = sprintf('//%s%s', $_SERVER['HTTP_HOST'], $route['route']);
            $this->header("Location: $url");
            return;
        }

        if (!is_null($route) && $router->isSearchEngineFriendly() && !empty($args)) {
            $routePieces = explode('/', $route['route']);
            $urlPattern = [];
            $url = [];
            foreach ($routePieces as $pos => $segment) {
                if ($router->isVar($segment) && !isset($args[$router->getVar()])) {
                    $url = sprintf('Location: //%s%s', $_SERVER['HTTP_HOST'], '/');
                    $this->header($url);
                    return;
                }

                if ($router->isVar($segment)) {
                    $urlPattern[$pos] = $route[0][$segment];
                    $url[] = $args[$router->getVar()];
                    continue;
                }

                $url[] = $segment;
                $urlPattern[$pos] = $segment;
            }

            $urlPattern = implode('/', $urlPattern);
            $url = implode('/', $url);
            $url = sprintf('//%s%s', $_SERVER['HTTP_HOST'], $url);
            $this->header("Location: $url");
            return;
        }

        if (!is_null($route) && !$router->isSearchEngineFriendly() && empty($args)) {
            $url = sprintf(
                '//%s/%s/%s/%s',
                $_SERVER['HTTP_HOST'],
                $route['module'],
                $route['controller'],
                $route['action']
            );
            $this->header("Location: $url");
            return;
        }

        if (!is_null($route) && !$router->isSearchEngineFriendly() && !empty($args)) {
            $routePieces = explode('/', $route['route']);

            $url = $urlPattern = [
                $_SERVER['HTTP_HOST'],
                $route['module'],
                $route['controller'],
                $route['action']
            ];

            foreach ($routePieces as $pos => $segment) {
                if ($router->isVar($segment) && !isset($args[$router->getVar()])) {
                    $url = sprintf('Location: //%s%s', $_SERVER['HTTP_HOST'], '/');
                    $this->header($url);
                    return;
                }

                if ($router->isVar($segment)) {
                    $urlPattern[] = $router->getVar();
                    $urlPattern[] = $route[0][$segment];
                    $url[] = $router->getVar();
                    $url[] = $args[$router->getVar()];
                    continue;
                }
            }

            $urlPattern = '//' . implode('/', $urlPattern);
            $url = '//' . implode('/', $url);

            $this->header("Location: $url");
            return;
        }
    }

    /**
     * @codeCoverageIgnore
     */
    protected function header(string $location)
    {
        header($location);
    }

    private function getSlashUrlParams($args)
    {
        $slashParams = '';
        if (!is_null($args) && count($args) > 0) {
            foreach ($args as $key => $value) {
                $slashParams .= '/'. $key . '/' . $value;
            }
        }

        return $slashParams;
    }
}
