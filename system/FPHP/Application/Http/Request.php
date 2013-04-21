<?php

namespace FPHP\Application\Http;

use FPHP\Http\Request as ParentRequest;

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
            $section = ucwords($section);
        }
        $this->_controller = implode('_', $sections).'_Controller';
        return $this;
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