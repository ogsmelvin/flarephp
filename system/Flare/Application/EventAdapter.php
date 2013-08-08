<?php

namespace Flare\Application;

use Flare\Application\EventListener;
use Flare\View\Dom\Event;
use Flare\Flare as F;

/**
 * 
 * @author anthony
 * 
 */
class EventAdapter implements EventListener
{
	/**
	 * 
	 * @param \Flare\View\Dom\Event $event
	 * @return mixed
	 */
	public function listen(Event $event)
	{
		return $event->fire();
	}

	/**
	 * 
	 * @return \Flare\Application\AbstractController
	 */
	public function getController()
	{
		return F::getApp()->getController();
	}
}