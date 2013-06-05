<?php

namespace FPHP\Http;

use \Exception;

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
        'overwrite' => true

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
     * @var string
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
     */
    private function __construct($name, $filename, $tmpname, $type, $error, $size)
    {
        $this->_name = $name;
        $this->_tmpname = $tmpname
        $this->_type = $type;
        $this->_error = $error
        $this->_size = $size;
        $this->_filename = self::cleanFilename($filename);
        $this->_extension = pathinfo($this->_filename, PATHINFO_EXTENSION);
    }

    /**
     * 
     * @param string $name
     * @param mixed $default
     * @return \FPHP\Http\File|array
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
     * @return string
     */
    public function getError()
    {
        return $this->_error;
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
     * @param string $to
     * @param array $config
     * @return array|false
     */
    public function move($to, $config = array())
    {
        $result = false;
        $config = !$config ? self::$_config : array_merge(self::$_config, $config);
        $to = $this->_validateUploadPath($to);
        if($to){
            if(!is_uploaded_file($this->_tmpname)){
                $error = ( ! isset($this->_error)) ? 4 : $this->_error;
                switch($error){
                    case 1: // UPLOAD_ERR_INI_SIZE
                        $this->_setMoveError('Upload file exceeds limit');
                        break;
                    case 2: // UPLOAD_ERR_FORM_SIZE
                        $this->_setMoveError('Upload file exceeds form limit');
                        break;
                    case 3: // UPLOAD_ERR_PARTIAL
                        $this->_setMoveError('Upload file partial');
                        break;
                    case 4: // UPLOAD_ERR_NO_FILE
                        $this->_setMoveError('Upload no file selected');
                        break;
                    case 6: // UPLOAD_ERR_NO_TMP_DIR
                        $this->_setMoveError('Upload no temp directory');
                        break;
                    case 7: // UPLOAD_ERR_CANT_WRITE
                        $this->_setMoveError('Upload unable to write file');
                        break;
                    case 8: // UPLOAD_ERR_EXTENSION
                        $this->_setMoveError('Upload stopped by extension');
                        break;
                    default : $this->_setMoveError('Upload no file selected');
                        break;
                }
            } else {

            }
        } else {
            $this->_setMoveError("Upload path doesn't exists or not writable");
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
     * @return string
     */
    public function getMoveError()
    {
        return $this->_moveError;
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
            return $moveTo."/".$this->_filename;
        } else if(@is_file($moveTo)){
            return $moveTo;
        }
        return false;
    }

    /**
     * 
     * @param array $config
     * @return void
     */
    public static function setUploadConfig(array $config)
    {
        foreach($config as $key => $conf){
            if(array_key_exists($key, self::$_config)){
                self::$_config[$key] = $conf;
            }
        }
    }

    /**
     * 
     * @return array
     */
    public static function getUploadConfig()
    {
        return self::$_config;
    }

    /**
     * Clean the file name for security
     *
     * @param   string
     * @return  string
     */
    public static function cleanFilename($filename)
    {
        $bad = array("<!--", "-->", "'", "<", ">", '"', '&', '$', '=', ';', '?',
                    '/', "%20", "%22",
                    "%3c",      // <
                    "%253c",    // <
                    "%3e",      // >
                    "%0e",      // >
                    "%28",      // (
                    "%29",      // )
                    "%2528",    // (
                    "%26",      // &
                    "%24",      // $
                    "%3f",      // ?
                    "%3b",      // ;
                    "%3d"       // =
                );
        $filename = str_replace($bad, '', $filename);
        return stripslashes($filename);
    }
}