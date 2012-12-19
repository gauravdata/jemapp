<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Context.class.php 18001 2008-05-13 16:05:33Z aharsani $
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
class Gpf_Plugins_Definition_ExtensionPoint extends Gpf_Object {
    private $extensionPoint;     
    private $className;
    private $methodName;
    private $priority;
    
    public function __construct($extensionPoint, $className, $methodName = '', $priority = 10) {
        $this->extensionPoint = $extensionPoint;
        $this->className = $className;
        $this->methodName = $methodName;
        $this->priority = $priority;
    }
    
    /**
     * Get extension point name
     *
     * @return string
     */
    public function getExtensionPoint() {
        return $this->extensionPoint;
    }
    
    /**
     * Get plugin class name
     *
     * @return string
     */
    public function getClassName() {
        return $this->className;
    }
    
    /**
     * Get plugin method name
     *
     * @return string
     */
    public function getMethodName() {
        return $this->methodName;
    }
    
    /**
     * Get priority in which will be executed plugin method in this extension point.
     *
     * @return int
     */
    public function getPriority() {
        return $this->priority;
    }
}
?>
