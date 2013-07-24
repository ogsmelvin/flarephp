<?php

namespace Flare\Http;

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

    private function __construct() {}

    /**
     * 
     * @return \Flare\Http\Cookie
     */
    public static function & getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
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
    public function set($name, $value, $time = null)
    {
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
            $this->set($name, $value['value']);
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