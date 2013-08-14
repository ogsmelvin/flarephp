<?php

namespace Demo\Controllers;

use Flare\Application\ErrorController;

class Error_Controller extends ErrorController
{
    public function index_action()
    {
        return $this->view('error', array(
            'errorCode' => $this->getErrorCode(),
            'errorMessage' => $this->getErrorMessage()
        ));
    }
}