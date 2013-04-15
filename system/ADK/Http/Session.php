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
     * @var string
     */
    private static $_keySettings = '__adk_session';

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
            $_SESSION[$this->_name] = array(
                self::$_keySettings => array()
            );
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
        if(!isset($_SESSION[$this->_name][$key]) || $key === self::$_keySettings){
            return null;
        } else if(isset($_SESSION[$this->_name][self::$_keySettings][$key])
            && (time() - $_SESSION[$this->_name][self::$_keySettings][$key]['create_time'] 
                > $_SESSION[$this->_name][self::$_keySettings][$key]['expiration']))
        {
            unset($_SESSION[$this->_name][$key], $_SESSION[$this->_name][self::$_keySettings][$key]);
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
        } else if(strpos($key, '__') === 0){
            throw new Exception("Key must not have '__' ( underscore )");
        }
        $_SESSION[$this->_name][$key] = $value;
    }

    /**
     * 
     * @param string $key
     * @param mixed $value
     * @return \ADK\Http\Session
     */
    public function set($key, $value)
    {
        $this->__set($key, $value);
        return $this;
    }

    /**
     * 
     * @param string $key
     * @param int $seconds
     * @param int $now
     * @return \ADK\Http\Session
     */
    public function setExpiration($key, $seconds = 1800, $now = null)
    {
        if(!$now){
            $now = time();
        }
        $_SESSION[$this->_name][self::$_keySettings][$key] = array(
            'expiration' => $seconds,
            'create_time' => $now
        );
        return $this;
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function get($key = null)
    {
        if(!$key){
            $session = $_SESSION[$this->_name];
            unset($session[self::$_keySettings]);
            return $session;
        }
        return $this->__get($key);
    }

    /**
     * 
     * @param string $key
     * @return boolean
     */
    public function has($key)
    {
        return $this->__isset($key);
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
