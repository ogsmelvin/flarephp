<?php

namespace Flare\View\Response;

use Flare\Application\EventListener;
use Flare\View\Response;
use Flare\View\Dom;
use Flare\View;

/**
 * 
 * @author anthony
 * 
 */
class Html extends Response implements Dom
{
	/**
	 * 
	 * @var string
	 */
	protected $contentType = 'text/html';

	/**
	 * 
	 * @var string
	 */
	private $_contentPath;

	/**
	 * 
	 * @var string
	 */
	private $_layoutPath;

	/**
	 * 
	 * @var \Flare\View
	 */
	private $_view;
	
	/**
	 *
	 * @var array
	 */
	private $_events = array();

	/**
	 * 
	 * @param string $path
	 */
	public function __construct($path)
	{
		$this->_view = View::create();
		$this->_contentPath = $path;
	}

	/**
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return \Flare\View\Response\Html
	 */
	public function set($key, $value)
	{
		$this->_view->setVar($key, $value);
		return $this;
	}
    
	/**
	 * 
	 * @return \Flare\View\Response\Html
	 */
	public function compile()
	{
		$view = & $this->_view;
		extract($view->getVars());
		
		ob_start();
		if ($this->_layoutPath) {
			
			include $this->_contentPath;
			$view->setLayoutContent((string) ob_get_clean());

			ob_start();
			include $this->_layoutPath;
			
		} else {
			include $this->_contentPath;
		}
		$view->setContent((string) ob_get_clean());
		
		return $this;
	}

	/**
	 * 
	 * @return string
	 */
	public function render()
	{
		return (string) $this->_view;
	}

	/**
	 * 
	 * @param string $file
	 * @return \Flare\View\Response\Html
	 */
	public function setLayout($file)
	{
		$this->_layoutPath = $file;
		return $this;
	}
	
	/**
	 * 
	 * @param string $selector
	 * @param string $event
	 * @param \Flare\Application\EventListener $listener
	 * @return \Flare\View\Response\Html
	 */
	public function addEventListener($selector, $event, EventListener &$listener)
	{
		$event = rtrim($event, '_event').'_event';
		if (!method_exists($listener, $event)) {
			show_error("Listener doesn't have '{$event}' method");
		}
		$this->_events[$selector] = array(
			'event' => $event,
			'listener' => $listener
		);
		return $this;
	}
	
	/**
	 * 
	 * @param string $selector
	 * @param string $event
	 * @return \Flare\View\Response\Html
	 */
	public function removeEventListener($selector, $event)
	{
		return $this;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getEvents()
	{
		return array();
	}
}