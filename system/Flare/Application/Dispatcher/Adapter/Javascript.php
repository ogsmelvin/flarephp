<?php

namespace Flare\Application\Dispatcher\Adapter;

use Flare\Application\Dispatcher\Adapter;
use Flare\View\Response\Javascript as Js;
use Flare\Application\Window;

/**
 * 
 * @author anthony
 * 
 */
class Javascript extends Adapter
{
    /**
     * 
     * @return boolean
     */
    public function dispatch()
    {
        if (!($this->_controller instanceof Window)) {
            return false;
        }

        $this->_controller->session->start();
        if (!$this->_controller->session->has(self::FLARE_JS_HEADER)) {
            $this->_controller->session->set(self::FLARE_JS_HEADER, md5(uniqid(mt_rand(), true)));
        }

        $js = new Js();
        $js->merge(FLARE_DIR.'Flare/Application/Window/lib.js');
        $js->write(
            "\nwindow.App = new flare.Application();\n".
            "App.Config.baseUrl = \"".$this->_controller->uri->base."\";\n".
            "App.Config.token = \"".$this->_controller->session->get(self::FLARE_JS_HEADER)."\";\n".
            "App.Config.header = \"".self::FLARE_JS_HEADER."\";\n".
            "App.Config.pageId = \"".bin2hex($this->_controller->request->getModule().'/'.$this->_controller->request->getController())."\";\n".
            "App.run();"
        );

        $this->_controller->response
            ->setContentType($js->getContentType())
            ->setBody($js)
            ->send();

        return true;
    }
}