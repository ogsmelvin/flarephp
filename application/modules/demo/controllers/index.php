<?php

namespace Demo\Controllers;

use Demo\Models\Users;
use Demo\Controller;

class Index_Controller extends Controller
{
    public function index_action()
    {
        $data = array();
        return $this->view('index', $data);
    }
}