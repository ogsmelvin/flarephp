<?php

namespace FPHP\Objects;

if(!extension_loaded('gd') || !function_exists('gd_info')){
    display_error("GD Library is not supported");
}

use \Exception;

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
                display_error("Can't load image, not supported image type");
            }
        } else {
            display_error("Not a valid image type");
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
     * @return \FPHP\Objects\Image
     */
    public function resizeHeight($height)
    {
        return $this->_resize(null, $height);
    }

    /**
     * 
     * @param int $width
     * @return \FPHP\Objects\Image
     */
    public function resizeWidth($width)
    {
        return $this->_resize($width);
    }

    /**
     * 
     * @param int $width
     * @param int $height
     * @return \FPHP\Objects\Image
     */
    private function _resize($width = null, $height = null)
    {
        return $this;
    }

    /**
     * 
     * @param int $width
     * @param int $height
     * @return \FPHP\Objects\Image
     */
    public function resize($width, $height)
    {
        return $this->_resize($width, $height);
    }

    /**
     * 
     * @param string $location
     * @return \FPHP\Objects\Image
     */
    public function copy($location)
    {
        return new Image();
    }

    /**
     * 
     * @return boolean
     */
    public function save()
    {

    }
}