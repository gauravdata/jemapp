<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Factory.class.php 20949 2008-09-17 17:23:32Z vzeman $
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
class Gpf_Gadget_Factory extends Gpf_Object {

    const CONTENT_PREFFIX = "content://";

    private $gadgetTypes;

    function __construct() {
        $gadgetTypes = array();
        $this->initGadgetTypes();
    }

    private function initGadgetTypes() {
        $this->addGadgetType("Gpf_Gadget_Uwa");
        $this->addGadgetType("Gpf_Gadget_Google");
        $this->addGadgetType("Gpf_Gadget_Content");
        $this->addGadgetType("Gpf_Gadget_Rss");
    }

    private function addGadgetType($className) {
        $gadget = Gpf::newObj($className);
        $this->gadgetTypes[$gadget->getType()] = $className;
    }

    /**
     * Returns gadget object
     *
     * @param string $gadgetId gadget ID
     * @return Gpf_Gadget
     */
    public function getGadget($gadgetId) {
        if ($gadgetId == '') {
            throw new Gpf_Exception("Gadget $gadgetId not found.");
        }
        $gadget = $this->getGadgetObject($gadgetId);
        $gadget->setPrimaryKeyValue($gadgetId);
        $gadget->load();
        return $gadget;
    }

    /**
     * @param string $gadgetId
     * @return string
     */
    private function getGadgetType($gadgetId) {
        $gadget = new Gpf_Gadget();
        $gadget->setPrimaryKeyValue($gadgetId);
         
        try {
            $gadget->load();
        } catch (Gpf_DbEngine_NoRowException $e) {
            throw new Gpf_Exception("Gadget $gadgetId not found.");
        }

        return $gadget->getType();
    }

    /**
     * @param string $gadgetId
     * @return Gpf_Gadget
     */
    private function getGadgetObject($gadgetId) {
        $gadgetType = $this->getGadgetType($gadgetId);

        $obj = $this->getGadgetObjectFromType($gadgetType);
        if($obj == null) {
            throw new Gpf_Exception("Gadget $gadgetId not found.");
        }
        return $obj;
    }

    /**
     * @param string $gadgetType
     * @return Gpf_Gadget
     */
    public function getGadgetObjectFromType($gadgetType) {
        foreach ($this->gadgetTypes as $type => $className) {
            if ($type == $gadgetType) {
                return Gpf::newObj($className);
            }
        }
        return null;
    }

    /**
     * @param string $url
     * @return Gpf_Gadget
     */
    public function downloadGadget($url) {
        if (strpos($url, self::CONTENT_PREFFIX) === 0) {
            Gpf_Log::debug($this->_sys("Adding content gadget: %s", $url));
            $gadget = new Gpf_Gadget_Content();
            $gadget->setUrl($url);
            $gadget->loadConfiguration("");
            return $gadget;
        } else {
            Gpf_Log::debug($this->_sys("Downloading gadget: %s", $url));

            $request = new Gpf_Net_Http_Request();
            $request->setUrl($url);

            $client = new Gpf_Net_Http_Client();
            $response = $client->execute($request);

            $gadgetContent = $response->getBody();
            foreach ($this->gadgetTypes as $className) {
                $gadget = Gpf::newObj($className);
                $gadget->setUrl($url);
                try {
                    $gadget->loadConfiguration($gadgetContent);
                    return $gadget;
                } catch (Gpf_Exception $e) {
                }
            }
            throw new Gpf_Exception($this->_("Unsupported gadget"));
        }
    }
}

?>
