<?php

namespace Flare\Http\Cookie;

use Flare\Http\Cookie;

/**
 * 
 * @author anthony
 * 
 */
class Jar
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
     * @param string $name
     * @param string $encryptionKey
     */
    private function __construct($name, $encryptionKey = null)
    {
        $this->_namespace = $name;
        if ($encryptionKey) {
            $this->_encryptionKey = $encryptionKey;
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
     * @param \Flare\Http\Cookie $cookie
     * @return \Flare\Http\Cookie\Jar
     */
    public function save(Cookie $cookie)
    {
        return $this;
    }
}