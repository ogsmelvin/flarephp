<?php

namespace Flare\View\Response;

use Flare\Application\EventListener;
use Flare\View\Response;
use \DOMDocument;
use Flare\View;

/**
 * 
 * @author anthony
 * 
 */
class Html extends Response
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
	 * @var array
	 */
	private $_events = array();

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
	public function render()
	{

		$view = & $this->_view;
		extract($view->getVars());

		ob_start();
		include $this->_contentPath;
		$view->setContent((string) ob_get_clean());

		if ($this->_layoutPath) {
			ob_start();
			include $this->_layoutPath;
			$view->setContent((string) ob_get_clean());
		}

		if ($this->_events) {

			$dom = new DOMDocument();
			$dom->loadHTML($view->getContent());

			$src = $dom->createAttribute('src');
			$src->value = '';

			$type = $dom->createAttribute('type');
			$type->value = 'text/javascript';

			$script = $dom->createElement('script');
			$script->appendChild($src);
			$script->appendChild($type);

			foreach ($dom->getElementsByTagName('body') as $body) {
				$body->appendChild($script);
			}

			$view->setContent($dom->saveHTML());
			unset($script, $dom, $src, $type);

		}

		return $view->getContent();
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
	 * @param \Flare\Application\EventListener $listener
	 * @return \Flare\View\Response\Html
	 */
	public function addEvent($selector, EventListener &$listener)
	{
		$this->_events[] = $selector;
		return $this;
	}
}