<?php

declare(strict_types=1);

namespace Piano;

/**
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 */
class Router
{
    private $urlVar = ':';
    private $varName = '';
    private $routes = [];
    private $matchedRoute = [];
    private $matchedRouteParams = [];
    private $searchEngineFriendly = false;

    public function addRoute(string $name, string $routePath, array $config) : Router
    {
        $this->validateConfig($name, $config);

        $route = [];
        if (array_key_exists(0, $config)) {
            $route['params'] = $config[0];
            unset($config[0]);
        }

        $route['route'] = $routePath;
        $route += $config;

        $this->routes[$name] = $route;

        return $this;
    }

    public function setRoutes(array $routes) : Router
    {
        $this->routes = $routes;
        return $this;
    }

    public function getRoutes() : array
    {
        return $this->routes;
    }

    public function getRoute(string $name)
    {
        return $this->routes[$name] ?? null;
    }

    public function getMatchedRoute() : array
    {
        return $this->matchedRoute;
    }

    public function getMatchedRouteParams() : array
    {
        return $this->matchedRouteParams;
    }

    private function matchUrlWithSEF(string $url) : bool
    {
        $urlPieces = explode('/', $url);
        foreach ($this->routes as $routeName => $route) {
            $currentUrlPiecesPattern = [];
            $currentUrlParams = [];
            $routePieces = explode('/', $route['route']);
            foreach ($routePieces as $pos => $segment) {
                if ($this->isVar($segment)) {
                    $currentUrlParams[$this->getVar()] = $urlPieces[$pos];
                    $currentUrlPiecesPattern[$pos] = $route[0][$segment];
                    continue;
                }

                $currentUrlPiecesPattern[$pos] = $segment;
            }

            $currentUrlPattern = implode('/', $currentUrlPiecesPattern);
            if (preg_match('#^' . $currentUrlPattern . '$#', $url)) {
                unset($route['route'], $route[0]);
                $route['controller'] = $this->createControllerName($route['controller']);
                $this->matchedRoute = $route;
                $this->matchedRouteParams = $currentUrlParams;
                return true;
            }
        }

        return false;
    }

    private function matchUrlWithNoSEF(string $url) : bool
    {
        $urlPieces = explode('/', $url);
        if (count($urlPieces) < 4) {
            return false;
        }

        foreach ($this->routes as $routeName => $route) {
            if (count($urlPieces) >= 4
                && ($route['module'] != $urlPieces[1]
                || $route['controller'] != $urlPieces[2]
                || $route['action'] != $urlPieces[3])) {
                continue;
            }

            if (count($urlPieces) == 4) {
                unset($route['route'], $route[0]);
                $route['controller'] = $this->createControllerName($route['controller']);
                $this->matchedRoute = $route;
                return true;
            }

            unset($urlPieces[0], $urlPieces[1], $urlPieces[2], $urlPieces[3]);
            $params = [];
            foreach ($urlPieces as $key => $var) {
                if ($key % 2 == 0) {
                    $params[$var] = $urlPieces[$key + 1] ?? '';
                }
            }

            foreach ($params as $var => $value) {
                $routeVar = ':' . $var;
                if (!array_key_exists($routeVar, $route[0])
                    || preg_match('#^' . $route[0][$routeVar] . '$#', $value) === 0) {
                    return false;
                }
            }

            unset($route['route'], $route[0]);
            $route['controller'] = $this->createControllerName($route['controller']);
            $this->matchedRoute = $route;
            $this->matchedRouteParams = $params;

            return true;
        }

        return false;
    }

    private function createControllerName(string $controller) : string
    {
        return sprintf(
            '%sController',
            ucfirst($controller)
        );
    }

    public function match(string $url) : bool
    {
        if ($this->isSearchEngineFriendly()) {
            return $this->matchUrlWithSEF($url);
        }

        return $this->matchUrlWithNoSEF($url);
    }

    public function getUrl(string $name, array $params = null) : string
    {
        if ($this->isSearchEngineFriendly()) {
            return $this->getUrlWithSEF($name, $params);
        }

        return $this->getUrlWithNoSEF($name, $params);
    }

    private function getUrlWithSEF(string $name, array $params = null) : string
    {
        // URL with no parameters
        $route = $this->getRoute($name);
        $friendlyUrl = $route['route'];

        if (is_null($params)) {
            return $friendlyUrl;
        }

        // URL with parameters
        $arrayUrlParams = $route[0] ?? [];
        $url = explode('/', $friendlyUrl);
        // $urlRegex = $url;

        foreach ($params as $key => $value) {
            $var = ':' . $key;
            if ($k = array_search($var, $url)) {
                $url[$k] = $value;
                // $urlRegex[$k] = $arrayUrlParams[$var];
            }
        }

        $url = implode('/', $url);

        // TODO
        // $urlRegex = implode('/', $urlRegex);
        // Maybe validate the URL pattern against $urlRegex?

        return $url;
    }

    private function getUrlWithNoSEF(string $name, array $params = null) : string
    {
        // URL with no parameters
        $route = $this->getRoute($name);
        $arrayUrlParams = $route[0] ?? [];

        $url = sprintf(
            '/%s/%s/%s/',
            $route['module'],
            $route['controller'],
            $route['action']
        );

        if (empty($arrayUrlParams)) {
            return $url;
        }

        // URL with parameters
        $urlParams = [];
        $urlParamsRegex = [];
        foreach ($arrayUrlParams as $key => $value) {
            $urlParams[] = substr($key, 1) . '/' . $params[substr($key, 1)];
            $urlParamsRegex[] = substr($key, 1) . '/' . $arrayUrlParams[$key];
        }

        $url .= implode('/', $urlParams);

        // TODO
        // $urlRegex .= implode('/', $urlParamsRegex);
        // Maybe validate the URL against $urlRegex?

        return $url;
    }

    public function enableSearchEngineFriendly($flag = true)
    {
        $this->searchEngineFriendly = $flag;
    }

    public function isSearchEngineFriendly()
    {
        return (bool) $this->searchEngineFriendly;
    }

    private function validateConfig(string $routeName, array $config)
    {
        if (empty($config)) {
            throw new \InvalidArgumentException('Route config cannot be empty.');
        }

        if (!array_key_exists('module', $config) || empty(trim($config['module']))) {
            throw new \InvalidArgumentException(
                sprintf('Route %s must have a valid module configuration.', $routeName)
            );
        }

        if (!array_key_exists('controller', $config) || empty(trim($config['controller']))) {
            throw new \InvalidArgumentException(
                sprintf('Route %s must have a valid controller configuration.', $routeName)
            );
        }

        if (!array_key_exists('action', $config) || empty(trim($config['action']))) {
            throw new \InvalidArgumentException(
                sprintf('Route %s must have a valid action configuration.', $routeName)
            );
        }
    }

    public function isVar(string $name) : bool
    {
        if (substr($name, 0, 1) == ':') {
            $this->varName = substr($name, 1);
            return true;
        }

        return false;
    }

    public function getVar() : string
    {
        return $this->varName;
    }
}
