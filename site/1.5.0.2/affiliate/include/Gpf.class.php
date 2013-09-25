<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: Gpf.class.php 33874 2011-07-21 09:20:23Z iivanco $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

require_once 'Gpf/Paths.class.php';

function _papAutoload($class = null){
    if ($class == '') {
        return;
    }
    Gpf::includeClass($class);
}
if((!defined('SPL_AUTOLOAD_DISABLED')) && (@function_exists('spl_autoload_register'))) {
    spl_autoload_register('_papAutoload');
} else {
    function __autoload($class = null){
        _papAutoload($class);
    }
}

class Gpf {
    const YES = 'Y';
    const NO = 'N';

    const GPF_VERSION = '1.1.11.0';
    const CODE = 'gpf';

    public static function newObj($class) {
        if (func_num_args() > 1) {
            $arg_list = func_get_args();
            $str_arg_list = '$arg_list[1]';
            for ($i = 2; $i < count($arg_list); $i++) {
                $str_arg_list .= ', $arg_list[' . $i . ']';
            }
            eval("\$obj = new ".$class."(".$str_arg_list.");");
            return $obj;
        } else {
            return new $class;
        }
    }

    public static function includeClass($class_name) {
        $fileName = self::existsClass($class_name);

        if($fileName === false) {
            $message = '';
            if(function_exists('debug_backtrace')) {
                foreach (debug_backtrace() as $stackElement) {
                    if(isset($stackElement['line']) && isset($stackElement['file'])) {
                        $message .= sprintf("At line %s, file %s\n", $stackElement['line'], $stackElement['file']);
                    }
                }
            }
            $message .= sprintf('Fatal Error: Class %s is missing', $class_name);
            throw new Gpf_ClassNotDefined($class_name, $message);
        }
        require_once($fileName);
        return true;
    }

    public static function existsClass($className) {
        foreach (Gpf_Paths::getInstance()->getIncludePaths() as $includePath) {
            $fileName = self::existsClassName($className, $includePath);
            if($fileName) {
                return $fileName;
            }
        }
        return false;
    }

    private static function existsClassName($className, $pathPrefix = '') {
        $fileName = self::getFileName($className, $pathPrefix);

        if(!is_file($fileName)) {
            $fileName = self::getFileNameCaseInsensitive($className, $pathPrefix);
        }
        return $fileName;
    }

    private static function getFileName($className, $pathPrefix = '') {
        $fileName = $pathPrefix . self::getRelativeClassPath($className) . '.class.php';
        return $fileName;
    }

    private static function getFileNameCaseInsensitive($className, $pathPrefix = '') {
        $filename = $pathPrefix . self::sqlRegcase(self::getRelativeClassPath($className)). '.class.php';
        if (strlen($filename) > 260) {
            return false;
        }
        $fileNames = @glob($filename);
        if(!$fileNames) {
            return false;
        }
        return $fileNames[0];
    }
    
    private static function sqlRegcase($string) {
        $letters = preg_split("//", strtolower($string));
        $result = ''; 
        foreach($letters as $letter) {
            if(preg_match("/[a-z]/", $letter)) {
                $result .= '[' . strtoupper($letter) . $letter . ']';
            } else {
                $result .= $letter;
            }
        }    
        return $result;
    }

    private static function getRelativeClassPath($className) {
        return str_replace("_", "/", $className);
    }

    /**
     * Encapsulate message as translated message with ## ##
     *
     * @param string $message
     * @return string
     */
    public static function _runtime($message) {
        return '##' . $message . '##';
    }
}

function gpf_errorHandler($errno, $errstr, $errfile, $errline) {
    switch ($errno) {
        case E_RECOVERABLE_ERROR:
            throw new Gpf_Exception('PHP Parse exception:' . $errstr . ' ' . $errfile . ' ' . $errline);
            return true;
        default:
            break;
    }
    return false;
}

if(version_compare("5.2.0", PHP_VERSION) <= 0) {
    set_error_handler('gpf_errorHandler', E_RECOVERABLE_ERROR);
}
?>
