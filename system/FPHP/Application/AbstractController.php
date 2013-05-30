<?php

namespace FPHP\Application;

use FPHP\Application\Http\Request;
use FPHP\Http\Response;
use FPHP\Fphp as A;
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
     * @var \FPHP\Http\Session
     */
    protected $session;

    /**
     * 
     * @var \FPHP\Application\Config
     */
    protected $config;

    /**
     * 
     * @var \FPHP\Application\Http\Request
     */
    protected $request;

    /**
     * 
     * @var \FPHP\Http\Response
     */
    protected $response;

    /**
     * 
     * @var \FPHP\Http\Uri
     */
    protected $uri;

    /**
     * 
     * @var array
     */
    private $_services = array();

    /**
     * 
     * @param \FPHP\Application\Http\Request $request
     * @param \FPHP\Http\Response $response
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
        if(!isset($this->_services[$shortKey])){
            if(A::service($service)){
                $this->_services[$shortKey] = $service;
            } else {
                display_error("Error initializing service '{$service}'");
            }
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
     * @return mixed
     */
    public function getPost($key = null)
    {
        return $this->request->post($key);
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function getRequest($key = null)
    {
        return $this->request->request($key);
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function getQuery($key = null)
    {
        return $this->request->get($key);
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function getServer($key = null)
    {
        return $this->request->server($key);
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
        $tmpKey = strtolower($key);
        if(!isset($this->{$tmpKey})){
            $ns = & A::ns($key);
            if(!$ns){
                display_error("Initialize nosql '{$key}' failed");
            }
            $this->{$tmpKey} = & $ns;
        }
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
     * @return string
     */
    public function getAppDirectory()
    {
        return A::mvc()->getAppDirectory();
    }

    /**
     * 
     * @param string $url
     * @param int $code
     * @return void
     */
    public function redirect($url, $code = 302)
    {
        $this->response->redirect($url, $code);
    }

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