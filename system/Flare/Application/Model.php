<?php

namespace Flare\Application;

use Flare\Flare as F;

/**
 * 
 * @author anthony
 * 
 */
abstract class Model
{
    /**
     * 
     * @return \Flare\Application\AbstractController
     */
    protected static function controller()
    {
        return F::getApp()->getController();
    }
}