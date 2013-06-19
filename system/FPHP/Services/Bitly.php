<?php

namespace FPHP\Services;

use FPHP\Http\Curl;

/**
 * 
 * @author
 * 
 */
class Bitly
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
     * @var \FPHP\Http\Curl
     */
    private $_curl;

    /**
     * 
     * @var string
     */
    const API_HOST = 'http://api.bit.ly/v3/';

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
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * 
     * @param string $link
     * @return string
     */
    public function shorten($link, $format = 'txt')
    {
        $result = (string) $this->_curl
            ->setUrl(self::API_HOST.'shorten')
            ->setParam('login', $this->_username)
            ->setParam('apiKey', $this->_password)
            ->setParam('uri', $link)
            ->setParam('format', $format)
            ->getContent();
        return $result;
    }
}