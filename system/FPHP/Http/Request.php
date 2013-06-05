<?php

namespace FPHP\Http;

use FPHP\Fphp as A;
use FPHP\Security;

/**
 *
 * @author anthony
 *
 */
class Request
{
    /**
     * 
     * @var boolean
     */
    protected $_autoXssFilter = false;

    /**
     * 
     * @var string
     */
    protected $_csrfToken = null;

    /**
     * 
     * @param boolean
     * @return \FPHP\Http\Request
     */
    public function setAutoFilter($switch)
    {
        $this->_autoXssFilter = (boolean) $switch;
        return $this;
    }

    /**
     * 
     * @param string|array $var
     * @return string|array
     */
    protected function _filter($var)
    {
        if(is_string($var)){
            return Security::xssClean($var);
        }

        foreach($var as &$val){
            $val = $this->_filter($val);
        }
        return $var;
    }

    /**
     *
     * @param string $key
     * @param mixed $default
     * @return string
     */
    public function post($key = null, $default = null)
    {
        if($key === null){
            if(!empty($_POST)){
                if($this->_autoXssFilter){
                    return $this->_filter($_POST);
                }
                return $_POST;
            }
        } else if(isset($_POST[$key])){
            if($this->_autoXssFilter){
                return $this->_filter($_POST[$key]);
            }
            return $_POST[$key];
        }
        return $default;
    }

    /**
     *
     * @param string $key
     * @param mixed $default
     * @return string
     */
    public function get($key = null, $default = null)
    {
        if($key === null){
            if(!empty($_GET)){
                if($this->_autoXssFilter){
                    return $this->_filter($_GET);
                }
                return $_GET;
            }
        } else if(isset($_GET[$key])){
            if($this->_autoXssFilter){
                return $this->_filter($_GET[$key]);
            }
            return $_GET[$key];
        }
        return $default;
    }

    /**
     *
     * @param string $key
     * @param mixed $default
     * @return string
     */
    public function server($key = null, $default = null)
    {
        if($key === null){
            if(!empty($_SERVER)){
                if($this->_autoXssFilter){
                    return $this->_filter($_SERVER);
                }
                return $_SERVER;
            }
        } else if(isset($_SERVER[strtoupper($key)])){
            if($this->_autoXssFilter){
                return $this->_filter($_SERVER[strtoupper($key)]);
            }
            return $_SERVER[strtoupper($key)];
        }
        return $default;
    }

    /**
     *
     * @return boolean
     */
    public function isPost()
    {
        if($this->server('REQUEST_METHOD') !== 'POST'){
            return false;
        }
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function isGet()
    {
        if($this->server('REQUEST_METHOD') !== 'GET'){
            return false;
        }
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function isAjax()
    {
        if(strtoupper($this->server('HTTP_X_REQUESTED_WITH')) === 'XMLHTTPREQUEST') {
            return true;
        }
        return false;
    }

    /**
     *
     * @param string $key
     * @param mixed $default
     * @return string
     */
    public function cookie($key = null, $default = null)
    {
        if($key === null){
            if(!empty($_COOKIE)){
                if($this->_autoXssFilter){
                    return $this->_filter($_COOKIE);
                }
                return $_COOKIE;
            }
        } else if(isset($_COOKIE[$key])){
            if($this->_autoXssFilter){
                return $this->_filter($_COOKIE[$key]);
            }
            return $_COOKIE[$key];
        }
        return $default;
    }

    /**
     *
     * @param string $key
     * @param mixed $default
     * @return string
     */
    public function request($key = null, $default = null)
    {
        if($key === null){
            if(!empty($_REQUEST)){
                if($this->_autoXssFilter){
                    return $this->_filter($_REQUEST);
                }
                return $_REQUEST;
            }
        } else if(isset($_REQUEST[$key])){
            if($this->_autoXssFilter){
                return $this->_filter($_REQUEST[$key]);
            }
            return $_REQUEST[$key];
        }
        return $default;
    }

    /**
     *
     * @param string $key
     * @param mixed $default
     * @return array
     */
    public function files($key = null, $default = null)
    {
        if($key === null){
            return !empty($_FILES) ? $_FILES : $default;
        }
        return isset($_FILES[$key]) ? $_FILES[$key] : $default;
    }

    /**
     * 
     * @param string $key
     * @return string
     */
    public function filterPost($key)
    {
        $value = $this->post($key);
        if(!$this->_autoXssFilter && $value !== null){
            $value = $this->_filter($value);
        }
        return $value;
    }

    /**
     * 
     * @param string $key
     * @return string
     */
    public function filterGet($key)
    {
        $value = $this->get($key);
        if(!$this->_autoXssFilter && $value !== null){
            $value = $this->_filter($value);
        }
        return $value;
    }

    /**
     * 
     * @param string $key
     * @return string
     */
    public function filterRequest($key)
    {
        $value = $this->request($key);
        if(!$this->_autoXssFilter && $value !== null){
            $value = $this->_filter($value);
        }
        return $value;
    }

    /**
     * 
     * @param string $key
     * @return string
     */
    public function filterCookie($key)
    {
        $value = $this->cookie($key);
        if(!$this->_autoXssFilter && $value !== null){
            $value = $this->_filter($value);
        }
        return $value;
    }
}