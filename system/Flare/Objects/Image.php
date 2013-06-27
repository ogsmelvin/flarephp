<?php

namespace Flare\Objects;

if(!extension_loaded('gd') || !function_exists('gd_info')){
    show_error("GD Library is not supported");
}

/**
 * 
 * @author anthony
 * 
 */
class Image
{
    /**
     * 
     * @var resource
     */
    private $_image;

    /**
     * 
     * @var array
     */
    private static $_types = array(
        IMAGETYPE_GIF,
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG,
        IMAGETYPE_SWF,
        IMAGETYPE_PSD,
        IMAGETYPE_BMP,
        IMAGETYPE_TIFF_II,
        IMAGETYPE_TIFF_MM,
        IMAGETYPE_JPC,
        IMAGETYPE_JP2,
        IMAGETYPE_JPX,
        IMAGETYPE_JB2,
        IMAGETYPE_SWC,
        IMAGETYPE_IFF,
        IMAGETYPE_WBMP,
        IMAGETYPE_XBM,
        IMAGETYPE_ICO
    );

    /**
     * 
     * @var string
     */
    private $_imageType;

    /**
     * 
     * @var array
     */
    private $_fileinfo;

    /**
     * 
     * @param string $path
     */
    public function __construct($path = null)
    {
        $realpath = realpath($path);
        $realpath = $realpath !== false ? rtrim(str_replace("\\", "/", $realpath), "/") : rtrim($path, "/");
        $this->_fileinfo = pathinfo($path);
        $image = getimagesize($path);
        if(isset($image[2]) && in_array($image[2], self::$_types)){
            $this->_imageType = $image[2];
            if($this->_imageType == IMAGETYPE_JPEG){
                $this->_image = imagecreatefromjpeg($filename);
            } else if($this->_imageType == IMAGETYPE_GIF){
                $this->_image = imagecreatefromgif($filename);
            } else if($this->_imageType == IMAGETYPE_PNG){
                $this->_image = imagecreatefrompng($filename);
            } else {
                show_error("Can't load image, not supported image type");
            }
        } else {
            show_error("Not a valid image type");
        }
    }

    /**
     * 
     * @return array
     */
    public static function getSupportedTypes()
    {
        return self::$_types;
    }

    /**
     * 
     * @return string
     */
    public function getType()
    {
        return $this->_imageType;
    }

    /**
     * 
     * @return string
     */
    public function getFilename()
    {
        return $this->_fileinfo['filename'];
    }

    /**
     * 
     * @return int
     */
    public function getWidth()
    {
        return imagesx($this->_image);
    }

    /**
     * 
     * @return int
     */
    public function getHeight()
    {
        return imagesy($this->_image);
    }

    /**
     * 
     * @param int $height
     * @return \Flare\Objects\Image
     */
    public function resizeHeight($height)
    {
        $width = $this->getWidth() * ($height / $this->getHeight());
        return $this->_resize($width, $height);
    }

    /**
     * 
     * @param int $width
     * @return \Flare\Objects\Image
     */
    public function resizeWidth($width)
    {
        $height = $this->getHeight() * ($width / $this->getWidth());
        return $this->_resize($width, $height);
    }

    /**
     * 
     * @param int $width
     * @param int $height
     * @return \Flare\Objects\Image
     */
    private function _resize($width = null, $height = null)
    {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->_image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->_image = $new_image;
        return $this;
    }

    /**
     * 
     * @param int $width
     * @param int $height
     * @return \Flare\Objects\Image
     */
    public function resize($width, $height)
    {
        return $this->_resize($width, $height);
    }

    /**
     * 
     * @param string $location
     * @return \Flare\Objects\Image
     */
    public function copy($location)
    {
        $this->saveAs($location);
        return new Image($location);
    }

    /**
     * 
     * @param int $compression
     * @param int $permmissions
     * @return boolean
     */
    public function save($compression = 75, $permissions = null)
    {
        return $this->_save()
    }

    /**
     * 
     * @param string $filename
     * @param int $compression
     * @return boolean
     */
    public function saveAs($filename, $compression = 75)
    {
        return $this->_save($);
    }

    /**
     * 
     * @param string $filename
     * @param string $image_type
     * @param int $compression
     * @param int $permissions
     * @return boolean
     */
    private function _save($filename = null, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null)
    {
        if($image_type){
            $image_type = $this->_imageType;
        }
        if($image_type == IMAGETYPE_JPEG){
            imagejpeg($this->_image, $filename, $compression);
        } else if($image_type == IMAGETYPE_GIF){
            imagegif($this->_image, $filename);         
        } else if($image_type == IMAGETYPE_PNG){
            imagepng($this->_image, $filename);
        }

        if($permissions != null){
            chmod($filename, $permissions);
        }
    }
}