<?php

namespace Demo\Controllers;

use Flare\Application\EventListener;
use Flare\View\Event;
use Demo\Controller;

class Index_Controller extends Controller implements EventListener
{
	public function index_action()
	{
		$view = $this->view('index');
		$view->getElementById('submitBtn')->addEventListener('click', $this);
		return $view;
	}

	public function submit_event()
	{
		
	}

	public function listen(Event $event)
	{		
		return $event->fire();
	}
}