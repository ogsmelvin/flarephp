<?php

namespace FPHP\Services;

use FPHP\Http\Curl;

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
     * @var \FPHP\Http\CUrl
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
     * @var array
     */
    private static $_errorMessages = array(
        'send_sms' => array(
            '-100' => 'apipassname or apipassword is invalid',
            '-200' => 'senderid parameter is invalid',
            '-300' => 'mobileno parameter is invalid',
            '-400' => 'languagetype is invalid',
            '-500' => 'Invalid characters in message',
            '-600' => 'Insufficient credit balance'
        ),
        'transaction_status' => array(
            //0 Success receive on mobile handset
            '100' => 'Message delivered to Telco',
            '-100' => 'mtid invalid / not found',
            '-200' => 'Message sending fail'
        ),
        'credit_balance' => array(
            '-100' => 'apipassname or apipassword is invalid'
        )
    );

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
     * @param int $languagetype
     * @return string|null
     */
    public function send($from, $to, $message, $languagetype = 1)
    {
        $this->_error = array();
        $result = $this->_curl
            ->setParam('senderid', $from)
            ->setParam('mobileno', $to)
            ->setParam('message', $message)
            ->setParam('languagetype', $languagetype)
            ->setParam('apiusername', $this->_username)
            ->setParam('apipassword', $this->_password)
            ->setUrl(self::API_HOST.'api.aspx')
            ->getContent();

        if($this->_curl->hasError()){
            $this->_error = array(
                'code' => $this->_curl->getErrorCode(),
                'message' => $this->_curl->getError()
            );
            $result = null;
        } else if($result < 0){
            $this->_setError('send_sms', $result);
            $result = null;
        }
        return $result;
    }

    /**
     * 
     * @param string $mtid
     * @return boolean
     */
    public function getTransactionStatus($mtid)
    {
        $this->_error = array();
        $result = $this->_curl
            ->setParam('mtid', $mtid)
            ->setUrl(self::API_HOST.'bulktrx.aspx')
            ->getContent();

        if($this->_curl->hasError()){
            $this->_error = array(
                'code' => $this->_curl->getErrorCode(),
                'message' => $this->_curl->getError()
            );
            return false;
        } else if($result != 0){
            $this->_setError('transaction_status', $result);
            return false;
        }
        return true;
    }

    /**
     * 
     * @return int
     */
    public function getCreditBalance()
    {
        $this->_error = array();
        $result = $this->_curl
            ->setParam('apiusername', $this->_username)
            ->setParam('apipassword', $this->_password)
            ->setUrl(self::API_HOST.'bulkcredit.aspx')
            ->getContent();

        if($this->_curl->hasError()){
            $this->_error = array(
                'code' => $this->_curl->getErrorCode(),
                'message' => $this->_curl->getError()
            );
            return null;
        } else if($result < 0){
            $this->_setError('credit_balance', $result);
            return null;
        }
        return (int) $result;
    }

    /**
     * 
     * @param string $type
     * @param string $code
     * @return void
     */
    private function _setError($type, $code)
    {
        $this->_error = array('code' => $code);
        if(isset(self::$_errorMessages[$type][$code])){
            $this->_error['message'] = self::$_errorMessages[$type][$code];
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