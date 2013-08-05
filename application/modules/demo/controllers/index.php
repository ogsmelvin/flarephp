<?php

namespace Demo\Controllers;

use Flare\Application\Event\Listener;
use Flare\Application\Event;
use Demo\Controller;

class Index_Controller extends Controller implements Listener
{
    public function index_action()
    {
        return $this->view('index');
    }

    public function listen(Event $event)
    {
        return $event->fire();
    }
}