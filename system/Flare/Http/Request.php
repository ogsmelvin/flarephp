<?php

namespace FPHP\Http;

use FPHP\Fphp as F;

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
        $key = strtoupper($key);
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
     * @param boolean $checkProxy
     * @return string
     */
    public function getClientIp($checkProxy = true)
    {
        $ip = null;
        if($checkProxy && $this->server('HTTP_CLIENT_IP') != null){
            $ip = $this->server('HTTP_CLIENT_IP');
        } else if($checkProxy && $this->server('HTTP_X_FORWARDED_FOR') != null){
            $ip = $this->server('HTTP_X_FORWARDED_FOR');
        } else if($checkProxy && $this->server('HTTP_X_CLUSTER_CLIENT_IP')){
            $ip = $this->server('HTTP_X_CLUSTER_CLIENT_IP');
        } else {
            $ip = $this->server('REMOTE_ADDR');
        }
        return $ip;
    }

    /**
     * 
     * @return boolean
     */
    public function isFlash()
    {
        $header = '';
        if($this->server('HTTP_USER_AGENT')){
            $header = strtolower($this->server('HTTP_USER_AGENT'));
        } else if(function_exists('apache_request_headers')){
            $headers = apache_request_headers();
            if(isset($headers['HTTP_USER_AGENT'])){
                $header = strtolower($headers['HTTP_USER_AGENT']);
            }
        }
        return (strstr($header, ' flash')) ? true : false;
    }
}