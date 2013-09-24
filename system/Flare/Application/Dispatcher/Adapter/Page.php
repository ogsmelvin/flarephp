<?php

namespace Flare\Application\Dispatcher\Adapter;

use Flare\Application\Dispatcher\Adapter;
use Flare\Application\Window;
use Flare\View\Response\Html;
use Flare\View\Response;

/**
 * 
 * @author anthony
 * 
 */
class Page extends Adapter
{
    /**
     * 
     * @return boolean
     */
    public function dispatch()
    {
        $this->_controller->init();
        $this->_controller->predispatch();
        $view = null;
        if (!$this->_controller->router->getRoute()->getActionParams()) {
            $view = $this->_controller->{$this->_controller->request->getActionMethodName()}();
        } else {
            $view = call_user_func_array(
                array($this->_controller, $this->_controller->request->getActionMethodName()), 
                $this->_controller->router->getRoute()->getActionParams()
            );
        }

        if (!$this->_controller->response->hasContentType()) {
            if (!($view instanceof Response)) {
                if (!empty($view) && !is_string($view)) {
                    show_error("Action must return a 'View\Response' instance or string");
                } elseif ($this->_controller->config->default_content_type) {
                    $this->_controller->response->setContentType($this->_controller->config->default_content_type);
                }
            } else {
                $this->_controller->response->setContentType($view->getContentType());
            }
        }
        
        $this->_controller->postdispatch();
        if ($this->_controller->cookie->hasNewData()) {
            $this->_controller->response->addCookie(
                $this->_controller->cookie->getNamespace(),
                $this->_controller->cookie->serialize(),
                $this->_controller->cookie->getExpiration()
            );
        }

        if ($view instanceof Html && $this->_controller instanceof Window) {
            $scripts = $this->_controller->assets();
            $jsfile = $this->_controller->request->getModule()
                    .'/js/'
                    .$this->_controller->request->getController()
                    .'.js';
            $view->addScript($this->_controller->uri->baseUrl.bin2hex($jsfile).'.js', true);
            if (is_array($scripts)) {
                foreach ($scripts as $script) {
                    $view->addScript($this->_controller->uri->baseUrl.$script, true);
                }
            } else {
                $view->addScript($this->_controller->uri->baseUrl.$scripts, true);
            }
        }
        
        $this->_controller->response->setBody($view)->send();
        $this->_controller->complete();

        return true;
    }
}