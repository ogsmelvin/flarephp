<?php

namespace Main\Controllers;

use Main\Controller;

class Index_Controller extends Controller
{
    public function index_action()
    {
        $data = array();
        return $this->view('index', $data);
    }

    public function receive_action()
    {
        $file = $this->getFile('file');
        if ($file) {
            if (!$file->upload('./img', array('is_image' => true))) {
                show_error($file->getValidationError());
            }
        }
        $this->back();
    }
}