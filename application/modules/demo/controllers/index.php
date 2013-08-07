<?php

namespace Demo\Controllers;

use Flare\Application\EventListener;
use Flare\View\Event;
use Demo\Controller;

class Index_Controller extends Controller implements EventListener
{
	public function index_action()
	{
		return $this->view('index');
	}

	public function submit_event()
	{
		
	}

	public function listen(Event $event)
	{		
		return $event->fire();
	}
}