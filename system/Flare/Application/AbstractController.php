<?php

namespace Flare\Application;

use Flare\Application\Http\Response;
use Flare\Application\Http\Request;
use Flare\View\Response\Html;
use Flare\Util\Collection;
use Flare\Application\Db;
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

    /**
     * 
     * @param \Flare\Application\Http\Request $request
     * @param \Flare\Application\Http\Response $response
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
     * @param string $key
     * @return \Flare\Application\AbstractController
     */
    public function setDatabase($key = 'default')
    {
        $this->db = Db::getConnection($key);
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
            return Db::getConnection($key);
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
            $url = $this->uri->baseUrl.ltrim($url, '/');
        }
        $this->response->setRedirect($url, $code)->send(false);
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
     * @param array $data
     * @param string|boolean $layout
     * @return \Flare\View\Response\Html
     */
    public function view($path, $data = array(), $layout = null)
    {
        $module = $this->request->getModule();
        if ($layout === null 
            && isset($this->config->layout[$module]) 
            && $this->config->layout[$module]['auto'])
        {
            $layout = F::getApp()->getLayoutsDirectory()
                .$this->config->layout[$module]['layout'].'_layout';
        } elseif ($layout !== false && $layout !== null) {
            $layout = F::getApp()->getLayoutsDirectory().$layout.'_layout';
        }

        $html = new Html(F::getApp()->getModuleViewsDirectory().$path);
        $html->setIncludePath(F::getApp()->getModuleViewsDirectory());
        if ($data) {
            $html->setData($data);
        }
        $html->with('session', $this->session)
            ->with('uri', $this->uri)
            ->with('config', $this->config)
            ->with('request', $this->request);
        if ($layout) {
            $html->setLayout($layout);
        }
        return $html;
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