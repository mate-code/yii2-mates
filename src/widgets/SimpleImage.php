<?php

namespace mate\yii\widgets;
/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details:
* http://www.gnu.org/licenses/gpl.html
*
*/

/**
 * Class SimpleImage
 * @package common\helpers
 */
class SimpleImage
{
    /**
     * @var resource
     */
    public $image;
    /**
     * @var int
     */
    public $image_type;
    /**
     * @var string
     */
    public $image_path;

    /**
     * @param string $filename
     */
    public function __construct($filename)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '1024M');
        if (!is_file($filename)) {
            throw new \RuntimeException("Given path is not a file: $filename");
        }

        $this->image_path = $filename;
        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];

        switch ($this->image_type) {
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($filename);
                break;
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($filename);
                break;
            case IMAGETYPE_BMP:
                $this->image = imagecreatefromwbmp($filename);
                break;
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($filename);
                break;
            default:
                throw new \RuntimeException("Unrecognized image type for file " . basename($filename));
        }
    }

    /**
     * @param string $filename
     * @param null $image_type
     * @param int $compression
     * @param null $permissions
     */
    public function save($filename, $image_type = null, $compression = 75, $permissions = null)
    {
        if ($image_type === null) {
            switch ($this->image_type) {
                case IMAGETYPE_GIF:
                    $image_type = IMAGETYPE_PNG;
                    break;
                case IMAGETYPE_BMP:
                    $image_type = IMAGETYPE_JPEG;
                    break;
                default:
                    $image_type = $this->image_type;
            }
        }

        switch ($image_type) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->image, $filename, $compression);
                break;
            case IMAGETYPE_GIF:
                imagegif($this->image, $filename);
                break;
            case IMAGETYPE_BMP:
                image2wbmp($this->image, $filename);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->image, $filename);
                break;
        }

        if ($permissions != null) {
            chmod($filename, $permissions);
        }
        $this->image_path = $filename;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return imagesx($this->image);
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return imagesy($this->image);
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return substr(strrchr($this->image_path, "."), 1);
    }

    /**
     * @param int $height
     */
    public function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * @param int $width
     */
    public function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * @param float $scale
     */
    public function scale($scale)
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize($width, $height);
    }

    /**
     * @param int $width
     * @param int $height
     */
    public function resize($width, $height)
    {
        $new_image = imagecreatetruecolor($width, $height);

        if ($this->image_type === IMAGETYPE_PNG || $this->image_type === IMAGETYPE_GIF) {
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            $transparent = imagecolorallocatealpha(
                $new_image,
                255,
                255,
                255,
                127
            );
            imagefilledrectangle(
                $new_image,
                0,
                0,
                $width,
                $height,
                $transparent
            );
        }

        imagecopyresampled(
            $new_image,
            $this->image,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $this->getWidth(),
            $this->getHeight()
        );
        $this->image = $new_image;
    }

    /**
     * cropData must contain:
     * x: Horizontal position to start cropping
     * y: Vertical position to start cropping
     * height: Height of the cropping area
     * width: Width of the cropping area
     * @param array $cropData
     */
    public function crop(array $cropData)
    {
        $this->image = imagecrop($this->image, $cropData);
    }

    public function opacity($opacity)
    {
        imagealphablending($this->image, false);
        imagesavealpha($this->image, true);
        $transparency = 1 - $opacity;
        imagefilter($this->image, IMG_FILTER_COLORIZE, 0, 0, 0, 127 * $transparency);

    }


}