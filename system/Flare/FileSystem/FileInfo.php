<?php

namespace Flare\FileSystem;

use \SplFileInfo;

/**
 * 
 * @author anthony
 * 
 */
abstract class FileInfo extends SplFileInfo
{
    /**
     * 
     * @return string
     */
    public function getPerms()
    {
        return substr(sprintf('%o', parent::getPerms()), -4);
    }

    /**
     * 
     * @param boolean $convert
     * @return string|int
     */
    public function getSize($convert = false)
    {
        $size = parent::getSize();
        if ($convert) {
            $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
            $power = $size > 0 ? floor(log($size, 1024)) : 0;
            return number_format($size / pow(1024, $power), 2, '.', ',').' '.$units[$power];
        }
        return $size;
    }

    /**
     * 
     * @param string $dateFormat
     * @return string
     */
    public function getMTime($dateFormat = 'Y-m-d H:i:s')
    {
        return date($dateFormat, parent::getMTime());
    }

    /**
     * 
     * @param string $dateFormat
     * @return string
     */
    public function getCTime($dateFormat = 'Y-m-d H:i:s')
    {
        return date($dateFormat, parent::getCTime());
    }

    /**
     * 
     * @param string $dateFormat
     * @return string
     */
    public function getATime($dateFormat = 'Y-m-d H:i:s')
    {
        return date($dateFormat, parent::getATime());
    }
}