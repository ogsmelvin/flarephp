<?php

namespace Demo\Controllers;

use Flare\Application\Window;
use Demo\Controller;

class Index_Controller extends Controller implements Window
{
    public function index_action()
    {
        return $this->view('index');
    }
    
    public function load()
    {
        
    }
}