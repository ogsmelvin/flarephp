<?php

namespace FPHP\Application;

use FPHP\Http\Uri;
/**
 * 
 * @author anthony
 * 
 */
class Router
{
    /**
     * 
     * @var \FPHP\Http\Uri
     */
    private $_uri;

    /**
     * 
     * @var array
     */
    private $_routes = array();

    /**
     * 
     * @param array $routes
     */
    public function __construct(Uri $uri, array $routes = array())
    {
        if($routes){
            $this->addRoutes($routes);
        }
        $this->_uri = & $uri;
    }

    /**
     * 
     * @param string $url
     * @param string $controller
     * @return \FPHP\Application\Router
     */
    public function addRoute($url, $controller)
    {
        return $this;
    }

    /**
     * 
     * @param array $routes
     * @return \FPHP\Application\Router
     */
    public function addRoutes($routes)
    {
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getRoute()
    {
        return $this->_controller
    }

    /**
     * 
     * @return void
     */
    public function clearRoutes()
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