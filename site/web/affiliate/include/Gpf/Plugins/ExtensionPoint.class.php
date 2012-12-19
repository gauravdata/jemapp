<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: ExtensionPoint.class.php 26215 2009-11-24 11:17:22Z mjancovic $
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
class Gpf_Plugins_ExtensionPoint extends Gpf_Object {
    /**
     * @var instances of all extension points
     */
    static private $instances = array();

    /**
     * extension point name
     */
    private $extensionPointName;

    /**
     * name of context class.
     * It is first created on the first use. It must be singleton
     * with getInstance() method
     */
    private $contextClassName = "";

    /**
     * class of the context
     * must be singleton with getInstance() method
     */
    private $contextClassObj = null;

    /**
     * name of context class.
     * It is first created on the first use. It must be singleton
     * with getInstance() method
     */
    private $handlers = array();

    /**
     * array of all process plugins for this extension point
     */
    private $plugins = array();

    function __construct($extensionPointName, $definition) {
        $this->extensionPointName = $extensionPointName;

        if(!isset($definition['context'])) {
	        throw Gpf_Plugins_Exception("Extension point '$extensionPointName' does not have context class defined");
        }
        $this->contextClassName = $definition['context'];

        if(!isset($definition['handlers']) || !is_array($definition['handlers'])) {
        	throw Gpf_Plugins_Exception("Extension point '$extensionPointName' does not have handlers defined");
        }
        $this->handlers = $definition['handlers'];
    }

    /**
     * returns instance of extention point class of given name
     *
     * @return Gpf_Plugins_ExtensionPoint
     */
    public static function getInstance($extensionPointName, $definition) {
    	if(!isset(self::$instances[$extensionPointName])) {
    		self::$instances[$extensionPointName] = new Gpf_Plugins_ExtensionPoint($extensionPointName, $definition);
    	}
        return self::$instances[$extensionPointName];
    }
    
    public static function clear() {
    	self::$instances = array();
    }

    /**
     * processes handlers reistered for this extension point
     *
     * @param object $context
     */
    public function processHandlers($context = null) {
    	if(!is_array($this->handlers)) {
    		throw Gpf_Plugins_Exception("Handlers for extension point '".$this->extensionPointName."' are null");
    	}

    	//check if definition of extension point contains same context class name as is used in context
    	if (!($context instanceof $this->contextClassName)) {
    	    throw new Gpf_Plugins_Exception("Context class name ($this->contextClassName) is not same as context object (" . get_class($context) . ")");
    	}

    	foreach($this->handlers as $handler) {
    	    if(!$this->callHandler($handler, $context)) {
                break;
            }
    	}
    }

    private function callHandler($handler, $context) {
		$handlerObject = $this->createHandlerObject($handler);
		$handlerMethod = $this->getHandlerMethod($handler);

        try {
            if($context == null) {
                $returnValue = $handlerObject->$handlerMethod();
            } else {
            	$returnValue = $handlerObject->$handlerMethod($context);
            }
        } catch(Exception $e) {
            throw new Gpf_Plugins_Exception("Unhalted exception: \"".$e->getMessage()."\" in class ".get_class($handlerObject).", STOPPING");
            exit;
        }

        if($returnValue === Gpf_Plugins_Engine::PROCESS_STOP_EXIT) {
            exit;
        }
        if($returnValue === Gpf_Plugins_Engine::PROCESS_STOP_EXTENSION_POINT) {
            return false;
        }
        if($returnValue != Gpf_Plugins_Engine::PROCESS_CONTINUE) {
        	// handler function does not need to return value,
        	// it is assumed that it means to continue
         	//   throw new Gpf_Exception("Handler ".get_class($handlerObject).".$handlerMethod() method has to return value PROCESS_CONTINUE / PROCESS_STOP_EXTENSION_POINT / PROCESS_STOP_ALL / PROCESS_STOP_EXIT!");
        }

        return true;
    }

    private function createHandlerObject($handler) {
    	if(!isset($handler['class'])) {
            throw new Gpf_Plugins_Exception("Handler class is nt defined!");
        }

        $className = $handler['class'];
    	// create context object
    	eval("\$obj = $className::getHandlerInstance();");
        return $obj;
    }

    private function getHandlerMethod($handler) {
    	if(!isset($handler['method'])) {
            throw new Gpf_Plugins_Exception("Handler method is nt defined!");
        }

        return $handler['method'];
    }
}

?>
