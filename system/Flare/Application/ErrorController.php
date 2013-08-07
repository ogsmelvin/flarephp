<?php

namespace Flare\Application;

use Flare\Application\AbstractController;

/**
 * 
 * @author anthony
 * 
 */
abstract class ErrorController extends AbstractController
{
	/**
	 * 
	 * @return void
	 */
	public function init() {}

	/**
	 * 
	 * @return void
	 */
	public function complete() {}

	/**
	 * 
	 * @return string
	 */
	public function getErrorMessage()
	{
		return $this->response->getCodeMessage();
	}

	/**
	 * 
	 * @return int
	 */
	public function getErrorCode()
	{
		return $this->response->getCode();
	}
}