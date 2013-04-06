<?php

namespace ADK\Application;

use ADK\Application\Http\Request;
use ADK\Http\Response;
use ADK\Adk as A;
use \Exception;

/**
 * 
 * @author anthony
 * 
 */
abstract class AbstractController
{
    /**
     * 
     * @var \ADK\Http\Session
     */
    protected $session;

    /**
     * 
     * @var \ADK\Application\Config
     */
    protected $config;

    /**
     * 
     * @var \ADK\Application\Http\Request
     */
    protected $request;

    /**
     * 
     * @var \ADK\Http\Response
     */
    protected $response;

    /**
     * 
     * @var \ADK\Http\Uri
     */
    protected $uri;

    /**
     * 
     * @var array
     */
    private $_services = array();

    /**
     * 
     * @param \ADK\Application\Http\Request $request
     * @param \ADK\Http\Response $response
     */
    public function __construct(Request &$request, Response &$response)
    {
        $this->session = & A::$session;
        $this->config = & A::$config;
        $this->uri = & A::$uri;
        $this->request = & $request;
        $this->response = & $response;

        if($this->config->autoload['database']){
            $this->setDb($this->config->autoload['database']);
        }

        if(!empty($this->config->autoload['helpers'])){
            foreach($this->config->autoload['helpers'] as $helper){
                $this->setHelper($helper);
            }
        }

        if(!empty($this->config->autoload['services'])){
            foreach($this->config->autoload['services'] as $service){
                if(!is_array($service)){
                    $this->setService($service);
                } else if(isset($service[0], $service[1])){
                    $this->setService($service[0], $service[1]);
                }
            }
        }
    }

    /**
     * 
     * @param string $service
     * @param string $shortKey
     * @return void
     */
    public function setService($service, $shortKey = null)
    {
        if(!$shortKey){
            $shortKey = $service;
        }
        if(A::service($service)){
            $this->_services[$shortKey] = $service;
        } else {
            display_error("Error initializing service '{$service}'");
        }
    }

    /**
     * 
     * @param string $service
     * @return mixed
     */
    public function getService($service)
    {
        if(!isset($this->_services[$service])){
            display_error("'{$service}' was not set");
        }
        return A::service($this->_services[$service]);
    }

    /**
     * 
     * @param string $key
     * @return void
     */
    public function setDb($key = 'default')
    {
        $this->db = & A::db($key);
    }

    /**
     * 
     * @param string $helper
     * @return void
     */
    public function setHelper($helper)
    {
        A::mvc()->helper($helper);
    }

    /**
     * 
     * @param string $key
     * @return void
     */
    public function setNosql($key)
    {
        $this->{$key} = A::ns($key);
    }

    /**
     * 
     * @param string $path
     * @param array $data
     * @param string|boolean $layout
     * @return mixed
     */
    public function view($path, $data = null, $layout = null)
    {
        return A::mvc()->view($path, $data, $layout);
    }

    /**
     * 
     * @param string $name
     * @param boolean $instance
     * @return mixed|boolean
     */
    // public function model($name, $instance = true)
    // {
    //     return A::mvc()->model($name, $instance);
    // }

    /**
     * 
     * @return void
     */
    abstract public function init();

    /**
     * 
     * @return void
     */
    abstract public function complete();

    /**
     * 
     * @return void
     */
    public function preDispatch(){}

    /**
     * 
     * @return void
     */
    public function postDispatch(){}
}