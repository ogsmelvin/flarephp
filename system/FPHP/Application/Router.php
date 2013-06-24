<?php

namespace FPHP\Application;

use FPHP\Application\Http\Request;
use FPHP\Fphp as F;

/**
 * 
 * @author anthony
 * 
 */
class Router
{
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
        $this->addRoutes($routes);
    }

    /**
     * 
     * @param array $modules
     * @return \FPHP\Application\Router
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
     * @return \FPHP\Application\Router
     */
    public function addRoute($url, $method)
    {
        $this->_routes[$url] = $method;
        return $this;
    }

    /**
     * 
     * @param array $routes
     * @return \FPHP\Application\Router
     */
    public function addRoutes(array $routes)
    {
        foreach($routes as $key => $route){
            $this->addRoute($key, $route);
        }
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getRoute()
    {
        $route = null;
        $uri = F::$uri->getURIString();
        if(isset($this->_routes[$uri])){
            $route = $this->_routes[$uri];
        }
        return $route;
    }

    /**
     * 
     * @return \FPHP\Application\Http\Request
     */
    public function getRouteRequest()
    {
        $route = $this->getRoute();
        $module = F::$uri->getSegment(1);
        $controller = F::$uri->getSegment(2);
        $action = F::$uri->getSegment(3);
        if($route){
            list($module, $controller, $action) = explode('.', $route);
        } else if($module === null){
            $module = F::$config->router['default_module'];
            $action = F::$config->router['default_action'];
            $controller = F::$config->router['default_controller'];
        } else if(!in_array($module, $this->_routeModules)){
            $action = $controller;
            $controller = $module;
            $module = F::$config->router['default_module'];
        }

        // if(F::$config->router['url_suffix']){
        //     if($action && F::$uri->getSuffix() !== F::$config->router['url_suffix']){
        //         display_error(404);
        //     }

        //     $action = rtrim($action, '.'.F::$config->router['url_suffix']);
        // }

        $controller = $controller === null ? F::$config->router['default_controller'] : $controller;
        $action = $action === null ? F::$config->router['default_action'] : $action;

        $request = new Request();
        $request->setModule($module)
            ->setController($controller)
            ->setAction($action);
        unset($module, $controller, $action);

        return $request;
    }

    /**
     * 
     * @return void
     */
    public function clearRoutesList()
    {
        $this->_routes = array();
    }

    /**
     * 
     * @return array
     */
    public function getRoutesList()
    {
        return $this->_routes;
    }
}