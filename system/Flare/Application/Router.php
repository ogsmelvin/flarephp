<?php

namespace Flare\Application;

use Flare\Application\Router\Route\Action;
use Flare\Application\Http\Response;
use Flare\Application\Http\Request;
use Flare\Application\Router\Route;
use Flare\Flare as F;

/**
 * 
 * @author anthony
 * 
 */
class Router
{
    /**
     * 
     * @var \Flare\Application\Router\Route
     */
    private $_currentRoute;

    /**
     * 
     * @var array
     */
    private $_routes = array();

    /**
     * 
     * @var array
     */
    private $_routeModules = array();

    /**
     * 
     * @param array $routes
     */
    public function __construct(array $routes = array())
    {
        if ($routes) {
            $this->addRoutes($routes);
        }
    }

    /**
     * 
     * @param array $modules
     * @return \Flare\Application\Router
     */
    public function setRoutingModules($modules)
    {
        $this->_routeModules = $modules;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getRoutingModules()
    {
        return $this->_routeModules;
    }

    /**
     * 
     * @param string $url
     * @param string $method
     * @return \Flare\Application\Router
     */
    public function addRoute($url, $method)
    {
        $this->_routes[trim($url, '/')] = $method;
        return $this;
    }

    /**
     * 
     * @param array $routes
     * @return \Flare\Application\Router
     */
    public function addRoutes(array $routes)
    {
        foreach ($routes as $key => $route) {
            $this->addRoute($key, $route);
        }
        return $this;
    }

    /**
     * 
     * @return string|null
     */
    private function _getMatchedCustomRoute()
    {
        $uri = trim((string) F::$uri, '/');
        if (isset($this->_routes[$uri])) {
            return $this->_routes[$uri];
        } else {
            foreach ($this->_routes as $key => $class) {
                if (preg_match('#^'.$key.'$#', $uri)) {
                    return $class;
                }
            }
        }
        return null;
    }

    /**
     * 
     * @return \Flare\Application\Router\Route|null
     */
    public function getMatchedCustomRoute()
    {
        if ($this->_currentRoute) {
            return $this->_currentRoute;
        }

        $route = $this->_getMatchedCustomRoute();
        if ($route) {
            // list($route, $params) = explode(':', $route, 2);
            // if ($params) {
            //     $params = explode(',', ltrim(rtrim($params, ')'), '('));
            // }
            list($module, $controller, $action) = explode('.', $route, 3);
            $route = $this->_route($module, $controller, $action);
            if ($route) {
                $this->_currentRoute = $route;
            }
        }

        return $this->_currentRoute;
    }

    /**
     * 
     * @param string $module
     * @param string $controller
     * @param string $action
     * @param array $params
     * @return \Flare\Application\Router\Route
     */
    private function _route($module, $controller, $action, $params = array())
    {
        $request = new Request();
        $request->setModule($module)
            ->setController($controller)
            ->setAction($action);

        $path = F::getApp()->getModulesDirectory()
            .$request->getModule()
            .'/'
            .F::getApp()->getControllersDirectory()
            .strtolower(urldecode($request->getController()))
            .'.php';
        if (!file_exists($path)) {
            return null;
        }

        require_once F::getApp()->getModulesDirectory().$request->getModule().'/bootstrap.php';
        require_once $path;
        
        $controller = ucwords($request->getModule())."\\Controllers\\".$request->getControllerClassName();
        $route = new Route();
        $route->setModule($request->getModule());
        $route->setController(new $controller($request, new Response()));
        $route->setAction(new Action($route->getController(), $request->getActionMethodName()));
        if ($params) {
            $route->setActionParams($params);
        }
        
        return $route;
    }

    /**
     * 
     * @param string $class
     * @return \Flare\Application\Router\Route|null
     */
    public function getErrorRoute($class)
    {
        $class = explode('.', $class);
        $module = isset($class[0]) ? $class[0] : F::$config->router['default_module'];
        $controller = isset($class[1]) ? $class[1] : F::$config->router['default_controller'];
        $action = isset($class[2]) ? $class[2] : F::$config->router['default_action'];
        $this->_currentRoute = $this->_route($module, $controller, $action);
        return $this->_currentRoute ? $this->_currentRoute : null;
    }

    /**
     * 
     * @return \Flare\Application\Router\Route|null
     */
    public function getRoute()
    {
        if ($this->_currentRoute) {
            return $this->_currentRoute;
        }

        $customRoute = $this->_getMatchedCustomRoute();
        $module = F::$uri->getSegment(1);
        $controller = F::$uri->getSegment(2);
        $action = F::$uri->getSegment(3);

        if ($customRoute) {
            list($module, $controller, $action) = explode('.', $customRoute, 3);
        } elseif ($module === null) {
            $module = F::$config->router['default_module'];
            $action = F::$config->router['default_action'];
            $controller = F::$config->router['default_controller'];
        } elseif (!in_array($module, $this->_routeModules)) {
            $action = $controller;
            $controller = $module;
            $module = F::$config->router['default_module'];
        }

        $controller = $controller === null ? F::$config->router['default_controller'] : $controller;
        $action = $action === null ? F::$config->router['default_action'] : $action;

        $route = $this->_route($module, $controller, $action);
        if ($route) {
            if (!$customRoute) {
                $this->_setActionParams($route, $validUriForParams);
                if (!$validUriForParams) {
                    return null;
                }
            } elseif (!$route->getAction()->exists()) {
                return null;
            }
            $this->_currentRoute = $route;
        }

        return $this->_currentRoute;
    }

    /**
     * 
     * @param \Flare\Application\Router\Route
     * @return void
     */
    private function _setActionParams(Route &$route, &$validUriForParams = null)
    {
        $actionParams = array();
        if (!$route->getAction()->exists()) {
            $validUriForParams = false;
            return;
        } else {

            $segmentCount = F::$uri->getSegmentCount();
            $firstSegment = F::$uri->getSegment(1);
            $params = $route->getAction()->getParameters();
            $indexStart = 3;
            if ($params) {
                if ($firstSegment) {
                    if (in_array($firstSegment, $this->_routeModules)) {
                        $indexStart = 4;
                    }
                } else {
                    $validUriForParams = false;
                    return;
                }
                if (!$params[0]->isOptional() && $segmentCount < $indexStart) {
                    $validUriForParams = false;
                    return;
                }

                $i = $indexStart;
                foreach ($params as $param) {
                    if ($i <= $segmentCount) {
                        if ($segmentValue = F::$uri->getSegment($i++)) {
                            $actionParams[] = $segmentValue;
                        }
                    }
                }
                
                $segmentParamsCount = ($segmentCount - $indexStart) + 1;
                $segmentParamsCount = $segmentParamsCount < 0 ? 1 : $segmentParamsCount;
                if ($segmentParamsCount > $route->getAction()->getNumberOfParameters()
                    || $segmentParamsCount < $route->getAction()->getNumberOfRequiredParameters())
                {
                    $validUriForParams = false;
                    return;
                }
            } else {
                if ($firstSegment && in_array($firstSegment, $this->_routeModules)) {
                    $indexStart = 4;
                }
                if ($segmentCount >= $indexStart) {
                    $validUriForParams = false;
                    return;
                }
            }
            unset($params, $indexStart, $segmentCount, $firstSegment);
        }
        
        $validUriForParams = true;
        $route->setActionParams($actionParams);
    }

    /**
     * 
     * @return \Flare\Application\Router
     */
    public function clearRoutesList()
    {
        $this->_routes = array();
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getRoutesList()
    {
        return $this->_routes;
    }

    /**
     * 
     * @param int $redirectCode
     * @return void
     */
    public function secure($redirectCode = 301)
    {
        if (!F::$uri->isHttps()) {
            $url = 'https://'.F::$uri->host;
            $url .= F::$uri;
            if (!empty($_SERVER['QUERY_STRING'])) {
                $url .= '?'.$_SERVER['QUERY_STRING'];
            }
            F::$response->setRedirect($url, $redirectCode)->send(false);
        }
    }
}