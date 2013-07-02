<?php

namespace Flare\Http;

use Flare\Security\File as FileSec;
use Flare\Security\Hash;
use Flare\Objects\Image;

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
     * 
     */
    private static $_removeMultipleExtension = false;

    /**
     * 
     * @var array
     */
    private static $_rules = array(
        'is_image' => null,
        'max_size' => null,
        'min_size' => null,
        'max_height' => null,
        'max_width' => null,
        'types' => null
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
    private $_mimeType;

    /**
     * 
     * @var boolean
     */
    private $_moved = false;

    /**
     * 
     * @var boolean
     */
    private $_isImage = false;

    /**
     * 
     * @var string
     */
    private $_moveError = null;

    /**
     * 
     * @var int
     */
    private $_width = 0;

    /**
     * 
     * @var int
     */
    private $_height = 0;

    /**
     * 
     * @param string $name
     * @return \Flare\Http\File|null
     */
    public static function & get($name)
    {
        if (!isset(self::$_instances[$name])) {
            if (!isset($_FILES[$name])) {
                return null;
            }
            self::$_instances[$name] = new self($name);
        }
        return self::$_instances[$name];
    }

    /**
     * 
     * @param array $validations
     * @return void
     */
    public static function validations(array $validations)
    {
        foreach ($validations as $key => $value) {
            self::validation($key, $value);
        }
    }

    /**
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function validation($key, $value)
    {
        if (!isset(self::$_rules[$key])) {
            show_error("File validation '{$key}' : unknown validation");
        }
        self::$_rules[$key] = $value;
    }

    /**
     * 
     * @return array
     */
    public static function getErrorCodes()
    {
        return self::$_errorCodes;
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
        if (count($source) !== 2) {
            show_error("Invalid base64 string");
        }

        $type = substr(substr($source[0], 0, -7), 5);
        $ext = explode('/', $type);
        $createpath = realpath($path);
        $createpath = $createpath !== false ? rtrim(str_replace("\\", "/", $createpath), "/") : rtrim($path, "/");
        
        if (@is_dir($createpath) === true) {
            $filename = Hash::create($source[1]).'.'.end($ext);
            $createpath .= '/'.$filename;
        } else {
            $filename = pathinfo($createpath, PATHINFO_FILENAME);
            $fileExt = pathinfo($createpath, PATHINFO_EXTENSION);
            if (!$fileExt) {
                $createpath .= $filename.'.'.end($ext);
            } else {
                $createpath .= $filename.'.'.$fileExt;
            }
        }
        if (file_put_contents($createpath, base64_decode(str_replace(' ', '+', $source[1])))) {
            $result = new self(null, $filename, $filename, $type, 0, 0);
        }
        return $result;
    }

    /**
     * 
     * @param string $name
     */
    private function __construct($name)
    {
        $this->_name = $name;
        $this->_tmpname = $_FILES[$name]['tmp_name'];
        $this->_error = (int) $_FILES[$name]['error'];
        $this->_size = (int) $_FILES[$name]['size'];
        $this->_filename = FileSec::sanitizeFilename($_FILES[$name]['name']);
        $this->_extension = pathinfo($this->_filename, PATHINFO_EXTENSION);
        $this->_setMimeType();
        $this->_setAsImage();
    }

    /**
     * 
     * @return void
     */
    private function _setMimeType()
    {
        $type = null;
        if (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME);
            if ($finfo) {
                $mime = finfo_file($finfo, $this->_tmpname);
                if ($mime && preg_match('/^([a-z\-]+\/[a-z0-9\-\.\+]+)(;\s.+)?$/', $mime, $match)) {
                    $type = $match[1];
                }
                finfo_close($finfo);
            }
        } elseif (DIRECTORY_SEPARATOR == '/') {
            if (function_exists('exec')) {
                $type = exec('file --brief --mime-type '.escapeshellarg($this->_tmpname));
            }
        }

        if (!$type && function_exists('mime_content_type')) {
            $type = mime_content_type($this->_tmpname);
        }

        $this->_type = !$type ? $_FILES[$this->_name]['type'] : $type;
    }

    /**
     * 
     * @param string $filename
     * @return string
     */
    private function _removeMultipleExt($filename)
    {
        $exts = explode('.', $filename);
        $ext = array_pop($exts);
        $filename = array_shift($exts);

        foreach ($exts as $e) {
            
        }

        return $filename.'.'.$ext;
    }

    /**
     * 
     * @return boolean
     */
    public function valid()
    {
        $valid = true;
        if (!file_exists($this->_tmpname)) {
            $valid = false;
        } elseif (is_boolean(self::$_rules['is_image'])) {
            if (self::$_rules['is_image'] !== $this->isImage()) {
                $valid = false;
            } elseif ((self::$_rules['max_width'] && $this->_width > (int) self::$_rules['max_width'])
                || (self::$_rules['max_height'] && $this->_height > (int) self::$_rules['max_height']))
            {
                $valid = false;
            }
        }

        if ($valid) {
            if (is_array(self::$_rules['types']) && !empty(self::$_rules['types'])
                && !in_array($this->_extension, self::$_rules['types']))
            {
                $valid = false;
            } elseif (self::$_rules['min_size'] && $this->_size < (int) self::$_rules['min_size']) {
                $valid = false;
            } elseif (self::$_rules['max_size'] && $this->_size > (int) self::$_rules['max_size']) {
                $valid = false;
            }
        }
        
        return $valid;
    }

    /**
     * 
     * @return void
     */
    private function _setAsImage()
    {
        if (function_exists('getimagesize')) {
            list($width, $height, $type) = getimagesize($this->_tmpname);
            if ($width && $height && in_array($type, get_image_types()) {
                $this->_isImage = true;
                $this->_width = $width;
                $this->_height = $height;
            }
        }

        if (!$this->_isImage && in_array($this->_type, get_image_mime_types())) {
            $this->_isImage = true;
        }
    }

    /**
     * 
     * @return boolean
     */
    public function isImage()
    {
        return $this->_isImage;
    }

    /**
     * 
     * @return string
     */
    public function getFilename()
    {
        return $this->_filename;
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
    public function getMimeType()
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
        if (!isset(self::$_errorCodes[$this->_error])) {
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
    public function upload($destination, $filename = null)
    {
        $result = false;
        if ($filename) {
            $filename = FileSec::sanitizeFilename($filename);
            // $filename = $this->_secureFilename($filename);
        } else {
            $filename = $this->_filename;
        }

        $to = $this->_validateUploadPath($to);
        if ($to) {
            if (is_uploaded_file($this->_tmpname)) {
                
            } else {
                $this->_setUploadError($this->getErrorMessage());
            }
        } else {
            $this->_setUploadError("Upload path doesn't exists or not writable");
        }

        $this->_moved = $result;
        return $result;
    }

    /**
     * 
     * @param string $error
     * @return void
     */
    private function _setUploadError($error)
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
        if (!$to) return false;
        $moveTo = realpath($to);
        $moveTo = $moveTo !== false ? rtrim(str_replace("\\", "/", $moveTo), "/") : rtrim($to, "/");

        if (@is_dir($moveTo)) {
            return $moveTo."/";
        }
        return false;
    }
}