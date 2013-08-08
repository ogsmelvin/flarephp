<?php

namespace Demo\Controllers;

use Flare\Application\EventListener;
use Flare\View\Dom\Event;
use Demo\Controller;

class Index_Controller extends Controller implements EventListener
{
	public function index_action()
	{
		$html = $this->view('index')
			->addEventListener('#submitBtn', 'click', $this)
			->addEventListener('#submitBtn', 'submit', $this);
		return $html;
	}

	public function click_event(Event $event)
	{
		
	}
	
	public function submit_event(Event $event)
	{
		
	}

	public function listen(Event $event)
	{
		return $event->fire();
	}
}