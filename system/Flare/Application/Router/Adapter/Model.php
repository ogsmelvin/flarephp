<?php

namespace Flare\Application\Router\Adapter;

use Flare\Application\Router\Adapter;
use Flare\Flare as F;

/**
 * 
 * @author anthony
 * 
 */
class Model extends Adapter
{
    /**
     * 
     * @return \Flare\Application\Router\Route
     */
    public function getRoute()
    {
        $controller = pack('H*', F::$uri->getSegment(1));
        @list($module, $controller) = explode('/', $controller, 2);
        if (!isset($module, $controller)) {
            show_response(404);
        }

        return $this->_route($module, $controller);
    }
}