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
    private $base;

    /**
     *
     * @var string
     */
    private $index;

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
    private $current;

    /**
     * 
     * @var string
     */
    private $full;

    /**
     * 
     * @var string
     */
    private $module;

    /**
     * 
     * @var string
     */
    private $suffix;

    /**
     * 
     * @var boolean
     */
    private $_valid = true;

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
        $this->index = pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_BASENAME);
        $this->base = str_replace($this->index, '', $_SERVER['SCRIPT_NAME']);
        $this->port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : self::DEFAULT_PORT;
        if (strpos($_SERVER['REQUEST_URI'], $this->base) !== 0) {
            $this->base = '/';
        }
        $search = array('?'.$_SERVER['QUERY_STRING']);
        if ($this->base !== '/') {
            $search[] = $this->base;
        }
        if ($this->index) {
            $search[] = $this->index;
        }
        $this->_uri = '/'.ltrim(str_replace($search, '', $_SERVER['REQUEST_URI']), '/');
        $valid = UriSec::validate($this->_uri, $this->_segments);
        if (!$valid) {
            $this->_valid = false;
            return;
        }
        $this->suffix = pathinfo($this->_uri, PATHINFO_EXTENSION);
        
        $this->protocol = 'http://';
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
            $this->protocol = 'https://';
        }

        $this->host = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
        if ($this->port == self::DEFAULT_PORT || $this->protocol == 'https://') {
            $this->base = $this->protocol.$this->host.$this->base;
        } else {
            $this->base = $this->protocol.$this->host.':'.$this->port.$this->base;
        }

        $this->current = $this->base.ltrim($this->_uri, '/');
        if (!empty($_SERVER['QUERY_STRING'])) {
            $this->full = $this->current.'?'.$_SERVER['QUERY_STRING'];
        } else {
            $this->full = $this->current;
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
     * @return boolean
     */
    public function isValid()
    {
        return $this->_valid;
    }

    /**
     * 
     * @return \Flare\Http\Uri
     */
    public function setModuleUrl()
    {
        $this->module = F::$request->getModule();
        if ($this->module !== F::$config->router['default_module']) {
            $this->module .= '/';
        }
        $this->module = $this->base.$this->module;
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