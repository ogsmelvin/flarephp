<?php

namespace ADK\Application;

/**
 * 
 * @author anthony
 * 
 */
abstract class Controller
{
    /**
     * 
     * @return void
     */
    abstract public function init();

    /**
     * 
     * @return void
     */
    abstract public function complete();
}