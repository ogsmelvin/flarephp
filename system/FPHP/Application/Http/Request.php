<?php

namespace FPHP\Application\Http;

use FPHP\Http\Request as ParentRequest;
use FPHP\Security\Xss;

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
     * @return \FPHP\Application\Http\Request
     */
    public function setModule($module)
    {
        $this->_module = $module;
        return $this;
    }

    /**
     *
     * @param string $controller
     * @return \FPHP\Application\Http\Request
     */
    public function setController($controller)
    {
        $sections = explode('_', urldecode($controller));
        foreach($sections as &$section){
            $section = strtolower($section);
        }

        $this->_controller = implode('_', $sections);
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
     * @return \FPHP\Application\Http\Request
     */
    public function setAction($action)
    {
        $this->_action = urldecode($action).'_action';
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