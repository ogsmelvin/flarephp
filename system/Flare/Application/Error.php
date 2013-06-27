<?php

namespace Flare\Application;

use Flare\Flare as F;

/**
 * 
 * @author anthony
 * 
 */
class Error
{
    /**
     * 
     * @var int
     */
    private $_errorCode;

    /**
     * 
     * @var string
     */
    private $_message;

    /**
     * 
     * @param int $code
     */
    public function __construct($code = 500)
    {
        $this->setErrorCode($code);
    }

    /**
     * 
     * @param int $code
     * @return \Flare\Application\Error
     */
    public function setErrorCode($code)
    {
        $this->_errorCode = (int) $code;
        return $this;
    }

    /**
     * 
     * @return int
     */
    public function getErrorCode()
    {
        return $this->_errorCode;
    }

    /**
     * 
     * @param string $message
     * @return \Flare\Application\Error
     */
    public function setMessage($message)
    {
        $this->_message = (string) $message;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }
}