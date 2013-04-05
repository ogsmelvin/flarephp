<?php

namespace ADK\Services;

use ADK\Http\Curl;

/**
 * 
 * @author anthony
 * 
 */
class OneWaySMS
{
    /**
     * 
     * @var string
     */
    private $_username;

    /**
     * 
     * @var string
     */
    private $_password;

    /**
     * 
     * @var \ADK\Http\CUrl
     */
    private $_curl;

    /**
     * 
     * @var array
     */
    private $_error = array();

    /**
     * 
     * @var string
     */
    const API_HOST = "http://gateway.onewaysms.ph:10001/";

    /**
     * 
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->_username = $username;
        $this->_password = $password;
        $this->_curl = new Curl();
    }

    /**
     * 
     * @param string $from
     * @param string $to
     * @param string $message
     * @return string|null
     */
    public function send($from, $to, $message)
    {
        $this->_error = array();
        $result = $this->_curl
            ->setParam('senderid', $from)
            ->setParam('mobileno', $to)
            ->setParam('message', $message)
            ->setParam('languagetype', 1)
            ->setParam('apiusername', $this->_username)
            ->setParam('apipassword', $this->_password)
            ->setUrl(self::API_HOST.'api.aspx')
            ->getContent();

        if($this->_curl->hasError()){
            $this->_error = array(
                'code' => '',
                'message' => $this->_curl->getError()
            );
            $result = null;
        } else if($result < 0){
            $this->_setError($result);
            $result = null;
        }

        $this->_curl->reset();
        return $result;
    }

    /**
     * 
     * @var string
     * @return void
     */
    private function _setError($code)
    {
        $this->_error = array('code' => $code);
        // -100 apipassname or apipassword is invalid
        // -200 senderid parameter is invalid
        // -300 mobileno parameter is invalid
        // -400 languagetype is invalid
        // -500 Invalid characters in message
        // -600 Insufficient credit balance
        if($this->_error['code'] == '-100'){
            $this->_error['message'] = 'apipassname or apipassword is invalid';
        } else if($this->_error['code'] == '-200'){
            $this->_error['message'] = 'senderid parameter is invalid';
        } else if($this->_error['code'] == '-300'){
            $this->_error['message'] = 'mobileno parameter is invalid';
        } else if($this->_error['code'] == '-400'){
            $this->_error['message'] = 'languagetype is invalid';
        } else if($this->_error['code'] == '-500'){
            $this->_error['message'] = 'Invalid characters in message';
        } else if($this->_error['code'] == '-600'){
            $this->_error['message'] = 'Insufficient credit balance';
        }
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
     * @return boolean
     */
    public function hasError()
    {
        return !empty($this->_error);
    }

    /**
     * 
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }
}