<?php

namespace Flare\Application;

use Flare\Application\EventListener;
use Flare\Application\Event;
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
     * @param \Flare\Application\Event $event
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