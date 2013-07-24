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
     * @param boolean $enableEncryption
     * @param string $encryptionKey
     */
    private function __construct($enableEncryption, $encryptionKey)
    {
        if ($enableEncryption) {
            $this->_encryptionKey = $encryptionKey;
        }
    }

    /**
     * 
     * @param boolean $enableEncryption
     * @param string $encryptionKey
     * @return \Flare\Http\Cookie
     */
    public static function & getInstance($enableEncryption = false, $encryptionKey = null)
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self($enableEncryption, $encryptionKey);
        }
        return self::$_instance;
    }

    /**
     * 
     * @param string $name
     * @param mixed $value
     * @param int $time
     * @return \Flare\Http\Cookie
     */
    public function set($name, $value, $expiry = 0)
    {
        setcookie($name, $value, $expiry);
        $_COOKIE[$name] = $value;
        return $this;
    }

    /**
     * 
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        if (is_array($value)) {
            $this->set($name, $value['value'], $value['expiry']);
        } else {
            $this->set($name, (string) $value);
        }
    }

    /**
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * 
     * @param string $name
     * @return mixed
     */
    public function get($name, $xss = null)
    {
        if (!isset($_COOKIE[$name])) {
            return null;
        }
        return $_COOKIE[$name];
    }
}