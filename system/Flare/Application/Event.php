<?php

namespace Flare\Application;

use \Serializable;

/**
 * 
 * @author anthony
 * 
 */
class Event implements Serializable
{
	/**
	 *
	 * @var array
	 */
	private $_info = array();
	
	/**
	 * 
	 * @param string $name
	 * @param string $source
	 */
	public function __construct($name, $source)
	{
		$this->_info['name'] = $name;
		$this->_info['source'] = $source;
	}
	
	/**
	 * 
	 * @param mixed $data
	 * @return \Flare\Application\Event
	 */
	public function setData($data)
	{
		$this->_info['data'] = $data;
		return $this;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getData()
	{
		return !empty($this->_info['data']) ? $this->_info['data'] : array();
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->_info['name'];
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getSource()
	{
		return $this->_info['source'];
	}
	
	/**
	 * 
	 * @return string
	 */
	public function serialize()
	{
		return serialize($this->_info);
	}
	
	/**
	 * 
	 * @param string $data
	 * @return void
	 */
	public function unserialize($data)
	{
		$this->_info = unserialize($data);
	}
}