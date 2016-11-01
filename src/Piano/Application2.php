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

        if (!is_null($route) && $router->isSearchEngineFriendly() && !empty($args)) {
            $routePieces = explode('/', $route['route']);
            $currentUrlPiecesPattern = [];
            $url = [];
            foreach ($routePieces as $pos => $segment) {
                if (substr($segment, 0, 1) == ':') {
                    $varName = substr($segment, 1);
                    $currentUrlPiecesPattern[$pos] = $route[0][$segment];
                    $url[] = $args[$varName];
                    continue;
                }

                $url[] = $segment;

                $currentUrlPiecesPattern[$pos] = $segment;
            }

            $currentUrlPattern = implode('/', $currentUrlPiecesPattern);
            $url = implode('/', $url);
            $url = sprintf('//%s%s', $_SERVER['HTTP_HOST'], $url);
            $this->header("Location: $url");
            return;
        }


        // $arrayUrl = explode('/', $urlPath);

        // if (count($arrayUrl) <= 3) {
        //     return $this->dispatchRouteDefault();
        // }

        // $this->moduleName = $arrayUrl[1];
        // $this->controllerName = ucfirst($arrayUrl[2]) . 'Controller';
        // $this->actionName = $arrayUrl[3];

        // $module = $arrayUrl[1];
        // $controller = $arrayUrl[2];
        // $action = $arrayUrl[3];

        // unset($arrayUrl[0], $arrayUrl[1], $arrayUrl[2], $arrayUrl[3]);

        // if ($router->isSearchEngineFriendly()) {
        //     if (is_null($args)) {
        //         $args = [];
        //         foreach ($arrayUrl as $key => $value) {
        //             if ($key % 2 == 0) {
        //                 $args[$value] = $arrayUrl[$key + 1] ?? '';
        //             }
        //         }
        //     }

        //     $this->urlParams = $args;

        //     $allRoutes = $router->getRoutes();

        //     foreach ($allRoutes as $route) {
        //         if ($route['module'] == $module && $route['controller'] == $controller && $route['action'] == $action) {
        //             $friendlyUrl = $route['route'];
        //             $slashParams = $this->getSlashUrlParams($args);

        //             $this->header("Location: //{$_SERVER['HTTP_HOST']}{$friendlyUrl}{$slashParams}");

        //             return;
        //         }
        //     }
        // }

        // $this->urlParams = $args;
        // $slashParams = $this->getSlashUrlParams($args);

        // $this->header("Location: //{$_SERVER['HTTP_HOST']}{$urlPath}{$slashParams}");
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
