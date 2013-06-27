<?php

namespace Flare\Application\Router;

use Flare\Application\AbstractController;
use Flare\Application\Router\Route\Action;

/**
 * 
 * @author anthony
 * 
 */
class Route
{
    /**
     * 
     * @var \Flare\Application\AbstractController
     */
    private $_controller = null;

    /**
     * 
     * @var string
     */
    private $_module = null;

    /**
     * 
     * @var string
     */
    private $_action = null;

    /**
     * 
     * @var array
     */
    private $_params = array();

    /**
     * 
     * @return \Flare\Application\AbstractController
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * 
     * @param \Flare\Application\AbstractController
     * @return \Flare\Application\Router\Route
     */
    public function setController(AbstractController $controller)
    {
        $this->_controller = $controller;
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
     * @param string $module
     * @return \Flare\Application\Router\Route
     */
    public function setModule($module)
    {
        $this->_module = $module;
        return $this;
    }

    /**
     * 
     * @return \Flare\Application\Router\Route\Action
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * 
     * @param \Flare\Application\Router\Route\Action $action
     * @return \Flare\Application\Router\Route
     */
    public function setAction(Action $action)
    {
        $this->_action = $action;
        return $this;
    }

    /**
     * 
     * @param array $params
     * @return \Flare\Application\Router\Route
     */
    public function setActionParams($params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getActionParams()
    {
        return $this->_params;
    }
}