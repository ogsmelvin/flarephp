<?php

namespace Flare\View\Response;

use Flare\Application\EventListener;
use Flare\Util\Collection;
use Flare\View\Dom\Event;
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
	 * @var \Flare\Util\Collection
	 */
	private $_events;

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
	 * @return string
	 */
	private function _compile()
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
		return $view->getContent();
	}

	/**
	 * 
	 * @return string
	 */
	public function render()
	{
		return $this->_compile();
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
	public function addEvent($selector, $event, EventListener &$listener)
	{
		$event .= '_event';
		if (!method_exists($listener, $event)) {
			show_error("Listener doesn't have '{$event}' method");
		}
		if (!$this->_events) {
			$this->_events = new Collection();
		}
		$this->_events[$selector][] = new Event($event, $selector);
		return $this;
	}
	
	/**
	 * 
	 * @param string $selector
	 * @param string $event
	 * @return \Flare\View\Response\Html
	 */
	public function removeEvent($selector, $event = null)
	{
		if ($event) {
			$event .= '_event';
			if (isset($this->_events[$selector])) {
				foreach ($this->_events[$selector] as $key => $evt) {
					if ($evt->name === $event) 
						unset($this->_events[$selector][$key]);
				}
			}
		} else {
			unset($this->_events[$selector]);
		}
		return $this;
	}
	
	/**
	 * 
	 * @return \Flare\Util\Collection
	 */
	public function getEvents()
	{
		return $this->_events;
	}
}