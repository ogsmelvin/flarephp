<?php

namespace ADK\Mashups;

use ADK\Mashups\Facebook\User;
use ADK\Http\Curl;
use \Exception;
use ADK\Adk as A;

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
    const API_HOST = 'https://graph.facebook.com/';

    /**
     * 
     * @var string
     */
    const HOST = 'https://www.facebook.com/';

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
        $this->setAccessToken();
    }

    /**
     * 
     * @param string $redirect_uri
     * @param boolean $auto_redirect
     * @return string|void
     */
    private function _connect($redirect_uri, $auto_redirect = false)
    {
        $value = sha1(uniqid(rand(), true));
        $data = array(
            'client_id' => $this->_appId,
            'redirect_uri' => $redirect_uri,
            'state' => $value
        );
        A::$session->set('fb_'.$this->_appId.'_state', $value);
        A::$session->set('fb_'.$this->_appId.'_uri', $data['redirect_uri']);
        $loginUrl = self::HOST.'dialog/oauth?'.http_build_query($data);
        if($auto_redirect){
            A::$response->redirect($loginUrl);
        }
        return $loginUrl;
    }

    /**
     * 
     * @param string $redirect_uri
     * @param boolean $auto_redirect
     * @return string|void
     */
    public function reconnect($redirect_uri, $auto_redirect = false)
    {
        return $this->_connect($redirect_uri, $auto_redirect);
    }

    /**
     * 
     * @param string $redirect_uri
     * @param boolean $auto_redirect
     * @return string|null
     */
    public function connect($redirect_uri, $auto_redirect = false)
    {
        if(!$this->_accessToken){
            return $this->_connect($redirect_uri, $auto_redirect);
        }
        return null;
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

        if(A::$session->has('fb_'.$this->_appId.'_token')){
            $this->_accessToken = A::$session->get('fb_'.$this->_appId.'_token');
            $this->_user = new User($this);
            return $this;
        }

        $code = A::$request->get('code');
        $state = A::$request->get('state');
        if(($code && $state) 
            && strcmp($state, A::$session->get('fb_'.$this->_appId.'_state')) === 0){

            $result = $this->_curl
                        ->setParam('code', $code)
                        ->setParam('client_id', $this->_appId)
                        ->setParam('client_secret', $this->_appSecret)
                        ->setParam('redirect_uri', A::$session->get('fb_'.$this->_appId.'_uri'))
                        ->setUrl(self::API_HOST.'oauth/access_token')
                        ->getContent();
            
            if($this->_curl->hasError()){
                throw new Exception($this->_curl->getError());
            }
            
            parse_str($result, $params);
            $this->_accessToken = $params['access_token'];
            A::$session->set('fb_'.$this->_appId.'_token', $params['access_token']);
            $this->_user = new User($this);
        }
        return $this;
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
        if(!$this->_accessToken){
            return A::$session->get('fb_'.$this->_appId.'_token');
        }
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