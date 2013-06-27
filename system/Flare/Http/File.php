<?php

namespace Flare\Http;

use Flare\Security\File as FileSec;
use Flare\Security\Hash;

/**
 * 
 * @author anthony
 * 
 */
class File
{
    /**
     * 
     * @var array
     */
    private static $_instances = array();

    /**
     * 
     * @var array
     */
    private static $_config = array(
        'overwrite' => true,
        'filename' => ''
    );

    /**
     * 
     * @var array
     */
    private static $_errorCodes = array(
        UPLOAD_ERR_INI_SIZE => 'Upload file exceeds limit', // 1
        UPLOAD_ERR_FORM_SIZE => 'Upload file exceeds form limit', // 2
        UPLOAD_ERR_PARTIAL => 'Upload file partial', // 3
        UPLOAD_ERR_NO_FILE => 'Upload no file selected', // 4
        UPLOAD_ERR_NO_TMP_DIR => 'Upload no temp directory', // 6
        UPLOAD_ERR_CANT_WRITE => 'Upload unable to write file', // 7
        UPLOAD_ERR_EXTENSION => 'Upload stopped by extension', // 8
        0 => 'Unkown error' // 0
        // UPLOAD_ERR_OK => 'No Error'
    );

    /**
     * 
     * @var string
     */
    private $_name;

    /**
     * 
     * @var string
     */
    private $_tmpname;

    /**
     * 
     * @var string
     */
    private $_filename;

    /**
     * 
     * @var string
     */
    private $_extension;

    /**
     * 
     * @var string
     */
    private $_type;

    /**
     * 
     * @var int
     */
    private $_error;

    /**
     * 
     * @var int
     */
    private $_size;

    /**
     * 
     * @var string
     */
    private $_moveError = null;

    /**
     * 
     * @param string $name
     * @param string $filename
     * @param string $tmpname
     * @param string $type
     * @param int $error
     * @param int $size
     */
    private function __construct($name, $filename, $tmpname, $type, $error, $size)
    {
        $this->_name = $name;
        $this->_tmpname = $tmpname;
        $this->_type = $type;
        $this->_error = (int) $error;
        $this->_size = (int) $size;
        $this->_filename = FileSec::sanitizeFilename($filename, false);
        $this->_extension = pathinfo($this->_filename, PATHINFO_EXTENSION);
    }

    /**
     * 
     * @param string $name
     * @param mixed $default
     * @return \Flare\Http\File|array
     */
    public static function get($name, $default = null)
    {
        if(!isset(self::$_instances[$name])){
            if(!isset($_FILES[$name])){
                return $default;
            }
            if(is_array($_FILES[$name]['name'])){
                foreach($_FILES[$name]['name'] as $key => $file){
                    self::$_instances[$name][] = new self(
                        $name,
                        $file,
                        $_FILES[$name]['tmp_name'][$key],
                        $_FILES[$name]['type'][$key],
                        $_FILES[$name]['error'][$key],
                        $_FILES[$name]['size'][$key]
                    );
                }
            } else {
                self::$_instances[$name] = new self(
                    $name,
                    $_FILES[$name]['name'],
                    $_FILES[$name]['tmp_name'],
                    $_FILES[$name]['type'],
                    $_FILES[$name]['error'],
                    $_FILES[$name]['size']
                );
            }
        }
        return self::$_instances[$name];
    }

    /**
     * 
     * @return string
     */
    public function getFilename()
    {
        return $this->_name;
    }

    /**
     * 
     * @return string
     */
    public function getExtension()
    {
        return $this->_extension;
    }

    /**
     * 
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * 
     * @return string
     */
    public function getTempname()
    {
        return $this->_tmpname;
    }

    /**
     * 
     * @return int
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * 
     * @return int
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * 
     * @return string
     */
    public function getErrorMessage()
    {
        if(!isset(self::$_errorCodes[$this->_error])){
            return self::$_errorCodes[0];
        }
        return self::$_errorCodes[$this->_error];
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * 
     * @param string $destination
     * @param string $filename
     * @return boolean
     */
    public function move($destination, $filename = null)
    {
        $result = false;
        $config = !$config ? self::$_config : array_merge(self::$_config, $config);
        $to = $this->_validateUploadPath($to);
        if($to){
            if(is_uploaded_file($this->_tmpname)){
                
            } else {
                $this->_setMoveError($this->getErrorMessage());
            }
        } else {
            $this->_setMoveError("Upload path doesn't exists or not writable");
        }
        return $result;
    }

    /**
     * 
     * @return boolean
     */
    public function valid()
    {
        return true;
    }

    /**
     * 
     * @param string $base64String
     * @param string $path
     * @return \Flare\Http\File|boolean
     */
    public static function createFromString($base64String, $path)
    {
        $result = false;
        $source = explode(',', $base64String, 2);
        if(count($source) !== 2){
            show_error("Invalid base64 string");
        }

        $type = substr(substr($source[0], 0, -7), 5);
        $ext = explode('/', $type);
        $createpath = realpath($path);
        $createpath = $createpath !== false ? rtrim(str_replace("\\", "/", $createpath), "/") : rtrim($path, "/");
        
        if(@is_dir($createpath) === true){
            $filename = Hash::create($source[1]).'.'.end($ext);
            $createpath .= '/'.$filename;
        } else {
            $filename = pathinfo($createpath, PATHINFO_FILENAME);
            $fileExt = pathinfo($createpath, PATHINFO_EXTENSION);
            if(!$fileExt){
                $createpath .= $filename.'.'.end($ext);
            } else {
                $createpath .= $filename.'.'.$fileExt;
            }
        }
        if(file_put_contents($createpath, base64_decode(str_replace(' ', '+', $source[1])))){
            $result = new self(null, $filename, $filename, $type, 0, 0);
        }
        return $result;
    }

    /**
     * 
     * @param string $error
     * @return void
     */
    private function _setMoveError($error)
    {
        $this->_moveError = $error;
    }

    /**
     * 
     * @param string $to
     * @return string|boolean
     */
    private function _validateUploadPath($to)
    {
        if(!$to) return false;
        $moveTo = realpath($to);
        $moveTo = $moveTo !== false ? rtrim(str_replace("\\", "/", $moveTo), "/") : rtrim($to, "/");

        if(@is_dir($moveTo)){
            return $moveTo."/";
        }
        return false;
    }

    /**
     * 
     * @param array $validation
     * @return void
     */
    public static function setValidation(array $validation)
    {
        self::$_validation = array_merge(self::$_validation, $validation);
    }

    /**
     * 
     * @return array
     */
    public static function getValidation()
    {
        return self::$_validation;
    }

    /**
     * 
     * @return array
     */
    public static function getErrorCodes()
    {
        return self::$_errorCodes;
    }
}