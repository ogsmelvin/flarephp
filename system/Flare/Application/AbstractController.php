<?php

namespace Flare\Application;

use Flare\Application\Http\Response;
use Flare\Application\Http\Request;
use Flare\View\Response\Html;
use Flare\View\Response\Json;
use Flare\View\Response\Xml;
use Flare\Db\Sql\Connection;
use Flare\Util\Collection;
use Flare\Flare as F;
use Flare\Http\File;
use \stdClass;

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
     * @var \Flare\Application\Http\Response
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
     * @var \Flare\Db\Sql\Driver
     */
    protected $db;
    
    public function __construct()
    {
        $this->uri = & F::$uri;
        $this->cookie = & F::$cookie;
        $this->config = & F::$config;
        $this->router = & F::$router;
        $this->session = & F::$session;
        $this->request = & F::$request;
        $this->response = & F::$response;
        $this->data = new stdClass();

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
     * @param string $key
     * @return \Flare\Application\AbstractController
     */
    public function setDatabase($key = 'default')
    {
        $this->db = $this->getDatabase($key);
        return $this;
    }

    /**
     * 
     * @param string $key
     * @return \Flare\Db\Sql\Driver
     */
    public function getDatabase($key = null)
    {
        if ($key) {
            if (!isset($this->config->database[$key])) {
                show_error("'{$key}' doesn't exists in database configuration");
            }
            return Connection::create($key, $this->config->database[$key]);
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
     * @param boolean $switch
     * @return \Flare\Application\AbstractController
     */
    public function autoLayout($switch)
    {
        $this->config->set('layout.'.$this->request->getModule().'.auto', $switch);
        return $this;
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
    public function redirectUrl($url, $code = 302)
    {
        if (parse_url($url, PHP_URL_SCHEME) === null) {
            $url = $this->uri->baseUrl.ltrim($url, '/');
            if (!empty($this->config->router['url_suffix'])) {
                $url .= '.'.$this->config->router['url_suffix'];
            }
        }
        $this->response->setRedirect($url, $code)->send(false);
    }

    /**
     * 
     * @param string|array $class
     * @param int $code
     * @return void
     */
    public function redirect($class, $code = 302)
    {
        if (!$class) return;

        $strClass = '';
        if (is_string($class)) {
            $class = explode('.', trim($class), 3);
        }

        $count = count($class);
        if ($count === 2) {
            $strClass = $this->request->getModule().'.'.implode('.', $class);
        } elseif ($count === 1) {
            $strClass = $this->request->getModule()
                .'.'.$this->request->getController()
                .'.'.implode('.', $class);
        }

        foreach ($this->config->router['routes'] as $url => $route) {
            
        }
    }

    /**
     * 
     * @param array $params
     * @return void|boolean
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
        return false;
    }

    /**
     * 
     * @param string $path
     * @param array|null $data
     * @return \Flare\View\Response\Html
     */
    public function view($path, $data = null)
    {
        $html = new Html($path);
        $html->setIncludePath(F::getApp()->getModuleViewsDirectory())
            ->setLayoutPath(F::getApp()->getLayoutsDirectory());
        $data = $data !== null ? $data : (array) $this->data;
        if ($data) {
            $html->setData($data);
        }
        return $html->with('session', $this->session)
            ->with('uri', $this->uri)
            ->with('config', $this->config)
            ->with('request', $this->request);
    }

    /**
     * 
     * @param \Flare\Object\Json|string|array $json
     * @return \Flare\View\Response\Json
     */
    public function viewAsJson($json)
    {
        return new Json($json);
    }

    /**
     * 
     * @param \Flare\Object\Xml|string|array $xml
     * @return \Flare\View\Response\Xml
     */
    public function viewAsXml($xml)
    {
        return new Xml($xml);
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