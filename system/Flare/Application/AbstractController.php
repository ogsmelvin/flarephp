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
    public $session;

    /**
     * 
     * @var \Flare\Application\Config
     */
    public $config;

    /**
     * 
     * @var \Flare\Application\Http\Request
     */
    public $request;

    /**
     * 
     * @var \Flare\Http\Response
     */
    public $response;

    /**
     * 
     * @var \Flare\Application\Router
     */
    public $router;

    /**
     * 
     * @var \Flare\Http\Uri
     */
    public $uri;

    /**
     * 
     * @var PDO
     */
    public $db = null;

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

        if ($this->config->autoload['database']) {
            $this->database($this->config->autoload['database']);
        }

        if (!empty($this->config->autoload['helpers'])) {
            foreach ($this->config->autoload['helpers'] as $helper) {
                $this->helper($helper);
            }
        }

        if (!empty($this->config->autoload['services'])) {
            foreach ($this->config->autoload['services'] as $service) {
                $this->service($service);
            }
        }
    }

    /**
     * 
     * @param string $service
     * @param array $config
     * @return \Flare\Service
     */
    public function service($service, $config = array())
    {
        return F::service($service, $config);
    }

    /**
     * 
     * @param string $key
     * @param boolean|null $xss
     * @return mixed
     */
    public function post($key = null, $xss = null)
    {
        $value = $this->request->post($key);
        if ($xss === null) {
            if ($this->config->get('auto_xss_filter') && $value) {
                return Xss::filter($value);
            }
        } elseif ($value) {
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
    public function param($key = null, $xss = null)
    {
        $value = $this->request->request($key);
        if ($xss === null) {
            if ($this->config->get('auto_xss_filter') && $value) {
                return Xss::filter($value);
            }
        } elseif ($value) {
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
    public function get($key = null, $xss = null)
    {
        $value = $this->request->get($key);
        if ($xss === null) {
            if ($this->config->get('auto_xss_filter') && $value) {
                return Xss::filter($value);
            }
        } elseif ($value) {
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
    public function server($key = null, $xss = null)
    {
        $value = $this->request->server($key);
        if ($xss === null) {
            if ($this->config->get('auto_xss_filter') && $value) {
                return Xss::filter($value);
            }
        } elseif ($value) {
            return $xss === true ? Xss::filter($value) : $value;
        }
        return $value;
    }

    /**
     * 
     * @param string $key
     * @return \Flare\Application\AbstractController
     */
    public function database($key = 'default')
    {
        $this->db = & F::db($key);
        return $this;
    }

    /**
     * 
     * @param string $helper
     * @return \Flare\Application\AbstractController
     */
    public function helper($helper)
    {
        F::getApp()->helper($helper);
        return $this;
    }

    /**
     * 
     * @param string $key
     * @return \Flare\Application\AbstractController
     */
    public function noSql($key)
    {
        $tmpKey = strtolower($key);
        if (!isset($this->{$tmpKey})) {
            $ns = & F::ns($key);
            if (!$ns) {
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
    public function enableAutoLayout($switch)
    {
        $this->config->set('layout.'.$this->request->getModule().'.auto', $switch);
        return $this;
    }

    /**
     * 
     * @param boolean $switch
     * @return \Flare\Application\AbstractController
     */
    public function enableAutoXssFilter($switch)
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
    public function preDispatch() {}

    /**
     * 
     * @return void
     */
    public function postDispatch() {}
}