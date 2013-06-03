<?php

namespace FPHP\Http\File;

use FPHP\Http\File;
use \Exception;

/**
 * 
 * @author anthony
 * 
 */
class Uploader
{
    /**
     * 
     * @param string $name
     * @param array $config
     * @return array|false
     */
    public static function upload($name, $config = array())
    {
        $file = File::get($name);
        // if(is_array($file)){
        //     foreach($file as $key => $content){
        //         move_uploaded_file($content->getTempname(), destination)
        //     }
        // }
    }
}