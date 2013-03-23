<?php

namespace ADK\Http;

use ADK\Objects\Json;
use ADK\Objects\Xml;
use \Exception;
use ADK\Adk as A;

if(!function_exists('curl_init')){
    display_error('CURL is not supported by your server');
}

if(!function_exists('http_build_url')){
    A::helper('url');
}

/**
 * 
 * @author anthony
 * 
 */
class Curl
{
    /**
     * 
     * @var string
     */
    const POST = 'POST';

    /**
     * 
     * @var string
     */
    const GET = 'GET';

    /**
     * 
     * @var resource
     */
    private $_curl;

    /**
     * 
     * @var array
     */
    private $_params = array();

    /**
     * 
     * @var string
     */
    private $_error = null;

    /**
     * 
     * @var string
     */
    private $_method = 'GET';

    /**
     * 
     * @var string
     */
    private $_url = null;

    /**
     * 
     * @var array
     */
    private $_httpHeaders = array();

    /**
     * 
     * @param string $url
     */
    public function __construct($url = null)
    {
        $this->_curl = curl_init($url);
    }

    /**
     * 
     * @param string $url
     * @return \ADK\Http\Curl
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        curl_setopt($this->_curl, CURLOPT_URL, $url);
        return $this;
    }

    /**
     * 
     * @param string $method
     * @return \ADK\Http\Curl
     */
    public function setRequestMethod($method)
    {
        $method = strtoupper($method);
        if($method === self::POST){
            $this->_method = self::POST;
            curl_setopt($this->_curl, CURLOPT_POST, true);
        } else if($method === self::GET){
            $this->_method = self::GET;
            curl_setopt($this->_curl, CURLOPT_POST, false);
        } else {
            throw new Exception("Invalid request method");
        }
        return $this;
    }

    /**
     * 
     * @param string|array $type
     * @return \ADK\Http\Curl
     */
    public function setContentType($type)
    {
        if(strpos($type, 'Content-Type: ') !== 0){
            $type = 'Content-Type: '.$type;
        }
        return $this->setOption(CURLOPT_HTTPHEADER, $type);
    }

    /**
     * 
     * @param array $params
     * @return \ADK\Http\Curl
     */
    public function setParams($params)
    {
        foreach($params as $k => $v){
            $this->setParam($k, $v);
        }
        return $this;
    }

    /**
     * 
     * @param string $key
     * @param string|int $value
     * @return \ADK\Http\Curl
     */
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
        return $this;
    }

    /**
     * 
     * @param int $key
     * @param mixed $value
     * @return \ADK\Http\Curl
     */
    public function setOption($key, $value)
    {
        if($key === CURLOPT_URL){
            $this->_url = $value;
        } else if($key === CURLOPT_HTTPHEADER){
            if(is_string($value)){
                $value = (array) $value;
                $this->_httpHeaders = $value;
            }
        }
        curl_setopt($this->_curl, $key, $value);
        return $this;
    }

    /**
     * 
     * @param array $options
     * @return \ADK\Http\Curl
     */
    public function setOptions($options)
    {
        if(isset($options[CURLOPT_URL])){
            $this->_url = $options[CURLOPT_URL];
        }
        curl_setopt_array($this->_curl, $options);
        return $this;
    }

    /**
     * 
     * @return \ADK\Http\Curl
     */
    public function execute()
    {
        $this->_prepare();
        curl_exec($this->_curl);
        if(curl_errno($this->_curl)){
            $this->_error = curl_error($this->_curl);
        }
        return $this;
    }

    /**
     * 
     * @return void
     */
    private function _prepare()
    {
        if($this->_method === self::POST){
            curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $this->_params);
        } else if($this->_method === self::GET){
            $url = parse_url($this->_url);
            if(!isset($url['query'])){
                $url['query'] = http_build_query($this->_params);
            }
            $this->_url = http_build_url($url);
            $this->setOption(CURLOPT_URL, $this->_url);
        } else {
            throw new Exception("Invalid request method");
        }
        return;
    }

    /**
     * 
     * @return string
     */
    public function getContent()
    {
        $this->_prepare();
        curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
        $return = (string) curl_exec($this->_curl);
        if(curl_errno($this->_curl)){
            $this->_error = curl_error($this->_curl);
        }
        return $return;
    }

    /**
     * 
     * @return string|null
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function getParam($key)
    {
        return isset($this->_params[$key]) ? $this->_params[$key] : null;
    }

    /**
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * 
     * @return string|null
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * 
     * @return boolean
     */
    public function hasError()
    {
        return !empty($this->_error);
    }

    /**
     * 
     * @return \ADK\Http\Curl
     */
    public function close()
    {
        curl_close($this->_curl);
        return $this;
    }

    /**
     * 
     * @return \ADK\Http\Json
     */
    public function getContentAsJson()
    {
        return new Json($this->getContent());
    }

    /**
     * 
     * @return \ADK\Http\Xml
     */
    public function getContentAsXml()
    {
        return new Xml($this->getContent());
    }
}