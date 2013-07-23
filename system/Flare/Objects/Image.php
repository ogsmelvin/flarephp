<?php

namespace Flare\Objects;

if (!extension_loaded('gd') || !function_exists('gd_info')) {
    show_error("GD Library is not supported");
}

use Flare\Object;

/**
 * 
 * @author anthony
 * 
 */
class Image extends Object
{
    /**
     * 
     * @var string
     */
    private $_type;

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
        if (isset($image[2]) && in_array($image[2], get_image_types()) {
            $this->_type = $image[2];
            if ($this->_type == IMAGETYPE_JPEG) {
                $this->_data = imagecreatefromjpeg($filename);
            } elseif ($this->_type == IMAGETYPE_GIF) {
                $this->_data = imagecreatefromgif ($filename);
            } elseif ($this->_type == IMAGETYPE_PNG) {
                $this->_data = imagecreatefrompng($filename);
            } else {
                show_error("Can't load image, not supported image type");
            }
        } else {
            show_error("Not a valid image type");
        }
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
        return imagesx($this->_data);
    }

    /**
     * 
     * @return int
     */
    public function getHeight()
    {
        return imagesy($this->_data);
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
        imagecopyresampled($new_image, $this->_data, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->_data = $new_image;
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
        return new __CLASS__($location);
    }

    /**
     * 
     * @param int $compression
     * @param int $permmissions
     * @return boolean
     */
    public function save($compression = 75, $permissions = null)
    {
        return $this->_save();
    }

    /**
     * 
     * @param string $filename
     * @param int $compression
     * @return boolean
     */
    public function saveAs($filename, $compression = 75)
    {
        return $this->_save();
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
        if ($image_type) {
            $image_type = $this->_type;
        }
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->_data, $filename, $compression);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif ($this->_data, $filename);         
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->_data, $filename);
        }

        if ($permissions != null) {
            chmod($filename, $permissions);
        }
    }
}