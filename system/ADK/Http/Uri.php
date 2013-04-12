<?php

namespace ADK\Http;

use ADK\Adk as A;

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
    private $_baseUrl;

    /**
     *
     * @var string
     */
    private $_indexPage;

    /**
     *
     * @var string
     */
    private $_uri;

    /**
     * 
     * @var string
     */
    private $_protocol;

    /**
     * 
     * @var string
     */
    private $_host;

    /**
     * 
     * @var string
     */
    private $_port;

    /**
     * 
     * @var string
     */
    private $_currentUrl;

    /**
     * 
     * @var string
     */
    private $_fullUrl;

    public function __construct()
    {
        $this->_setSegments();
    }

    /**
     *
     * @return \ADK\Http\Uri
     */
    private function _setSegments()
    {
        if(!isset($_SERVER['REQUEST_URI']) || !isset($_SERVER['SCRIPT_NAME'])
            || !isset($_SERVER['SCRIPT_FILENAME'])){
            throw new Exception("REQUEST_URI / SCRIPT_NAME was not set");
        }
        $this->_indexPage = pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_BASENAME);
        $this->_baseUrl = str_replace($this->_indexPage, '', $_SERVER['SCRIPT_NAME']);
        $this->_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : '80';
        if(strpos($_SERVER['REQUEST_URI'], $this->_baseUrl) !== 0){
            $this->_baseUrl = '/';
        }
        $search = array('?'.$_SERVER['QUERY_STRING']);
        if($this->_baseUrl !== '/'){
            $search[] = $this->_baseUrl;
        }
        if($this->_indexPage){
            $search[] = $this->_indexPage;
        }
        $uri = str_replace($search, '', $_SERVER['REQUEST_URI']);
        $uri = '/'.ltrim($uri, '/');
        $this->_uri = $uri;
        $this->_segments = explode('/', $uri);

        $this->_protocol = 'http://';
        if(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on'){
            $this->_protocol = 'https://';
        }

        $this->_host = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
        if($this->_port == '80' || $this->_protocol == 'https://'){
            $this->_baseUrl = $this->_protocol.$this->_host.$this->_baseUrl;
        } else {
            $this->_baseUrl = $this->_protocol.$this->_host.':'.$this->_port.$this->_baseUrl;
        }

        $this->_currentUrl = $this->_baseUrl.ltrim($uri, '/');
        if(!empty($_SERVER['QUERY_STRING'])){
            $this->_fullUrl = $this->_currentUrl.'?'.$_SERVER['QUERY_STRING'];
        } else {
            $this->_fullUrl = $this->_currentUrl;
        }
        unset($uri, $search);
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getPort()
    {
        return $this->_port;
    }

    /**
     * 
     * @return boolean
     */
    public function valid()
    {
        //TODO
        return true;
    }

    /**
     *
     * @return array
     */
    public function getSegments($start = null)
    {
        if($start !== null){
            return array_slice($this->_segments, $start);
        }
        return $this->_segments;
    }

    /**
     * 
     * @return string
     */
    public function getHost()
    {
        return $this->_host;
    }

    /**
     *
     * @param int $index
     * @return string
     */
    public function getSegment($index)
    {
        if(!empty($this->_segments[$index])){
            return $this->_segments[$index];
        }
        return null;
    }

    /**
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * 
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_currentUrl;
    }

    /**
     *
     * @return string
     */
    public function getLandingPage()
    {
        return $this->_indexPage;
    }

    /**
     *
     * @return int
     */
    public function getSegmentCount()
    {
        return count($this->_segments);
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
     * @return string
     */
    public function getProtocol()
    {
        return $this->_protocol;
    }

    /**
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->_fullUrl;
    }

    /**
     * 
     * @return boolean
     */
    public function isHttps()
    {
        return ($this->_protocol === 'https://');
    }

    /**
     * 
     * @return void
     */
    public function requireHttps($code = 301)
    {
        if(!$this->isHttps()){
            $url = 'https://'.$this->_host;
            //TODO: what if not port 80
            // if($this->_port != '80' ){
            //     $url .= ':'.$this->_port;
            // }
            $url .= $this->_uri;
            if(!empty($_SERVER['QUERY_STRING'])){
                $url .= '?'.$_SERVER['QUERY_STRING'];
            }
            A::$response->redirect($url, $code);
        }
        return;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getURIString();
    }
}