<?php

namespace Flare\Application;

use Flare\View\Event;

/**
 * 
 * @author anthony
 * 
 */
interface EventListener
{
	/**
	 * 
	 * @param \Flare\View\Event $event
	 * @return mixed
	 */
	public function listen(Event $event);
}