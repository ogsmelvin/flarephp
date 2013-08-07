<?php

namespace Flare\View\DOM;

use Flare\Application\EventListener;

/**
 * 
 * @author anthony
 * 
 */
class Element
{
	/**
	 *
	 * @var string
	 */
	private $_id;
	
	/**
	 *
	 * @var string
	 */
	private $_tag;
	
	/**
	 * 
	 * @param string $id
	 * @return \Flare\View\DOM\Element
	 */
	public function setId($id)
	{
		$this->_id = $id;
		return $this;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return \Flare\View\DOM\Element
	 */
	public function setTagName($name)
	{
		$this->_tag = $name;
		return $this;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getTagName()
	{
		return $this->_tag;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getId()
	{
		return $this->_id;
	}
	
	/**
	 * 
	 * @param string $event
	 * @param \Flare\Application\EventListener $handler
	 * @return \Flare\View\DOM\Element
	 */
	public function addEventListener($event, EventListener &$handler)
	{
		return $this;
	}
}