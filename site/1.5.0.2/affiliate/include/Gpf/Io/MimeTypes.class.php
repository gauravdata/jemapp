<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: File.class.php 23688 2009-03-08 22:42:54Z aharsani $
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
class Gpf_Io_MimeTypes {
    
    private static $mimeTypes =
        array('tgz'     => 'application/x-gtar',
              'tar.gz'  => 'application/x-gtar',
              'tar'     => 'application/x-tar',
              'zip'     => 'application/zip',
              'gif'     => 'image/gif',
              'jpeg'    => 'image/jpeg',
              'jpg'     => 'image/jpeg',
              'jpe'     => 'image/jpeg',
              'png'     => 'image/png',
              'tiff'    => 'image/tiff',
              'tif'     => 'image/tiff',
              'kdc'     => 'image/x-kdc',
              'mpeg'    => 'video/mpeg',
              'mpg'     => 'video/mpeg',
              'mpe'     => 'video/mpeg',
              'mng'     => 'video/x-mng',
              'css'     => 'text/css',
              'html'    => 'text/html',
              'htm'     => 'text/html');
    

    public static function getMimeType($extension) {
        if (array_key_exists($extension, self::$mimeTypes)) {
            return self::$mimeTypes[$extension];
        }
        return 'text/plain';
    }
}

?>
