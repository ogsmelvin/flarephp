<?php

namespace FPHP\Application\Router;

use FPHP\Application\AbstractController;
use FPHP\Application\Router\Route\Action;

/**
 * 
 * @author anthony
 * 
 */
class Route
{
    /**
     * 
     * @var \FPHP\Application\AbstractController
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
     * @return \FPHP\Application\AbstractController
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * 
     * @param \FPHP\Application\AbstractController
     * @return \FPHP\Application\Router\Route
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
     * @return \FPHP\Application\Router\Route
     */
    public function setModule($module)
    {
        $this->_module = $module;
        return $this;
    }

    /**
     * 
     * @return \FPHP\Application\Router\Route\Action
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * 
     * @param \FPHP\Application\Router\Route\Action $action
     * @return \FPHP\Application\Router\Route
     */
    public function setAction(Action $action)
    {
        $this->_action = $action;
        return $this;
    }

    /**
     * 
     * @param array $params
     * @return \FPHP\Application\Router\Route
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