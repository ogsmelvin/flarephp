<?php

namespace Flare;

use Flare\Security\Xss;

/**
 * 
 * @author anthony
 * 
 */
class View
{
	/**
	 * 
	 * @var \Flare\View
	 */
	private static $instance;

	/**
	 * 
	 * @var string
	 */
	private $content;

	/**
	 * 
	 * @var string
	 */
	private $path;

	/**
	 * 
	 * @var array
	 */
	private $sections = array();

	/**
	 * 
	 * @var array
	 */
	private $vars = array();

	private function __construct() {}

	/**
	 * 
	 * @return \Flare\View
	 */
	public static function create()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * 
	 * @param string $string
	 * @return string
	 */
	public function escape($string)
	{
		return Xss::filter($string);
	}

	/**
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return \Flare\View
	 */
	public function setVar($key, $value)
	{
		$this->vars[$key] = $value;
		return $this;
	}

	/**
	 * 
	 * @param string $path
	 * @return \Flare\View
	 */
	public function setIncludePath($path)
	{
		$folder = realpath($path);
		$folder = $folder !== false ? rtrim(str_replace("\\", '/', $folder), '/').'/' : rtrim($path, '/').'/';
		$this->path = $folder;
		return $this;
	}

	/**
	 * 
	 * @param string $path
	 * @param boolean $useIncludePath
	 * @return string
	 */
	public function render($path, $useIncludePath = true)
	{
		extract($this->vars);
		ob_start();
		if ($useIncludePath && $this->path) {
			include $this->path.rtrim(ltrim($path, '/'), '.php').'.php';
		} elseif (!$useIncludePath) {
			include rtrim($path, '.php').'.php';
		}
		return (string) ob_get_clean();
	}

	/**
	 * 
	 * @param string $name
	 * @return void
	 */
	public function sectionOpen($name)
	{
		end($this->sections);
		$lastSection = key($this->sections);
		if ($lastSection && $this->sections[$lastSection] === true) {
			show_error("'{$lastSection}' section is currently open");
		} elseif (isset($this->sections[$name])) {
			show_error("'{$name}' section is already open");
		}
		$this->sections[$name] = true;
		reset($this->sections);
		ob_start();
	}

	/**
	 * 
	 * @return void
	 */
	public function sectionClose()
	{
		end($this->sections);
		$name = key($this->sections);
		if (!isset($this->sections[$name])) {
			show_error("'{$name}' is not yet open");
		}
		reset($this->sections);
		$this->sections[$name] = (string) ob_get_clean();
	}


	/**
	 * 
	 * @param string $name
	 * @return string
	 */
	public function getSection($name)
	{
		if (!isset($this->sections[$name])) {
			return null;
		}
		return $this->sections[$name];
	}


	/**
	 * 
	 * @return array
	 */
	public function getSections()
	{
		return $this->sections;
	}

	/**
	 * 
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * 
	 * @param string $content
	 * @return \Flare\View
	 */
	public function setContent($content)
	{
		$this->content = $content;
		return $this;
	}

	/**
	 * 
	 * @return array
	 */
	public function & getVars()
	{
		return $this->vars;
	}

	/**
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->getContent();
	}
}