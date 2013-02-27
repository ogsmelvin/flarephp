<?php

namespace ADK\Mashups;

use ADK\Mashups\Facebook\User;
use ADK\Http\Curl;

/**
 * 
 * @author anthony
 * 
 */
class Facebook
{
    /**
     * 
     * @var string
     */
    private $_accessToken = null;

    /**
     * 
     * @var string
     */
    private $_appId;

    /**
     * 
     * @var string
     */
    private $_appSecret;

    /**
     * 
     * @var boolean
     */
    private $_fileUploadSupport;

    /**
     * 
     * @var \ADK\Http\Curl
     */
    private $_curl;

    /**
     * 
     * @var \ADK\Mashups\Facebook\User
     */
    private $_user = null;

    /**
     * 
     * @param string $appId
     * @param string $appSecret
     * @param boolean $fileUpload
     */
    public function __construct($appId, $appSecret, $fileUpload = false)
    {
        $this->_curl = new Curl();
        $this->setAppId($appId);
        $this->setAppSecret($appSecret);
        $this->setFileUpload($fileUpload);
    }

    /**
     * 
     * @param boolean $auto_redirect
     * @return string|void
     */
    public function connect($auto_redirect = false)
    {
        
    }

    /**
     * 
     * @param string $token
     * @return \ADK\Mashups\Facebook
     */
    public function setAccessToken($token = null)
    {
        if($token){
            $this->_accessToken = (string) $token;
            return $this;
        }
    }

    /**
     * 
     * @param boolean $upload
     * @return \ADK\Mashups\Facebook
     */
    public function setFileUpload($upload)
    {
        $this->_fileUploadSupport = (boolean) $upload;
        return $this;
    }

    /**
     * 
     * @param string $id
     * @return \ADK\Mashups\Facebook
     */
    public function setAppId($id)
    {
        $this->_appId = (string) $id;
        return $this;
    }

    /**
     * 
     * @param string $secret
     * @return \ADK\Mashups\Facebook
     */
    public function setAppSecret($secret)
    {
        $this->_appSecret = (string) $secret;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getAppId()
    {
        return $this->_appId;
    }

    /**
     * 
     * @return string
     */
    public function getAppSecret()
    {
        return $this->_appSecret;
    }

    /**
     * 
     * @return string
     */
    public function getAccessToken()
    {
        return $this->_accessToken;
    }

    /**
     * 
     * @return \ADK\Mashups\Facebook\User
     */
    public function getUser()
    {
        return $this->_user;
    }
}