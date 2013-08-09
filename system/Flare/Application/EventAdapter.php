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
	 * @return void
	 */
	public function listen(Event $event) {}

	/**
	 * 
	 * @return \Flare\Application\AbstractController
	 */
	public function getController()
	{
		return F::getApp()->getController();
	}
}