<?php

namespace Flare\Http;

use Flare\Security\Crypt;

/**
 * 
 * @author anthony
 * 
 */
class Cookie
{
    /**
     * 
     * @var \Flare\Http\Cookie\Jar
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
        if (isset($_COOKIE[$this->_namespace])) {
            if ($this->_encryptionKey) {
                Crypt::decode($_COOKIE[$this->_namespace], $this->_encryptionKey);
            } else {
                
            }
        } else {
            $this->_cookies = array();
        }
    }

    /**
     * 
     * @param string $name
     * @param string $encryptionKey
     * @return \Flare\Http\Cookie\Jar
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
     * @param \Flare\Http\Cookie|string $cookie
     * @return \Flare\Http\Cookie\Jar
     */
    public function set($name, $cookie, $time, $domain)
    {
        
    }

    /**
     * 
     * @return void
     */
    public function save()
    {

    }
}