<?php

namespace Flare\Http\Client;

use Flare\Object\Json;
use Flare\Object\Xml;

if (!function_exists('curl_init')) {
    show_error('CURL is not supported by your server');
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
    private $_error;

    /**
     * 
     * @var string
     */
    private $_method = 'GET';

    /**
     * 
     * @var string
     */
    private $_url;

    /**
     * 
     * @var boolean
     */
    private $_autoReset = true;

    /**
     * 
     * @var array
     */
    private $_httpHeaders = array();

    /**
     * 
     * @var string
     */
    private $_errorCode;

    /**
     * 
     * @param string $url
     */
    public function __construct($url = null)
    {
        $this->open($url);
    }

    /**
     * 
     * @param boolean $switch
     * @return \Flare\Http\Client\Curl
     */
    public function setAutoReset($switch)
    {
        $this->_autoReset = (boolean) $switch;
        return $this;
    }

    /**
     * 
     * @param string $url
     * @return \Flare\Http\Client\Curl
     */
    public function open($url = null)
    {
        $this->_curl = curl_init($url);
        if ($url) {
            $this->_url = $url;
        }
        return $this;
    }

    /**
     * 
     * @param string $url
     * @return \Flare\Http\Client\Curl
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
     * @return \Flare\Http\Client\Curl
     */
    public function setRequestMethod($method)
    {
        $method = strtoupper($method);
        if ($method === self::POST) {
            $this->_method = self::POST;
            curl_setopt($this->_curl, CURLOPT_POST, true);
        } elseif ($method === self::GET) {
            $this->_method = self::GET;
            curl_setopt($this->_curl, CURLOPT_POST, false);
        } else {
            show_error("Invalid request method");
        }
        return $this;
    }

    /**
     * 
     * @param string|array $type
     * @return \Flare\Http\Client\Curl
     */
    public function setContentType($type)
    {
        if (strpos($type, 'Content-Type: ') !== 0) {
            $type = 'Content-Type: '.$type;
        }
        return $this->setOption(CURLOPT_HTTPHEADER, $type);
    }

    /**
     * 
     * @param array $headers
     * @return \Flare\Http\Client\Curl
     */
    public function setHeaders(array $headers)
    {
        return $this->setOption(CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * 
     * @param array $params
     * @return \Flare\Http\Client\Curl
     */
    public function setParams($params)
    {
        foreach ($params as $k => $v) {
            $this->setParam($k, $v);
        }
        return $this;
    }

    /**
     * 
     * @param string $key
     * @param string|int $value
     * @return \Flare\Http\Client\Curl
     */
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
        return $this;
    }

    /**
     * 
     * @return \Flare\Http\Client\Curl
     */
    public function clearParams()
    {
        $this->_params = array();
        return $this;
    }

    /**
     * 
     * @param int $key
     * @param mixed $value
     * @return \Flare\Http\Client\Curl
     */
    public function setOption($key, $value)
    {
        if (strpos($key, 'CURLOPT_') !== 0) {
            $key = constant('CURLOPT_'.$key);
        }
        if ($key === CURLOPT_URL) {
            $this->_url = $value;
        } elseif ($key === CURLOPT_HTTPHEADER) {
            if (is_string($value)) {
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
     * @return \Flare\Http\Client\Curl
     */
    public function setOptions($options)
    {
        if (isset($options[CURLOPT_URL])) {
            $this->_url = $options[CURLOPT_URL];
        }
        curl_setopt_array($this->_curl, $options);
        return $this;
    }

    /**
     * 
     * @return \Flare\Http\Client\Curl
     */
    public function execute()
    {
        $this->_prepare();
        curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, false);
        curl_exec($this->_curl);
        if ($this->_errorCode = curl_errno($this->_curl)) {
            $this->_error = curl_error($this->_curl);
        }
        if ($this->_autoReset) {
            $this->reset();
        }
        return $this;
    }

    /**
     * 
     * @return void
     */
    private function _prepare()
    {
        if ($this->_method === self::POST) {
            curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $this->_params);
        } elseif ($this->_method === self::GET) {
            $url = parse_url($this->_url);
            if (!isset($url['query'])) {
                $url['query'] = http_build_query($this->_params);
            }
            $this->_url = http_build_url($url);
            $this->setOption(CURLOPT_URL, $this->_url);
        } else {
            show_error("Invalid request method");
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
        if ($this->_errorCode = curl_errno($this->_curl)) {
            $this->_error = curl_error($this->_curl);
        }
        if ($this->_autoReset) {
            $this->reset();
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
     * @return string|null
     */
    public function getErrorCode()
    {
        return $this->_errorCode;
    }

    /**
     * 
     * @return boolean
     */
    public function hasError()
    {
        return !empty($this->_error) || !empty($this->_errorCode);
    }

    /**
     * 
     * @return \Flare\Http\Client\Curl
     */
    public function reset()
    {
        curl_close($this->_curl);
        return $this->open();
    }

    /**
     * 
     * @return \Flare\Http\Json
     */
    public function getContentAsJson()
    {
        return new Json($this->getContent());
    }

    /**
     * 
     * @return \Flare\Http\Xml
     */
    public function getContentAsXml()
    {
        return new Xml($this->getContent());
    }
}