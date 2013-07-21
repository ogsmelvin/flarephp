<?php

namespace Flare\Application\Http;

use Flare\Http\Request as ParentRequest;
use Flare\Security\Xss;
use Flare\Flare as F;

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
        $sections = explode('_', urldecode($controller));
        foreach ($sections as &$section) {
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


    /**
     * 
     * @param string $key
     * @param boolean|null $xss
     * @return mixed
     */
    public function post($key = null, $xss = null)
    {
        $value = parent::post($key);
        if ($xss === null) {
            if (F::$config->get('auto_xss_filter') && $value) {
                return Xss::filter($value);
            }
        } elseif ($value) {
            return $xss === true ? Xss::filter($value) : $value;
        }
        return $value;
    }

    /**
     * 
     * @param string $key
     * @param boolean|null $xss
     * @return mixed
     */
    public function request($key = null, $xss = null)
    {
        $value = parent::request($key);
        if ($xss === null) {
            if (F::$config->get('auto_xss_filter') && $value) {
                return Xss::filter($value);
            }
        } elseif ($value) {
            return $xss === true ? Xss::filter($value) : $value;
        }
        return $value;
    }

    /**
     * 
     * @param string $key
     * @param boolean|null $xss
     * @return mixed
     */
    public function get($key = null, $xss = null)
    {
        $value = parent::get($key);
        if ($xss === null) {
            if (F::$config->get('auto_xss_filter') && $value) {
                return Xss::filter($value);
            }
        } elseif ($value) {
            return $xss === true ? Xss::filter($value) : $value;
        }
        return $value;
    }

    /**
     * 
     * @param string $key
     * @param boolean|null $xss
     * @return mixed
     */
    public function server($key = null, $xss = null)
    {
        $value = parent::server($key);
        if ($xss === null) {
            if (F::$config->get('auto_xss_filter') && $value) {
                return Xss::filter($value);
            }
        } elseif ($value) {
            return $xss === true ? Xss::filter($value) : $value;
        }
        return $value;
    }
}