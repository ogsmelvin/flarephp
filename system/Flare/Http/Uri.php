<?php

namespace Flare\Http;

use Flare\Security\Uri as UriSec;
use Flare\Flare as F;

/**
 *
 * @author anthony
 *
 */
class Uri
{
    /**
     *
     * @var array
     */
    private $_segments;

    /**
     *
     * @var string
     */
    private $baseUrl;

    /**
     *
     * @var string
     */
    private $indexPage;

    /**
     *
     * @var string
     */
    private $_uri;

    /**
     * 
     * @var string
     */
    private $protocol;

    /**
     * 
     * @var string
     */
    private $host;

    /**
     * 
     * @var string
     */
    private $port;

    /**
     * 
     * @var string
     */
    private $currentUrl;

    /**
     * 
     * @var string
     */
    private $fullUrl;

    /**
     * 
     * @var string
     */
    private $moduleUrl;

    /**
     * 
     * @var string
     */
    private $suffix;

    /**
     * 
     * @var string
     */
    const DEFAULT_PORT = '80';

    public function __construct()
    {
        $this->_setSegments();
    }

    /**
     *
     * @return \Flare\Http\Uri
     */
    private function _setSegments()
    {
        if (!isset($_SERVER['REQUEST_URI']) || !isset($_SERVER['SCRIPT_NAME'])
            || !isset($_SERVER['SCRIPT_FILENAME'])) {
            show_error("REQUEST_URI / SCRIPT_NAME was not set");
        }
        $this->indexPage = pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_BASENAME);
        $this->baseUrl = str_replace($this->indexPage, '', $_SERVER['SCRIPT_NAME']);
        $this->port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : self::DEFAULT_PORT;
        if (strpos($_SERVER['REQUEST_URI'], $this->baseUrl) !== 0) {
            $this->baseUrl = '/';
        }
        $search = array('?'.$_SERVER['QUERY_STRING']);
        if ($this->baseUrl !== '/') {
            $search[] = $this->baseUrl;
        }
        if ($this->indexPage) {
            $search[] = $this->indexPage;
        }
        $this->_uri = '/'.ltrim(str_replace($search, '', $_SERVER['REQUEST_URI']), '/');
        $valid = UriSec::validate($this->_uri, $this->_segments);
        if (!$valid) {
            show_response(400);
        }
        $this->suffix = pathinfo($this->_uri, PATHINFO_EXTENSION);
        
        $this->protocol = 'http://';
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
            $this->protocol = 'https://';
        }

        $this->host = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
        if ($this->port == self::DEFAULT_PORT || $this->protocol == 'https://') {
            $this->baseUrl = $this->protocol.$this->host.$this->baseUrl;
        } else {
            $this->baseUrl = $this->protocol.$this->host.':'.$this->port.$this->baseUrl;
        }

        $this->currentUrl = $this->baseUrl.ltrim($this->_uri, '/');
        if (!empty($_SERVER['QUERY_STRING'])) {
            $this->fullUrl = $this->currentUrl.'?'.$_SERVER['QUERY_STRING'];
        } else {
            $this->fullUrl = $this->currentUrl;
        }

        unset($uri, $search);
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getSegments($start = null)
    {
        if ($start !== null) {
            return array_slice($this->_segments, $start);
        }
        return $this->_segments;
    }

    /**
     *
     * @param int $index
     * @return string
     */
    public function getSegment($index)
    {
        if (!empty($this->_segments[$index])) {
            return $this->_segments[$index];
        }
        return null;
    }

    /**
     *
     * @return int
     */
    public function getSegmentCount()
    {
        $length = count($this->_segments);
        if (empty($this->_segments[$length - 1])) {
            $length--;
        }
        return --$length;
    }

    /**
     *
     * @return string
     */
    public function getURIString()
    {
        return $this->_uri;
    }

    /**
     * 
     * @return boolean
     */
    public function isHttps()
    {
        return ($this->protocol === 'https://');
    }

    /**
     * 
     * @return \Flare\Http\Uri
     */
    public function setModuleUrl()
    {
        $this->moduleUrl = F::getApp()->getController()->request->getModule();
        if ($this->moduleUrl !== F::$config->router['default_module']) {
            $this->moduleUrl .= '/';
        }
        $this->moduleUrl = $this->baseUrl.$this->moduleUrl;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getURIString();
    }

    /**
     * 
     * @param string
     * @return string
     */
    public function __get($key)
    {
        if (strpos($key, '_') !== false) {
            show_error('URI Class. Unable to access property.');
        }
        return isset($this->{$key}) ? $this->{$key} : show_error('URI Class. Unknown property.');
    }
}