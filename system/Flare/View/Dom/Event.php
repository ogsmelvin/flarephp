<?php

namespace Flare\View\Dom;

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
	 * @param string $event
	 * @param string $source
	 */
	public function __construct($event, $source)
	{
		$this->_info['name'] = $event;
		$this->_info['source'] = $source;
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
	 * @param mixed $data
	 * @return void
	 */
	public function unserialize($data)
	{
		$this->_info = unserialize($data);
	}
}