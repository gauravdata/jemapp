<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * This plugin context class enables to add new constraints to table classes
 * which define the extension point. Currently it does not enable to remove
 * constraints hard coded in table classes as it could cause problems with
 * core functionality of application.
 *
 * @package GwtPhpFramework
 */
class Gpf_Contexts_Module extends Gpf_Plugins_Context {

    /**
     * @var array of resource urls
     */
    private $jsResources = array();

    /**
     * @var array of js scripts
     */
    private $jsScripts = array();

    /**
     * @var Gpf_Contexts_Module
     */
    private static $instance = null;

    protected function __construct() {
    }

    /**
     * @return Gpf_Contexts_Module
     */
    public function getContextInstance() {
        if (self::$instance == null) {
            self::$instance = new Gpf_Contexts_Module();
        }
        return self::$instance;
    }

    /**
     * Add javascript resource url
     *
     * @param string $resource
     */
    public function addJsResource($resource, $id = null) {
        $this->jsResources[$resource] = array('resource' => $resource, 'id' => $id);
    }

    /**
     * Get javascript resources array
     *
     * @return array
     */
    public function getJsResources() {
        return $this->jsResources;
    }

    /**
     * Get javascripts array
     *
     * @return array
     */
    public function getJsScripts() {
        return $this->jsScripts;
    }

    /**
     * Add javascript code
     *
     * @param string $sourceCode javascript source code, which should be added to main page header
     */
    public function addJsScript($sourceCode) {
        $this->jsScripts[] = $sourceCode;
    }

}
?>
