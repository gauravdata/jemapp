<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak, Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id:
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
class Gpf_Rpc_Annotation extends Gpf_Object {
    const SERVICE = 'service';
    const ANONYM = 'anonym';
    
    private $permissionObject = "";
    private $permissionPrivilege = "";
    private $annotations;

    function __construct(ReflectionMethod $method) {
        $this->parseComment($this->getDocComment($method));
        $this->parsePermissionAnnotation($this->getAnnotation(self::SERVICE));
    }
    
    public function hasAnnotation($name) {
        return isset($this->annotations[$name]);
    }

    private function getAnnotation($name) {
        if ($this->hasAnnotation($name)) {
            return $this->annotations[$name];
        }
        return '';
    }
    
    public function hasServiceAnnotation() {
        return $this->hasAnnotation(self::SERVICE);
    }
    
    public function hasAnonymAnnotation() {
        return $this->hasAnnotation(self::ANONYM);
    }
    
    public function hasServicePermissionsAnnotation() {
        return $this->permissionObject != "" || $this->permissionPrivilege != "";
    }
    
    public function getServicePermissionObject() {
        return $this->permissionObject;
    }
    
    public function getServicePermissionPrivilege() {
        return $this->permissionPrivilege;
    }
    
    private function parsePermissionAnnotation($annotation) {
        $parsedArray = explode(" ", $annotation);
        if (is_array($parsedArray) && count($parsedArray) >= 2) {
            $this->permissionObject = $parsedArray[0];
            $this->permissionPrivilege = $parsedArray[1];
        }
    }
    
    /**
     *
     * @param ReflectionMethod $method
     * @return string
     */
    private function getDocComment(ReflectionMethod $method) {
        $comment = '';
        //$comment = $method->getDocComment();
        if(strlen($comment) > 0) {
            return $comment;
        }
        $fileName = $method->getDeclaringClass()->getFileName();
        
        $commentParser = new Gpf_Rpc_Annotation_CommentParser(new Gpf_Io_File($fileName));
        return $commentParser->getMethodComment($method->getName());
    }
    
    private function parseComment($comment) {
        $lines = explode("\n", $comment);
        if (count($lines)) {
            foreach ($lines as $line) {
                $this->parseLine($line);
            }
        }
    }

    private function parseLine($line) {
        if (($posAt = strpos($line, "@")) === false) {
            return;
        }
        if (strlen($line = substr($line, $posAt+1)) < 1) {
            return;
        }
        if (($posSpace = strpos($line, " ")) === false) {
            $posSpace = strlen($line);
        }
        $name = trim(substr($line, 0, $posSpace));
        $value = trim(substr($line, $posSpace+1));
        $this->annotations[$name] = $value;
    }
}
?>
