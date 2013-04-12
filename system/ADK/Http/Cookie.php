<?php

namespace ADK\Http;

/**
 * 
 * @author anthony
 * 
 */
class Cookie
{
    /**
     * 
     * @var string
     */
    private $_name;

    /**
     * 
     * @var string
     */
    private $_value = '';

    /**
     * 
     * @var int
     */
    private $_expire = 0;

    /**
     * 
     * @var string
     */
    private $_domain = '';

    /**
     * 
     * @var boolean
     */
    private $_httpOnly = false;

    /**
     * 
     * @var boolean
     */
    private $_secure = false;

    /**
     * 
     * @var string
     */
    private $_path = '';

    /**
     * 
     * @param string $name
     */
    public function __construct($name)
    {
        $this->_name = (string) $name;
    }

    /**
     * 
     * @param string $value
     * @return \ADK\Http\Cookie
     */
    public function setValue($value)
    {
        $this->_value = (string) $value;
        return $this;
    }

    /**
     * 
     * @param int $expire
     * @return \ADK\Http\Cookie
     */
    public function setExpiration($expire)
    {
        $this->_expire = (int) $expire;
        return $this;
    }

    /**
     * 
     * @param string $path
     * @return \ADK\Http\Cookie
     */
    public function setPath($path)
    {
        $this->_path = $path;
        return $this;
    }

    /**
     * 
     * @param string $domain
     * @return \ADK\Http\Cookie
     */
    public function setDomain($domain)
    {
        $this->_domain = $domain;
        return $this;
    }

    /**
     * 
     * @param boolean $secure
     * @return \ADK\Http\Cookie
     */
    public function setSecure($secure)
    {
        $this->_secure = (boolean) $secure;
        return $this;
    }

    /**
     * 
     * @param boolean $httponly
     * @return \ADK\Http\Cookie
     */
    public function setHttpOnly($httponly)
    {
        $this->_httpOnly = (boolean) $httponly;
        return $this;
    }

    /**
     * 
     * @return \ADK\Http\Cookies
     */
    public function save()
    {
        setcookie(
            $this->_name, 
            $this->_value, 
            $this->_expire, 
            $this->_path, 
            $this->_domain, 
            $this->_secure, 
            $this->_httpOnly
        );
        return $this;
    }
}