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
    public static function getController()
    {
        return F::getApp()->getController();
    }
}