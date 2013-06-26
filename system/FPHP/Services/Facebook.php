<?php

namespace FPHP\Services;

use FPHP\Http\Client\Curl;
use FPHP\Objects\Json;
use FPHP\Fphp as A;

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
     * @var array
     */
    private $_signedRequest = array();

    /**
     * 
     * @var \FPHP\Http\Curl
     */
    private $_curl;

    /**
     * 
     * @var string
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
     * @param string $userId
     * @return void
     */
    private function _setUser($userId = null)
    {
        if($userId){
            $this->_user = $userId;
            A::$session->set('fb_'.$this->_appId.'_user', $userId);
        } else {
            $this->_user = A::$session->get('fb_'.$this->_appId.'_user');
        }
    }

    /**
     * 
     * @return void
     */
    private function _parseSignedRequest($s_request)
    {
        if(!$s_request){
            $this->_setUser();
            return;
        }

        list($encoded_sig, $payload) = explode('.', $s_request, 2); 
        $sig = $this->_base64UrlDecode($encoded_sig);
        $data = json_decode($this->_base64UrlDecode($payload), true);
        $this->_signedRequest = $data;

        if(!isset($data['user_id'])){
            $this->_setUser();
        } else {
            $this->_setUser($data['user_id']);
        }
    }

    /**
     * 
     * @param string $input
     * @return string
     */
    private function _base64UrlDecode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * 
     * @param string $redirect_uri
     * @param boolean $auto_redirect
     * @return string|void
     */
    private function _connect($redirect_uri, $permission = array(), $auto_redirect = false)
    {
        $value = sha1(uniqid(rand(), true));
        $data = array(
            'client_id' => $this->_appId,
            'redirect_uri' => $redirect_uri,
            'state' => $value,
            'scope' => implode(',', $permission)
        );
        A::$session->set('fb_'.$this->_appId.'_perms', $permission);
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
     * @return array
     */
    public function getPermission()
    {
        return A::$session->get('fb_'.$this->_appId.'_perms');
    }

    /**
     * 
     * @param string $type
     * @param string $fbid
     * @return string
     */
    public function getImage($type = 'small', $fbid = 'me')
    {
        if($fbid == 'me'){
            $fbid = $this->getUser();
        }
        return 'https://graph.facebook.com/'.$fbid.'/picture?type='.$type;
    }

    /**
     * 
     * @param string $redirect_uri
     * @param boolean $auto_redirect
     * @return string|void
     */
    public function reconnect($redirect_uri, $permission = array(), $auto_redirect = false)
    {
        return $this->_connect($redirect_uri, $permission, $auto_redirect);
    }

    /**
     * 
     * @param string $redirect_uri
     * @param boolean $auto_redirect
     * @return string|null
     */
    public function connect($redirect_uri, $permission = array(), $auto_redirect = false)
    {
        if(!$this->_accessToken){
            return $this->_connect($redirect_uri, $permission, $auto_redirect);
        }
        return null;
    }

    /**
     * 
     * @return string
     */
    public function getSignedRequest()
    {
        return $this->_signedRequest;
    }


    public function feed()
    {
        
    }

    public function comment()
    {

    }

    public function like()
    {

    }
    
    /**
     * 
     * @param string $id
     * @return \FPHP\Objects\Json
     */
    public function getProfile($id = 'me')
    {
        $data = $this->_curl
            ->setParam('access_token', $this->getAccessToken())
            ->setUrl(self::API_HOST.$id)
            ->getContentAsJson();

        if(isset($data['error'])){
            $this->_error = $data['error'];
            return null;
        }
        return $data;
    }

    /**
     * 
     * @param string $id
     * @param array $fields
     * @return \FPHP\Objects\Json
     */
    public function getUserDetails($id = 'me', $fields = array())
    {
        if($id === 'me'){
            $id = $this->getUser();
        }
        if(!$fields){
            $fields = array('uid', 'first_name', 'last_name', 'profile_url');
        }
        $fql = "SELECT ".implode(',', $fields)." FROM user WHERE uid = ".$id;
        $data = $this->_curl
            ->setUrl(self::API_HOST.'fql')
            ->setParam('access_token', $this->getAccessToken())
            ->setParam('q', $fql)
            ->getContentAsJson();

        if(isset($data['error'])){
            $this->_error = $data['error'];
            return null;
        }
        $data = isset($data['data']) ? end($data['data']) : array();
        return new Json($data);
    }

    /**
     * 
     * @param string $fql
     * @return \FPHP\Objects\Json
     */
    public function fql($fql)
    {
        return $this->_curl
            ->setUrl(self::API_HOST.'fql')
            ->setParam('access_token', $this->getAccessToken())
            ->setParam('q', $fql)
            ->getContentAsJson();
    }

    /**
     * 
     * @param array $fields
     * @param int $limit
     * @param int $page
     * @param string $order
     * @return \FPHP\Objects\Json
     */
    public function getFriends($fields = array(), $limit = 0, $page = 0, $order = null)
    {
        if(!$fields){
            $fields = array('uid', 'first_name', 'last_name', 'profile_url');
        }
        $fql = "SELECT ".implode(',', $fields)." FROM user "
            ."WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = ".$this->getUser().")";
        if($limit){
            $page = (int) ($page <= 1 || !$page ? 0 : ($page - 1));
            $fql .= " LIMIT ".($page * $limit).",".(int) $limit;
        }
        if($order){
            $fql .= " ORDER BY ".(string) $order;
        }
        $data = $this->_curl
            ->setUrl(self::API_HOST.'fql')
            ->setParam('access_token', $this->getAccessToken())
            ->setParam('q', $fql)
            ->getContentAsJson();

        if(isset($data['error'])){
            $this->_error = $data['error'];
            return null;
        }
        $data = isset($data['data']) ? $data['data'] : array();
        return new Json($data);
    }

    /**
     * 
     * @return boolean
     */
    public function hasError()
    {
        return !empty($this->_error);
    }

    /**
     * 
     * @return array
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * 
     * @param string $token
     * @return \FPHP\Services\Facebook
     */
    public function setAccessToken($token = null)
    {
        $code = A::$request->get('code');
        $state = A::$request->get('state');
        if($token){
            $this->_accessToken = (string) $token;
            A::$session->set('fb_'.$this->_appId.'_token', $this->_accessToken);
        } else if(A::$session->has('fb_'.$this->_appId.'_token')){
            $this->_accessToken = A::$session->get('fb_'.$this->_appId.'_token');
        } else if(($code && $state) 
            && strcmp($state, A::$session->get('fb_'.$this->_appId.'_state')) === 0){

            $result = $this->_curl
                        ->setParam('code', $code)
                        ->setParam('client_id', $this->_appId)
                        ->setParam('client_secret', $this->_appSecret)
                        ->setParam('redirect_uri', A::$session->get('fb_'.$this->_appId.'_uri'))
                        ->setUrl(self::API_HOST.'oauth/access_token')
                        ->getContent();
            
            if($this->_curl->hasError()){
                show_error($this->_curl->getError());
            }
            
            parse_str($result, $params);
            $this->_accessToken = $params['access_token'];
            A::$session->set('fb_'.$this->_appId.'_token', $params['access_token']);
        }

        $s_request = A::$request->request('signed_request');
        if(!$s_request){
            $s_request = A::$session->get('fb_'.$this->_appId.'_signed_request');
        } else {
            A::$session->set('fb_'.$this->_appId.'_signed_request', $s_request);
        }
        $this->_parseSignedRequest($s_request);
        return $this;
    }

    /**
     * 
     * @param boolean $upload
     * @return \FPHP\Services\Facebook
     */
    public function setFileUpload($upload)
    {
        $this->_fileUploadSupport = (boolean) $upload;
        return $this;
    }

    /**
     * 
     * @param string $id
     * @return \FPHP\Services\Facebook
     */
    public function setAppId($id)
    {
        $this->_appId = (string) $id;
        return $this;
    }

    /**
     * 
     * @param string $secret
     * @return \FPHP\Services\Facebook
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
     * @return string|null
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * 
     * @param string $userId
     * @return \FPHP\Services\Facebook
     */
    public function setUser($userId)
    {
        $this->_user = (string) $userId;
        return $this;
    }
}