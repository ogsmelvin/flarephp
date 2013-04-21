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
     * @var array
     */
    private $_files = array();

    /**
     * 'filename' => '',
     * 'path' => '',
     * 'extension' => '',
     * 'basename' => '',
     * 'type' => array(),
     * 'max_size' => 0
     * @var array
     */
    private $_upload = array();

    /**
     * 
     * @param \FPHP\Http\File|string|array $file
     */
    public function __construct($files = array())
    {
        if(!empty($files)){
            if(is_array($files)){
                $this->addFiles($files);
            }
            throw new Exception("Must be an array");
        }
    }

    /**
     * 
     * @param string $name
     * @return void
     */
    private function _setRules($name)
    {
        $this->_upload[$name] = array(
            'filename' => '',
            'path' => './uploads/',
            'extension' => '',
            'basename' => '',
            'type' => array(),
            'max_size' => 0
        );
    }

    /**
     * 
     * @param \FPHP\Http\File|string $file
     * @return \FPHP\Http\File\Uploader
     */
    public function addFile($file)
    {
        if(is_string($file)){
            $file = File::get($file);
        }
        $this->_files[$file->getName()] = $file;
        $this->_setRules($file->getName());
        return $this;
    }

    /**
     * 
     * @param array $files
     * @return \FPHP\Http\File\Uploader
     */
    public function addFiles(array $files)
    {
        foreach($files as $f){
            $f = File::get($f);
            $this->_files[$f->getName()] = $f;
            $this->_setRules($f->getName());
        }
        return $this;
    }

    /**
     * 
     * @param string $file
     * @return \FPHP\Http\File\Uploader
     */
    public function removeFile($file)
    {
        unset($this->_files[$file]);
        unset($this->_uploads[$file]);
        return $this;
    }

    /**
     * 
     * @return \FPHP\Http\File\Uploader
     */
    public function removeFiles()
    {
        $this->_files = array();
        $this->_uploads = array();
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getFiles()
    {
        return $this->_files;
    }

    /**
     * 
     * @param string $file
     * @return \FPHP\Http\File
     */
    public function getFile($file)
    {
        if(!isset($this->_files[$file])){
            return null;
        }
        return $this->_files[$file];
    }

    /**
     * 
     * @param string $name
     * @return boolean
     */
    public function upload($name = null)
    {
        $details = null;
        if($name === null){
            foreach($this->_files as $key => $file){
                $details[$key] = $this->_upload($key, $file);
            }
        } else {
            if(!isset($this->_files[$name])){
                throw new Exception("_FILE[{$name}] doesn't exists");
            }
            $details = $this->_upload($name, $this->_files[$name]);
        }
        return $details;
    }

    /**
     * 
     * @param string $key
     * @param \FPHP\Http\File $file
     * @return void
     */
    private function _upload($key, $file)
    {
        if(!$this->valid($key, $error)){
            $this->_error[$key] = $error;
            return false;
        }

        $rule = $this->_upload[$key];
        $ext = $file->getExtension();
        $new = $rule['filename'] ? $rule['filename'] : $file->getFilename();
        if($rule['extension']){
            $new .= $rule['extension'];
            $ext = $rule['extension'];
        } else if($rule['filename'] && !$rule['extension']){
            $new .= '.'.$file->getExtension();
        }

        if(!move_uploaded_file($file->getTempname(), $rule['path'].$new)){
            // display_error("Failed to upload {$new}");
            $this->_error[$key] = "Failed to upload {$new}";
            return false;
        }

        return array(
            'filename' => $new,
            'path' => $rule['path'].$new,
            'size' => $file->getSize(),
            'extension' => $ext
        );
    }

    /**
     * 
     * @param string $name
     * @param string $message
     * @return boolean
     */
    public function valid($name, &$message = null)
    {
        if(!isset($this->_files[$name])){
            $message = "_FILES[{$name}] doesn't exists";
            return false;
        }

        if(!isset($this->_upload[$name])){
            return true;
        }

        $file = $this->_files[$name];
        $rule = $this->_upload[$name];

        if(!$file->exists()){
            $message = "_FILES[{$name}] is not a valid file";
            return false;
        }

        $fileType = explode('/', $file->getType());
        if(!in_array(end($fileType), $rule['type']) 
            && !in_array(strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION)), $rule['type']))
        {
            $message = "{$name} Type is not allowed";
            return false;
        }
        unset($fileType);
        
        if($rule['max_size'] !== 0 && $file->getSize() > $rule['max_size']){
            $message = "{$name} Exceeds max size";
            return false;
        }

        if($file->getError()){
            $message = "{$name} has error '{$file->getError()}'";
            return false;
        }

        return true;
    }

    /**
     * 
     * @param string|array $val
     * @param string $key
     * @return \FPHP\Http\File\Uploader
     */
    public function settings($val, $key = null)
    {
        if($key === null && is_array($val)){
            foreach(array_keys($this->_files) as $file){
                foreach(array_keys($val) as $k){
                    $this->_set($file, $k, $val[$k]);
                }
            }
        } else if(is_string($val)){
            if($key === null){
                throw new Exception("{$key} must be defined if value is string");
            }
            foreach(array_keys($this->_files) as $file){
                $this->_set($file, $key, $val);
            }
        }
        return $this;
    }

    /**
     * 
     * @param string $name
     * @param string $key
     * @param mixed $value
     * @return void
     */
    private function _set($name, $key, $value)
    {
        if($key == 'path'){
            $value = rtrim($value, '/').'/';
        } else if($key == 'extension'){
            $value = ltrim($value, '.').'.';
        }
        $this->_upload[$name][$key] = $value;
    }

    /**
     * 
     * @param string $file
     * @param string $name
     * @param mixed $value
     * @return \FPHP\Http\File\Uploader
     */
    public function set($file, $name, $value)
    {
        if(!isset($this->_files[$file])){
            throw new Exception("{$file} doesn't exists");
        }
        $this->_set($file, $name, $value);
        return $this;
    }

    /**
     * 
     * @param string $key
     * @return string|array|null
     */
    public function getError($key = null)
    {
        if($key === null){
            return $this->_error;
        }
        return isset($this->_error[$key]) ? $this->_error[$key] : null;
    }
}