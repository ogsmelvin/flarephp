<?php

namespace Flare\Service;

use Flare\Service;

/**
 * 
 * @author
 * 
 */
class Bitly extends Service
{
	/**
	 * 
	 * @var string
	 */
	protected static $service = __CLASS__;

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
	 * @var string
	 */
	const API_HOST = 'http://api.bit.ly/v3/';

	/**
	 * 
	 * @access protected
	 * @param array $params
	 * @return void
	 */
	protected function init(array $params)
	{
		if (!isset($params['username'], $params['password'])) {
			show_error('Username and password must be set for Bitly Service');
		}
		$this->_username = $params['username'];
		$this->_password = $params['password'];
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
	 * @param string $format
	 * @return string
	 */
	public function shorten($link, $format = 'txt')
	{
		$result = (string) $this->curl
			->setUrl(self::API_HOST.'shorten')
			->setParam('login', $this->_username)
			->setParam('apiKey', $this->_password)
			->setParam('uri', $link)
			->setParam('format', $format)
			->getContent();
		return $result;
	}
}