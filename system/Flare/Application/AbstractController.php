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
     * @return \Flare\Application\AbstractController
     */
    public function disableCache()
    {
        $this->response->setHeader('Expires: Mon, 26 Jul 1997 05:00:00 GMT')
            ->setHeader('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT')
            ->setHeader('Cache-Control: no-store, no-cache, must-revalidate')
            ->setHeader('Cache-Control: post-check=0, pre-check=0', false)
            ->setHeader('Pragma: no-cache');
        return $this;
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
        $helper = ucwords(strtolower($helper));
        if (file_exists(FLARE_DIR.'Flare/Helper/'.$helper.'.php')) {
            require_once FLARE_DIR.'Flare/Helper/'.$helper.'.php';
        } elseif (file_exists(F::getApp()->getModuleHelpersDirectory().$helper.'.php')) {
            require_once F::getApp()->getModuleHelpersDirectory().$helper.'.php';
        }
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
     * @return string
     */
    public function getBaseDirectory()
    {
        return F::getApp()->getBaseDirectory();
    }

    /**
     *
     * @param string $url
     * @param int $code
     * @return void
     */
    public function gotoUrl($url, $code = 302)
    {
        if (parse_url($url, PHP_URL_SCHEME) === null) {
            $url = $this->uri->base.trim($url, '/');
            if (!empty($this->config->router['url_suffix'])) {
                $url .= '.'.$this->config->router['url_suffix'];
            }
        }

        $this->response->setRedirect($url, $code)->send(false);
    }

    /**
     * 
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param int $code
     * @return void
     */
    public function gotoAction($action, $controller = null, $module = null, $code = 302)
    {
        if (!$action) return;

        $urlRedirect = '';
        $strClass = ($module ? $module : $this->request->getModule())
            .'.'.($controller ? $controller : $this->request->getController())
            .'.'.$action;

        if (!empty($this->config->router['routes'])) {
            foreach ($this->config->router['routes'] as $urlRoute => $route) {
                if ($route === $strClass) {
                    $urlRedirect = $this->uri->base.ltrim($urlRoute, '/');
                    break;
                }
            }
        }

        if (!$urlRedirect) {

            $strClass = explode('.', $strClass);
            if ($strClass[0] === $this->config->router['default_module']) unset($strClass[0]);

            $isDefaultCtrl = ($strClass[1] === $this->config->router['default_controller']);
            $isDefaultAction = ($strClass[2] === $this->config->router['default_action']);

            if (!$isDefaultCtrl && $isDefaultAction) unset($strClass[2]);
            elseif ($isDefaultCtrl && $isDefaultAction) unset($strClass[1], $strClass[2]);

            $urlRedirect = trim(implode('/', $strClass), '/');

            if (!empty($this->config->router['url_suffix']) && $urlRedirect && isset($strClass[2])) {
                $urlRedirect .= '.'.$this->config->router['url_suffix'];
            }
            $urlRedirect = $this->uri->base.$urlRedirect;

        }

        if ($urlRedirect) $this->response->setRedirect($urlRedirect, $code)->send(false);
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
     * @param string $action
     * @param string $controller
     * @param string $module
     * @return void
     */
    public function forward($action, $controller = null, $module = null)
    {
        if (!$action) return;
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
            ->setLayoutPath(F::getApp()->getModuleLayoutsDirectory());
        $data = $data !== null ? $data : (array) $this->data;
        if ($data) {
            $html->setData($data);
        }
        return $html->with('session', $this->session)
            ->with('cookie', $this->cookie)
            ->with('uri', $this->uri)
            ->with('config', $this->config)
            ->with('request', $this->request);
    }

    /**
     * 
     * @param \Flare\Object\Json|\Flare\Util\Collection\|\ArrayObject|string|arraystring|array $json
     * @return \Flare\View\Response\Json
     */
    public function viewAsJson($json)
    {
        return !($json instanceof Json) ? new Json($json) : $json;
    }

    /**
     * 
     * @param \Flare\Object\Xml|\Flare\Util\Collection\|\ArrayObject|string|array $xml
     * @return \Flare\View\Response\Xml
     */
    public function viewAsXml($xml)
    {
        return !($xml instanceof Xml) ? new Xml($xml) : $xml;
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