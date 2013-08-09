<?php

namespace Flare\Application;

use Flare\View\Dom\Event;

/**
 * 
 * @author anthony
 * 
 */
interface EventListener
{
	/**
	 * 
	 * @param \Flare\View\Dom\Event $event
	 * @return void
	 */
	public function listen(Event $event);
}