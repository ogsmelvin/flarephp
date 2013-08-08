<?php

namespace Flare\View\Dom;

/**
 * 
 * @author anthony
 * 
 */
class Event
{
	/**
	 *
	 * @var string
	 */
	public $name;
	
	/**
	 *
	 * @var string
	 */
	public $element;
	
	/**
	 * 
	 * @param string $event
	 * @param string $element
	 */
	public function __construct($event, $element)
	{
		$this->name = $event;
		$this->element = $element;
	}
}