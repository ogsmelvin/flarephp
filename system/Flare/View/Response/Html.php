<?php

namespace Flare\View\Response;

use Flare\View\DOM\Element;
use Flare\View\Response;
use Flare\View\DOM;
use Flare\View;

/**
 * 
 * @author anthony
 * 
 */
class Html extends Response implements DOM
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
		include $this->_contentPath;
		$view->setContent((string) ob_get_clean());

		if ($this->_layoutPath) {
			ob_start();
			include $this->_layoutPath;
			$view->setContent((string) ob_get_clean());
		}
		
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
	 * @param string $id
	 * @return \Flare\View\DOM\Element
	 */
	public function getElementById($id)
	{
		$element = $this->_view->getDOMDocument()->getElementById($id);
		if ($element) {
			$element = new Element();
			$element->setId($id);
		}
		return $element;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return \Flare\Util\Collection
	 */
	public function getElementsByTagName($name)
	{
		
	}
}