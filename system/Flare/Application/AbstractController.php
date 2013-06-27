<?php

namespace Flare\Application;

use Flare\Application\Http\Request;
use Flare\Http\Response;
use Flare\Security\Xss;
use Flare\Flare as F;

/**
 * 
 * @author anthony
 * 
 */
abstract class AbstractController
{
    /**
     * 
     * @var \Flare\Http\Session
     */
    protected $session;

    /**
     * 
     * @var \Flare\Application\Config
     */
    protected $config;

    /**
     * 
     * @var \Flare\Application\Http\Request
     */
    protected $request;

    /**
     * 
     * @var \Flare\Http\Response
     */
    protected $response;

    /**
     * 
     * @var \Flare\Application\Router
     */
    protected $router;

    /**
     * 
     * @var \Flare\Http\Uri
     */
    protected $uri;

    /**
     * 
     * @var array
     */
    private $_services = array();

    /**
     * 
     * @param \Flare\Application\Http\Request $request
     * @param \Flare\Http\Response $response
     */
    public function __construct(Request &$request, Response &$response)
    {
        $this->session = & F::$session;
        $this->config = & F::$config;
        $this->uri = & F::$uri;
        $this->router = & F::$router;
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
     * @return \Flare\Application\AbstractController
     */
    public function setService($service, $shortKey = null)
    {
        if(!$shortKey){
            $shortKey = $service;
        }
        if(!isset($this->_services[$shortKey])){
            if(F::service($service)){
                $this->_services[$shortKey] = $service;
            } else {
                show_error("Error initializing service '{$service}'");
            }
        }
        return $this;
    }

    /**
     * 
     * @param string $service
     * @return mixed
     */
    public function getService($service)
    {
        if(!isset($this->_services[$service])){
            show_error("'{$service}' was not set");
        }
        return F::service($this->_services[$service]);
    }

    /**
     * 
     * @param string $key
     * @param boolean|null $xss
     * @return mixed
     */
    public function getPost($key = null, $xss = null)
    {
        $value = $this->request->post($key);
        if($xss === null){
            if($this->config->get('auto_xss_filter') && $value){
                return Xss::filter($value);
            }
        } else if($value){
            return $xss === true ? Xss::filter($value) : $value;
        }
        return $value;
    }

    /**
     * 
     * @param string $key
     * @param boolean|null $xss
     * @return mixed
     */
    public function getRequest($key = null, $xss = null)
    {
        $value = $this->request->request($key);
        if($xss === null){
            if($this->config->get('auto_xss_filter') && $value){
                return Xss::filter($value);
            }
        } else if($value){
            return $xss === true ? Xss::filter($value) : $value;
        }
        return $value;
    }

    /**
     * 
     * @param string $key
     * @param boolean|null $xss
     * @return mixed
     */
    public function getQuery($key = null, $xss = null)
    {
        $value = $this->request->get($key);
        if($xss === null){
            if($this->config->get('auto_xss_filter') && $value){
                return Xss::filter($value);
            }
        } else if($value){
            return $xss === true ? Xss::filter($value) : $value;
        }
        return $value;
    }

    /**
     * 
     * @param string $key
     * @param boolean|null $xss
     * @return mixed
     */
    public function getServer($key = null, $xss = null)
    {
        $value = $this->request->server($key);
        if($xss === null){
            if($this->config->get('auto_xss_filter') && $value){
                return Xss::filter($value);
            }
        } else if($value){
            return $xss === true ? Xss::filter($value) : $value;
        }
        return $value;
    }

    /**
     * 
     * @param string $key
     * @return \Flare\Application\AbstractController
     */
    public function setDb($key = 'default')
    {
        $this->db = & F::db($key);
        return $this;
    }

    /**
     * 
     * @param string $helper
     * @return \Flare\Application\AbstractController
     */
    public function setHelper($helper)
    {
        F::getApp()->helper($helper);
        return $this;
    }

    /**
     * 
     * @param string $key
     * @return \Flare\Application\AbstractController
     */
    public function setNosql($key)
    {
        $tmpKey = strtolower($key);
        if(!isset($this->{$tmpKey})){
            $ns = & F::ns($key);
            if(!$ns){
                show_error("Initialize nosql '{$key}' failed");
            }
            $this->{$tmpKey} = & $ns;
        }
        return $this;
    }

    /**
     * 
     * @param boolean $switch
     * @return \Flare\Application\AbstractController
     */
    public function setAutoLayout($switch)
    {
        $this->config->set('layout.'.$this->request->getModule().'.auto', $switch);
        return $this;
    }

    /**
     * 
     * @param boolean $switch
     * @return \Flare\Application\AbstractController
     */
    public function setAutoXssFilter($switch)
    {
        $this->config->set('auto_xss_filter', $switch);
        return $this;
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
        return F::getApp()->view($path, $data, $layout);
    }

    /**
     * 
     * @return string
     */
    public function getAppDirectory()
    {
        return F::getApp()->getAppDirectory();
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
     * @return \Flare\Application\Http\Request
     */
    public function getAppRequest()
    {
        return $this->request;
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