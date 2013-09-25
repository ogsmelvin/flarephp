<?php

namespace Flare\Application\Http;

use Flare\Http\Request as ParentRequest;

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
    private $_module;

    /**
     *
     * @var string
     */
    private $_controller;

    /**
     *
     * @var string
     */
    private $_action;

    /**
     *
     * @param string $module
     * @return \Flare\Application\Http\Request
     */
    public function setModule($module)
    {
        $this->_module = $module;
        return $this;
    }

    /**
     *
     * @param string $controller
     * @return \Flare\Application\Http\Request
     */
    public function setController($controller)
    {
        $this->_controller = strtolower(urldecode($controller));
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getControllerClassName()
    {
        return str_replace(' ', '_', ucwords(str_replace('_', ' ', $this->_controller))).'_Controller';
    }

    /**
     *
     * @param string $action
     * @return \Flare\Application\Http\Request
     */
    public function setAction($action)
    {
        $this->_action = urldecode($action);
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

    /**
     * 
     * @return string
     */
    public function getActionMethodName()
    {
        return $this->_action.'_action';
    }
}