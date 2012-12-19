<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ImageGenerator.class.php 23635 2009-02-27 07:50:38Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Common_Captcha_ImageGenerator extends Gpf_Object implements Gpf_Rpc_Serializable {
    private $image;
    /**
     * Image width
     *
     * @var number
     */
    private $width;
    /**
     * Image height
     *
     * @var number
     */
    private $height;
    /**
     * Noise character min. size
     *
     */
    private $characterMinSize = 25;
    /**
     * Noise character max. size
     *
     */
    private $characterMaxSize = 30;
    /**
     * Max. degree of text character rotation
     *
     */
    private $maxRotation = 25;
    private $jpegQuality = 80;
    /**
     * Text of captcha
     *
     */
    private $text;
    /**
     * Density of noise characters
     *
     */
    private $noiseFactor = 9;
    private $generateNoise = true;
    private $generateGrid = true;
    /**
     * Selected font
     *
     * @var unknown_type
     */
    private $font;
    private static $fonts = null;

    public function __construct($text) {
        $this->text = $text;
        self::loadFonts();
        $this->getRandomFontFile();
        $this->setSize($this->getTextSize() * 30, 50);
    }

    public function setSize($width, $height) {
        $this->width = $width;
        $this->height = $height;
    }

    public function generate() {
        $this->image = $this->createImage();
        $backgroundColor = $this->createColor($this->image,
        Gpf_Common_Captcha_RgbColor::createRandomColor(224, 255));
        imagefilledrectangle($this->image, 0, 0, $this->width, $this->height, $backgroundColor);

        $this->fillWithNoiseOrGrid();
        $this->generateText();


        Gpf_Http::setHeader(Gpf_Net_Server_Http_Response::CONTENT_TYPE, 'image/jpeg');
        ob_start();
        imagejpeg($this->image, null, $this->jpegQuality);
        $imageText = ob_get_contents();
        ob_end_clean();
        imagedestroy($this->image);
        return $imageText;
    }

    public function toObject() {
        throw new Gpf_Exception("Unsupported");
    }

    public function toText() {
        return $this->generate();
    }

    private static function loadFonts() {
        if(self::$fonts !== null) {
            return;
        }
        $directory = dirname(__FILE__) . '/font/';
        self::$fonts = array();
        foreach (new Gpf_Io_DirectoryIterator($directory, '.ttf') as $fullName => $file) {
            self::$fonts[$fullName] = $fullName;
        }
    }

    private function createImage() {
        if(Gpf_Php::isFunctionEnabled('imagecreatetruecolor')) {
            return imagecreatetruecolor($this->width, $this->height);
        }
        if (Gpf_Php::isFunctionEnabled('imagecreate')) {
            return imagecreate($this->width, $this->height);
        }
        throw new Gpf_Exception("No GD installed");
    }

    private function seedRandomGenerator() {
        srand((double) microtime() * 1000000);
    }

    private function getFontFile() {
        return $this->font;
    }

    private function getRandomFontFile() {
        $this->seedRandomGenerator();
        $this->font = array_rand(self::$fonts);
        return $this->font;
    }

    private function getBackgroundNoiseCharacterCount() {
        return $this->noiseFactor * $this->getTextSize();
    }

    private function getTextSize() {
        return strlen($this->text);
    }

    private function generateNoise() {
        for($i=0; $i < $this->getBackgroundNoiseCharacterCount(); $i++) {
            $this->seedRandomGenerator();
            $size = intval(rand((int)($this->characterMinSize / 2.3),
            (int)($this->characterMaxSize / 1.7)));

            $this->seedRandomGenerator();
            $angle = intval(rand(0, 360));

            $this->seedRandomGenerator();
            $left = intval(rand(0, $this->width));

            $this->seedRandomGenerator();
            $top = intval(rand(0, (int)($this->height - ($size / 5))));

            $color = $this->createColor($this->image,
            Gpf_Common_Captcha_RgbColor::createRandomColor(160, 224));

            $this->seedRandomGenerator();
            $text = chr(intval(rand(45, 250)));

            imagettftext($this->image, $size, $angle, $left, $top, $color,
            $this->getRandomFontFile(), $text);
        }
    }

    private function generateGrid() {
        for($i=0; $i < $this->width; $i += (int)($this->characterMinSize / 1.5)) {
            $color = $this->createColor($this->image,
            Gpf_Common_Captcha_RgbColor::createRandomColor(160, 224));
            imageline($this->image, $i, 0, $i, $this->height, $color);
        }

        for($i=0 ; $i < $this->height; $i += (int)($this->characterMinSize / 1.8)) {
            $color = $this->createColor($this->image,
            Gpf_Common_Captcha_RgbColor::createRandomColor(160, 224));
            imageline($this->image, 0, $i, $this->width, $i, $color);
        }
    }

    private function fillWithNoiseOrGrid() {
        if($this->generateNoise) {
            $this->generateNoise();
        }

        if($this->generateGrid) {
            $this->generateGrid();
        }
    }

    private function createColor($image, Gpf_Common_Captcha_RgbColor $color) {
        return  imagecolorallocate($image, $color->r, $color->g, $color->b);
    }

    private function generateText() {
        $left = 0;
        for($i=0, $left = intval(rand($this->characterMinSize, $this->characterMaxSize));
        $i < $this->getTextSize(); $i++) {
            $text    = strtoupper(substr($this->text, $i, 1));

            $this->seedRandomGenerator();
            $angle = intval(rand(($this->maxRotation * -1), $this->maxRotation));

            $this->seedRandomGenerator();
            $size = intval(rand($this->characterMinSize, $this->characterMaxSize));

            $this->seedRandomGenerator();
            $top = intval(rand((int)($size * 1.5), (int)($this->height - ($size / 7))));

            $color = $this->createColor($this->image,
            Gpf_Common_Captcha_RgbColor::createRandomColor(0, 127));

            $shadowRgb = Gpf_Common_Captcha_RgbColor::createRandomColor(0, 127);
            $shadowRgb->add(127, 127, 127);
            $shadow = $this->createColor($this->image, $shadowRgb);


            ImageTTFText($this->image, $size, $angle, $left + (int)($size / 15), $top,
            $shadow, $this->getRandomFontFile(), $text);

            ImageTTFText($this->image, $size, $angle, $left, $top - (int)($size / 15),
            $color, $this->getFontFile(), $text);
            $left += (int)($size + ($this->characterMinSize / 5));
        }
    }
}
