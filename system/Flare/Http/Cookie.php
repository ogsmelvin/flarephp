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
     * @var string
     */
    public $name;

    /**
     * 
     * @var string
     */
    public $path;

    /**
     * 
     * @var string
     */
    public $value;

    /**
     * 
     * @var boolean
     */
    public $secure;

    /**
     * 
     * @var string
     */
    public $domain;

    /**
     * 
     * @var boolean
     */
    public $httpOnly;

    /**
     * 
     * @var int
     */
    public $expiration;

    /**
     * 
     * @param string $name
     * @param string $value
     * @param int $expiration
     * @param boolean $secure
     * @param boolean $httponly
     * @param string $path
     * @param string $domain
     */
    public function __construct($name, $value, $expiration = 0, $secure = false, $httponly = false, $path = '/', $domain = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->expiration = $expiration;
        $this->secure = $secure;
        $this->httpOnly = $httponly;
        $this->path = $path;
        $this->domain = $domain;
    }

    /**
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        show_error("Undefined cookie attribute '{$key}'");
    }

    /**
     * 
     * @param string $key
     * @return void
     */
    public function __get($key)
    {
        show_error("Undefined cookie attribute '{$key}'");
    }
}