<?php

namespace Flare\View;

/**
 * 
 * @author anthony
 * 
 */
interface DOM
{
	/**
	 * 
	 * @param string $id
	 * @return \Flare\View\DOM\Element
	 */
	public function getElementById($id);
	
	/**
	 * 
	 * @param string $name
	 * @return \Flare\Util\Collection
	 */
	public function getElementsByTagName($name);
}