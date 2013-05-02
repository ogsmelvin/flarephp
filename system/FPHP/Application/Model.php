<?php

namespace FPHP\Application;

use FPHP\Fphp as F;

/**
 * 
 * @author anthony
 * 
 */
abstract class Model
{
    /**
     * 
     * @return \FPHP\Application\AbstractController
     */
    public static function appInstance()
    {
        return F::mvc()->getController();
    }
}