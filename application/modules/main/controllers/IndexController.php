<?php

namespace Main\Controllers;

use Main\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        $data = array();
        return $this->view('index', $data);
    }
}