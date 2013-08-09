<?php

namespace Demo\Controllers;

use Flare\Application\EventListener;
use Flare\View\Dom\Event;
use Demo\Controller;

class Index_Controller extends Controller implements EventListener
{
	public function index_action()
	{
		$listener = new IndexListener();
		$html = $this->view('index')
			->addEvent('#submitBtn', 'click', $listener)
			->addEvent('#submitBtn', 'submit', $listener);
		return $html;
	}

	public function click_event(Event $event)
	{
		
	}
	
	public function submit_event(Event $event)
	{
		$source = $event->getSource();
	}

	public function listen(Event $event)
	{
		
	}
}