<?php
class AW_Colorswatches_Helper_Image
{
    const BASE_MEDIA_PATH = 'aw_colorswatches';
    const CACHED_PREFIX = 'resized';

    /**
     * @return string
     */
    public static function getDirPath()
    {
        return Mage::getBaseDir('media') . DS . self::BASE_MEDIA_PATH;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function getFilePathByFilename($filename)
    {
        return self::getDirPath() . DS . $filename;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function getUrlByFilename($filename)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)
            . self::BASE_MEDIA_PATH . '/'
            . self::_convertPathToUrl($filename)
        ;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public static function getFilenameFromUrl($url)
    {
        $path = self::_convertUrlToPath(
            str_replace(
                Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . self::BASE_MEDIA_PATH,
                '',
                $url
            )
        );
        if (strpos($path, DS . self::CACHED_PREFIX . DS) !== FALSE) {
            $patternAnchor = "\\" . DS . self::CACHED_PREFIX . "\\" . DS;
            $path = preg_replace("/" . $patternAnchor . "[0-9]*x[0-9]*/", '', $path);
        }
        $path = self::_cleanDuplicateSlashes($path);
        return $path;
    }

    /**
     * @param string $filename
     */
    public static function deleteImage($filename)
    {
        $path = self::getFilePathByFilename($filename);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * @param string $filename
     * @param int|null $width
     * @param int|null $height
     *
     * @return null|string
     */
    public static function resizeImage($filename, $width = 100, $height = null)
    {
        $originalImagePath = self::getFilePathByFilename($filename);
        if (is_null($width) && is_null($height)) {
            list($width, $height) = getimagesize($originalImagePath);
        }
        if (!file_exists($originalImagePath) || !is_file($originalImagePath)) {
            return null;
        }
        $cachedImagePath = self::getCachedImagePath($filename, $width, $height);
        if (!file_exists($cachedImagePath) || !is_file($cachedImagePath)) {
            $imageObj = new Varien_Image($originalImagePath);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(false);
            $imageObj->backgroundColor(array(255, 255, 255));
            try {
                $imageObj->resize($width, $height);
                $imageObj->save($cachedImagePath);
            } catch (Exception $e) {
                return null;
            }
        }
        return self::getCachedImageUrl($filename, $width, $height);
    }

    /**
     * @param string $imageName
     * @param int $width
     * @param int|null $height
     *
     * @return string
     */
    public static function getCachedImagePath($imageName, $width, $height)
    {
        return self::getDirPath()
            . DS . self::CACHED_PREFIX
            . DS . $width . 'x' . (!is_null($height) ? $height : '')
            . DS . $imageName
        ;
    }

    /**
     * @param string $filename
     * @param int $width
     * @param int|null $height
     *
     * @return string
     */
    public static function getCachedImageUrl($filename, $width, $height)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)
            . self::BASE_MEDIA_PATH . '/'
            . self::CACHED_PREFIX . '/'
            . $width . 'x' . (!is_null($height) ? $height : '') . '/'
            . self::_convertPathToUrl($filename)
        ;
    }

    public static function cleanImageCache()
    {
        $cacheImageDir = self::getDirPath() . DS . self::CACHED_PREFIX . DS;
        $io = new Varien_Io_File();
        $io->rmdir($cacheImageDir, true);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    protected static function _convertUrlToPath($url)
    {
        return str_replace('/', DS, $url);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected static function _convertPathToUrl($path)
    {
        return str_replace(DS, '/', $path);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected static function _cleanDuplicateSlashes($string)
    {
        return preg_replace("/[\/]+/", '/', $string);
    }
}