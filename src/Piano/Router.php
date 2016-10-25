<?php

declare(strict_types=1);

namespace Piano;

/**
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 */
class Router
{
    private $regexDelimiter = '#';
    private $urlVar = ':';
    private $routes = [];
    private $matchedRoute = null;
    private $matchedRouteName = null;
    private $matchedRouteParams = null;
    private $searchEngineFriendly = false;

    public function addRoute(string $name, string $routePath, array $config) : Router
    {
        $this->validateConfig($name, $config);

        $params = null;

        foreach ($config as $pos => $arg) {
            if (is_array($arg)) {
                $params = $arg;
                unset($config[$pos]);
                break;
            }
        }

        $route['route'] = $routePath;
        $route += $config;

        if (!is_null($params)) {
            $route['params'] = $params;
        }

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

    public function getMatchedRoute()
    {
        return $this->matchedRoute;
    }

    public function getMatchedRouteParams()
    {
        return $this->matchedRouteParams;
    }

    public function getMatchedRouteName()
    {
        return $this->routes[$this->matchedRouteName];
    }

    public function match(string $url) : bool
    {
        $urlPieces = explode('/', $url);
        foreach ($this->routes as $routeName => $route) {
            $routePieces = explode('/', $route['route']);
            $actualUrlVars = [];
            $actualUrlParams = [];
            foreach ($routePieces as $pos => $segment) {
                if (substr($segment, 0, 1) != $this->urlVar) {
                    $actualUrlParams[$pos] = $segment;
                    continue;
                }

                $actualUrlVars[$pos] = substr($segment, 1);

                if (array_key_exists(0, $route)) {
                    $actualUrlParams[$pos] = $route[0][$segment];
                }
            }

            $params = [];
            foreach ($actualUrlVars as $varPos => $var) {
                $params[$var] = $urlPieces[$varPos];
            }

            $actualUrl = implode('/', $actualUrlParams);
            if (preg_match($this->regexDelimiter . '^' . $actualUrl . '$' . $this->regexDelimiter, $url)) {
                unset($route['route'], $route[0]);

                $this->matchedRoute = $route;
                $this->matchedRouteParams = $params;
                $this->matchedRouteName = $routeName;
                return true;
            }
        }

        return false;
    }

    public function getUrl(string $name, array $params = null)
    {
        if ($this->isSearchEngineFriendly()) {
            return $this->getFriendlyUrlForRoute($name, $params);
        }

        return $this->getUrlForRoute($name, $params);
    }

    private function getFriendlyUrlForRoute(string $name, array $params = null) : string
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
            if ($k = array_search(':' . $key, $url)) {
                $url[$k] = $value;
                // $urlRegex[$k] = $arrayUrlParams[':' . $key];
            }
        }

        $url = implode('/', $url);

        // TODO
        // $urlRegex = implode('/', $urlRegex);
        // Maybe validate the URL pattern against $urlRegex?

        return $url;
    }

    private function getUrlForRoute(string $name, array $params = null) : string
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
        return $this->searchEngineFriendly;
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
}
