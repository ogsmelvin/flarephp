<?php

namespace Flare\Application\Event;

/**
 * 
 * @author anthony
 * 
 */
interface Listener
{
    /**
     * 
     * @param Flare\Application\Event
     * @return mixed
     */
    public function listen(Event $event);
}