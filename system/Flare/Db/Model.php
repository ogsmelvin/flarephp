<?php

namespace Flare\Db;

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
    public function getController()
    {
        return F::getApp()->getController();
    }
}