<?php

namespace Flare\Http;

use Flare\Security\Crypt;
use Flare\Flare as F;

/**
 * 
 * @author anthony
 * 
 */
class Cookie
{
    /**
     * 
     * @var \Flare\Http\Cookie
     */
    private static $_instance;

    /**
     * 
     * @var string
     */
    private $_encryptionKey = null;

    /**
     * 
     * @var string
     */
    private $_namespace;

    /**
     * 
     * @var array
     */
    private $_cookies;

    /**
     * 
     * @var array
     */
    private $_info;

    /**
     * 
     * @param string $name
     * @param string $encryptionKey
     */
    private function __construct($name, $encryptionKey = null)
    {
        $this->_namespace = $name;
        if ($encryptionKey) {
            $this->_encryptionKey = $encryptionKey;
        }

        $this->_fetchCookies();
    }

    /**
     * 
     * @return void
     */
    private function _fetchCookies()
    {
        $this->_cookies = array();
        $this->_info = array();

        if (isset($_COOKIE[$this->_namespace])) {
            $tmp = $_COOKIE[$this->_namespace];
            if ($this->_encryptionKey) {
                $tmp = Crypt::decode($tmp, $this->_encryptionKey);
            }
            $tmp = unserialize($tmp);
            if (isset($tmp['client_ip'])) {
                if (isset($tmp['data']) && is_array($tmp['data'])) {
                    $this->_cookies = $tmp['data'];
                }
                $this->_info = array(
                    'client_ip' => $tmp['client_ip']
                );
            }
        }
    }

    /**
     * 
     * @param string $name
     * @param string $encryptionKey
     * @return \Flare\Http\Cookie
     */
    public static function create($name, $encryptionKey = null)
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self($name, $encryptionKey);
        }
        return self::$_instance;
    }

    /**
     * 
     * @param string $name
     * @param string $value
     * @param int $expire
     * @param string path
     * @param string $domain
     * @param boolean $secure
     * @param boolean $httponly
     * @return \Flare\Http\Cookie
     */
    public function set($name, $cookie, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
    {
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * 
     * @param string $name
     * @return mixed
     */
    public function get($name, $xss = false)
    {
        if (!isset($this->_cookies[$name])) {
            return null;
        }
        return $this->_cookies[$name];
    }

    /**
     * 
     * @return string
     */
    public function serialize()
    {
        if (empty($this->_info)) {
            return null;
        }
        $data = serialize(array_merge(array('data' => $this->_cookies), $this->_info));
        if ($this->_encryptionKey) {
            $data = Crypt::encode($data, $this->_encryptionKey);
        }
        return $data;
    }

    /**
     * 
     * @return array
     */
    public function getData()
    {
        return $this->_cookies;
    }

    /**
     * 
     * @return string
     */
    public function getIp()
    {
        return isset($this->_info['client_ip']) ? $this->_info['client_ip'] : null;
    }

    /**
     * 
     * @return array
     */
    public function getInfo()
    {
        return $this->_info;
    }
}