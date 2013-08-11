<?php

namespace Flare\View\Response;

use Flare\View\Response;
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
}