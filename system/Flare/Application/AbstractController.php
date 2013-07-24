<?php

namespace Flare\Application;

use Flare\Application\Http\Request;
use Flare\Http\Response;
use Flare\Flare as F;
use Flare\Http\File;

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
     * @var \Flare\Http\Cookie
     */
    public $cookie;

    /**
     * 
     * @var PDO
     */
    protected $db = null;

    /**
     * 
     * @param \Flare\Application\Http\Request $request
     * @param \Flare\Http\Response $response
     */
    public function __construct(Request &$request, Response &$response)
    {
        $this->session = & F::$session;
        $this->cookie = & F::$cookie;
        $this->config = & F::$config;
        $this->uri = & F::$uri;
        $this->router = & F::$router;
        $this->request = & $request;
        $this->response = & $response;

        if ($this->config->autoload['database']) {
            $this->setDatabase($this->config->autoload['database']);
        }

        if (!empty($this->config->autoload['helpers'])) {
            foreach ($this->config->autoload['helpers'] as $helper) {
                $this->setHelper($helper);
            }
        }
    }

    /**
     * 
     * @param string $key
     * @return \Flare\Http\File|null
     */
    public function getFile($key)
    {
        return File::get($key);
    }

    /**
     * 
     * @param string $key
     * @return \Flare\Http\Files|null
     */
    public function getFiles($key)
    {
        return File::getMultiple($key);
    }

    /**
     * 
     * @param string $service
     * @param array $config
     * @return \Flare\Service
     */
    public function getService($service, $config = array())
    {
        return F::service($service, $config);
    }

    /**
     * 
     * @param string $key
     * @return \Flare\Application\AbstractController
     */
    public function setDatabase($key = 'default')
    {
        $this->db = & F::db($key);
        return $this;
    }

    /**
     * 
     * @param string $key
     * @return \PDO
     */
    public function getDatabase($key = null)
    {
        if ($key) {
            return F::db($key);
        }
        return $this->db ? $this->db : null;
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
    public function setNoSql($key)
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
        if (parse_url($url, PHP_URL_SCHEME) === null) {
            $url = F::$uri->baseUrl.ltrim($url, '/');
        }
        $this->response->setRedirect($url, $code)->send(false);
    }

    /**
     * 
     * @param array $params
     * @return void
     */
    public function back(array $params = array())
    {
        $url = $this->request->server('HTTP_REFERER', false);
        if ($url) {
            if ($params) {
                $parts = parse_url($url);
                if (!isset($parts['query'])) {
                    $parts['query'] = '';
                }
                parse_str($parts['query'], $query);
                $parts['query'] = http_build_query(array_merge($query, $params));
                if (!$parts['query']) {
                    unset($parts['query']);
                }
                $url = http_build_url($parts);
            }
            $this->redirect($url);
        }
    }

    /**
     * 
     * @param string $name
     * @param array $config
     * @return \Flare\Cache
     */
    public function getCache($name, $config = array())
    {
        return F::cache($name, $config);
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
    public function predispatch() {}

    /**
     * 
     * @return void
     */
    public function postdispatch() {}
}