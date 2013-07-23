<?php

namespace Demo\Controllers;

use Demo\Models\Registrants;
use Demo\Controller;

class Process_Controller extends Controller
{
    public function register_action()
    {
        $photo = $this->getFile('photo');
        if ($this->request->post('submit') && $photo) {
            $validation = array(
                'is_image' => true
            );
            if (!$photo->upload('./img/registrants', $validation, true)) {
                $this->back(array(
                    'invalid' => $photo->getValidationError()
                ));
            }
        }
        $this->back(array('invalid' => null));
    }
}