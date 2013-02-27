<?php

namespace ADK\Http;

if(!function_exists('curl_init')){
    display_error('CURL is not supported by your server');
}

use ADK\Objects\Json;
use ADK\Objects\Xml;

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
        return $this;
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
        curl_setopt($this->_curl, $key, $value)
        return $this;
    }

    /**
     * 
     * @param array $options
     * @return \ADK\Http\Curl
     */
    public function setOptions($options)
    {
        curl_setopt_array($this->_curl, $options);
        return $this;
    }

    /**
     * 
     * @return \ADK\Http\Curl
     */
    public function execute()
    {
        curl_exec($this->_curl);
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getContent()
    {
        curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
        return (string) curl_exec($this->_curl);
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