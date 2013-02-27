<?php

namespace ADK\Http;

use \Exception;

/**
 * 
 * @author
 * 
 */
class Session
{
    /**
     * 
     * @var \ADK\Http\Session
     */
    private static $_instance = null;

    /**
     * 
     * @var boolean
     */
    private $_started = false;
    
    /**
     *
     * @var string
     */
    private $_name = null;

    /**
     * 
     * @param boolean $start
     */
    private function __construct($namespace, $start = false)
    {
        $this->_name = $namespace;
        if($start){
            $this->start();
        }
    }

    /**
     * 
     * @return \ADK\Http\Session
     */
    public function start()
    {
        session_start();
        if(!isset($_SESSION[$this->_name])){
            $_SESSION[$this->_name] = array();
        }
        $this->_started = true;
        return $this;
    }

    /**
     * 
     * @return \ADK\Http\Session
     */
    public static function & getInstance($namespace, $start = false)
    {
        if(!self::$_instance){
            self::$_instance = new self($namespace, $start);
        }
        return self::$_instance;
    }

    /**
     * 
     * @return string
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * 
     * @return string
     */
    public function getNamespace()
    {
        return $this->_name;
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if(!$this->_started){
            throw new Exception("Session must be started first");
        }
        if(!isset($_SESSION[$this->_name][$key])){
            return null;
        }
        return $_SESSION[$this->_name][$key];
    }

    /**
     * 
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function __set($key, $value)
    {
        if(!$this->_started){
            throw new Exception("Session must be started first");
        }
        $_SESSION[$this->_name][$key] = $value;
    }

    /**
     * 
     * @param string $key
     * @return void
     */
    public function __isset($key)
    {
        if(!$this->_started){
            throw new Exception("Session must be started first");
        }
        return isset($_SESSION[$this->_name][$key]);
    }

    /**
     * 
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        if(!$this->_started){
            throw new Exception("Session must be started first");
        }
        unset($_SESSION[$this->_name][$key]);
    }

    /**
     * 
     * @return \ADK\Http\Session
     */
    public function destroy()
    {
        if(!$this->_started){
            throw new Exception("Session must be started first");
        }
        session_destroy();
        session_regenerate_id();
        $this->_started = false;
        return $this;
    }

    /**
     * 
     * @return \ADK\Http\Session
     */
    public function resetId()
    {
        if(!$this->_started){
            throw new Exception("Session must be started first");
        }
        session_regenerate_id();
        return $this;
    }

    /**
     * 
     * @return \ADK\Http\Session
     */
    public function clear()
    {
        if(!$this->_started){
            throw new Exception("Session must be started first");
        }
        unset($_SESSION[$this->_name]);
        return $this;
    }

    /**
     * 
     * @param string $key
     * @return string|int
     */
    public function flash($key)
    {
        $val = $this->__get($key);
        if($val !== null){
            unset($_SESSION[$this->_name][$key]);
        }
        return $val;
    }
}
