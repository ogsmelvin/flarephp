<?php

namespace Flare\View;

use Flare\Application\EventListener;

/**
 * 
 * @author
 * 
 */
interface Dom
{
	/**
	 * 
	 * @param string $selector
	 * @param string $event
	 * @param \Flare\View\EventListener $listener
	 * @return \Flare\View\Response
	 */
	public function addEvent($selector, $event, EventListener &$listener);
	
	/**
	 * 
	 * @param string $selector
	 * @param string $event
	 * @return \Flare\View\Response
	 */
	public function removeEvent($selector, $event);
	
	/**
	 * 
	 * @return \Flare\Util\Collection
	 */
	public function getEvents();
}
