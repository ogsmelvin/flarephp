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
     * @var string
     */
    private $_current;

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
    public function __construct(Uri &$uri, array $routes = array())
    {
        if($routes){
            $this->addRoutes($routes);
        }
        $this->_uri = & $uri;
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
        $uri = (string) $this->_uri;
        if(!isset($this->_routes[$uri])){
            return null;
        } else {
            
        }
        return $this->_routes[$uri];
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