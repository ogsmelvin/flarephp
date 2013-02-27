<?php

namespace ADK\Application\Http;

use ADK\Http\Request as ParentRequest;

/**
 * 
 * @author anthony
 * 
 */
class Request extends ParentRequest
{
    /**
     *
     * @var string
     */
    private $_module = null;

    /**
     *
     * @var string
     */
    private $_controller = null;

    /**
     *
     * @var string
     */
    private $_action = null;

    /**
     *
     * @param string $module
     * @return \ADK\Application\Http\Request
     */
    public function setModule($module)
    {
        $this->_module = $module;
        return $this;
    }

    /**
     *
     * @param string $controller
     * @return \ADK\Application\Http\Request
     */
    public function setController($controller)
    {
        $this->_controller = ucwords(urldecode($controller)).'Controller';
        return $this;
    }

    /**
     *
     * @param string $action
     * @return \ADK\Application\Http\Request
     */
    public function setAction($action)
    {
        $this->_action = urldecode($action).'Action';
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     *
     * @return string
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     *
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }
}