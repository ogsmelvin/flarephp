<?php

namespace Flare\Application\Router\Adapter;

use Flare\Application\Router\Adapter;
use Flare\Flare as F;

/**
 * 
 * @author anthony
 * 
 */
class Javascript extends Adapter
{
    /**
     * 
     * @return \Flare\Application\Router\Route
     */
    public function getRoute()
    {
        $location = pack('H*', pathinfo(F::$uri->getSegment(1), PATHINFO_FILENAME));
        @list($module, $location, $controller) = explode('/', $location, 3);
        if (!isset($module, $location, $controller)) {
            show_response(404);
        }
        
        $controller = pathinfo($controller, PATHINFO_FILENAME);
        $route = $this->_route($module, $controller);
        return $route;
    }
}