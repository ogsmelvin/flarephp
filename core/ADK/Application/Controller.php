<?php

namespace ADK\Application;

/**
 * 
 * @author anthony
 * 
 */
abstract class Controller
{
    public function __construct(){}

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