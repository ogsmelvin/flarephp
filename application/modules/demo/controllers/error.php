<?php

namespace Demo\Controllers;

use Flare\Application\ErrorController;

class Error_Controller extends ErrorController
{
    public function index_action()
    {
        debug($this->getErrorCode().' : '.$this->getErrorMessage());
    }
}