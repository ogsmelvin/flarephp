<?php

namespace ADK\Http;

/**
 *
 * @author anthony
 *
 */
class Request
{
    /**
     *
     * @param string $key
     * @param mixed $default
     * @return string
     */
    public function post($key = null, $default = null)
    {
        if($key === null){
            return !empty($_POST) ? $_POST : $default;
        }

        return isset($_POST[$key]) ? $_POST[$key] : $default;
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
            return !empty($_GET) ? $_GET : $default;
        }

        return isset($_GET[$key]) ? $_GET[$key] : $default;
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
            return !empty($_SERVER) ? $_SERVER : $default;
        }

        return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
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
        if($this->server('HTTP_X_REQUESTED_WITH') === 'XMLHTTPREQUEST') {
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
            return !empty($_COOKIE) ? $_COOKIE : $default;
        }

        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
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
            return !empty($_REQUEST) ? $_REQUEST : $default;
        }

        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
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
        if($value){
            $value = html($value);
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
        if($value){
            $value = html($value);
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
        if($value){
            $value = html($value);
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
        if($value){
            $value = html($value);
        }
        return $value;
    }
}