<?php

namespace Flare\Application;

use Flare\Application\Event;

/**
 * 
 * @author anthony
 * 
 */
interface EventListener
{
	/**
	 * 
	 * @param \Flare\Application\Event $event
	 * @return void
	 */
	public function listen(Event $event);
}