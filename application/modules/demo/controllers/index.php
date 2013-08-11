<?php

namespace Demo\Controllers;

use Flare\Application\EventListener;
use Flare\Application\Event;
use Demo\Controller;

class Index_Controller extends Controller implements EventListener
{
	public function index_action()
	{	
		return $this->view('index');
	}

	public function click_event(Event $event)
	{
		
	}
	
	public function submit_event(Event $event)
	{
		
	}
	
	public function listen(Event $event)
	{
		
	}
}