<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ServiceMethod.class.php 23603 2009-02-24 14:45:07Z mjancovic $
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
class Gpf_Rpc_ServiceMethod extends Gpf_Object {
    protected $methodName;
    protected $className;
    /**
     * @var Gpf_Rpc_Annotation
     */
    protected $annotations;
    protected $serviceObj;

    function __construct(Gpf_Rpc_Params $params) {
        $this->methodName = $params->getMethod();
        $this->className = $params->getClass();

        $reflectionClass = new ReflectionClass($this->className);
        $reflectionMethod = $reflectionClass->getMethod($this->methodName);
        
        if (!$reflectionMethod->isPublic()) {
            throw new Gpf_Exception($this->className.'->'.$this->methodName.'() is not a service method (not public)');
        }
        $this->annotations = new Gpf_Rpc_Annotation($reflectionMethod);
        if (!$this->annotations->hasServiceAnnotation()) {
            throw new Gpf_Exception($this->className.'->'.$this->methodName.'() is not a service method (annotation)');
        }

        $this->initSession($params->getSessionId());
        $this->createInstance();
    }
    
    protected function createConstructorInstance() {
        $reflectionClass = new ReflectionClass($this->className);
        $constructor = $reflectionClass->getConstructor();
        if(is_object($constructor) && !$constructor->isPublic()) {
            throw new Gpf_Exception('Constructor of class '.$this->className.' is not public');
        }
        $this->serviceObj = Gpf::newObj($this->className);
    }
    
    protected function createInstance() {
        try {
            $this->createConstructorInstance();
            return;    
        } catch (Exception $e) {
            $reflectionMethod = new ReflectionMethod($this->className, "getInstance");
            $this->serviceObj = $reflectionMethod->invoke(null);
        }
    }
    
    public function invoke(Gpf_Rpc_Params $params) {
        Gpf_Log::debug($this->_sys("Invoking method %s->%s()", $this->className, $this->methodName));
        $this->checkPermissions($params);
        $this->checkParams();
        return call_user_func(array(&$this->serviceObj, $this->methodName), $params);
    }
    
    protected function initSession($sessionId) {
        Gpf_Session::load($sessionId);
    }

    protected function checkPermissions(Gpf_Rpc_Params $params) {
        if ($this->annotations->hasAnonymAnnotation()) {
            return;
        }
        if (Gpf_Session::getAuthUser()->isLogged()) {
            if (!$this->annotations->hasServicePermissionsAnnotation()) {
                throw new Gpf_Exception("Method ".$this->className."->".$this->methodName."() does not have permission annotation");
            }
            if (Gpf_Session::getAuthUser()->hasPrivilege(
                $this->annotations->getServicePermissionObject(),
                $this->annotations->getServicePermissionPrivilege())) {
                    return;
            }
        }
        throw new Gpf_Rpc_PermissionDeniedException($this->className, $this->methodName);
    }

    private function checkParams() {
    }
}
?>
