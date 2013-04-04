<?php

namespace ADK\Services;

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
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->_username = $username;
        $this->_password = $password;
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