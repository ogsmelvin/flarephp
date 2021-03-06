<?php

namespace Flare\Application;

use Flare\Application\AbstractController;

/**
 * 
 * @author anthony
 * 
 */
class Dispatcher
{
    /**
     * 
     * @param \Flare\Application\AbstractController $controller
     * @param string $adapter
     */
    public function __construct(AbstractController &$controller, $adapter = 'page')
    {
        $adapter = __CLASS__."\\Adapter\\".ucwords(strtolower($adapter));
        if ($controller->config->autoload['database']) {
            $controller->setDatabase($controller->config->autoload['database']);
        }
        if (!empty($controller->config->autoload['helpers'])) {
            foreach ($controller->config->autoload['helpers'] as $helper) {
                $controller->setHelper($helper);
            }
        }
        $this->_adapter = new $adapter($controller);
    }

    /**
     * 
     * @return \Flare\Application\Dispatcher\Adapter
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * 
     * @return void
     */
    public function dispatch()
    {
        $this->_adapter->dispatch();
    }
}