<?php

namespace Demo\Controllers;

use Demo\Controller;

class Index_Controller extends Controller
{
    public function index_action()
    {
        return $this->view('index');
    }
}