<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Gadget.class.php 23910 2009-03-25 13:15:46Z mbebjak $
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
class Gpf_Gadget extends Gpf_Db_Gadget implements Gpf_Rpc_Serializable {
    const POSITION_DESKTOP = 'D';
    const POSITION_SIDEBAR = 'S';
    const POSITION_HIDDEN = 'H';
    
    protected $panelWidth;
    protected $panelHeight;
       
    private $preferenceFormFields = array();
    
    public function __construct() {
        parent::__construct();
    }
    
    public function loadConfiguration($configurationContent) {
        throw new Gpf_Exception("wrong format");
    }
    
    protected function getTemplateName() {
        throw new Gpf_Exception("template name not defined");
    }
        
    public function toObject() {
        return $this->toText();
    }

    public function toText() {
        if ($this->panelWidth == 0) {
            $this->panelWidth = $this->getWidth();
        }
        if ($this->panelHeight == 0) {
            $this->panelHeight = $this->getHeight();
        }
        $template = new Gpf_Templates_Template($this->getTemplateName());
        $template->setDelimiter('/*{', '}*/');
        $template->assign('id', $this->getId());
        $template->assign('name', $this->getName());
        $template->assign('url', $this->getUrl());
        $template->assign('width', $this->getWidth());
        $template->assign('height', $this->getHeight());
        $template->assign('panelWidth', $this->panelWidth-40);
        $template->assign('panelHeight', $this->panelHeight-75);
        $template->assign('properties', $this->getProperties());
        $template->assign('autoRefreshTime', $this->getAutorefreshTime());
        
        return $template->getHTML();
    }
    
    public function setPanelSize($panelWidth, $panelHeight) {
        $this->panelWidth = $panelWidth;
        $this->panelHeight = $panelHeight;
    }
    
    public function getProperties() {
        $properties = new Gpf_Db_GadgetProperty();
        $properties->setGadgetId($this->getId());
        return $properties->loadCollection();
    }
    
    public function getPreferencesFormId() {
        return md5($this->getUrl());
    }
    
    protected function addPreferenceField(Gpf_Db_FormField $formField) {
        $formField->setAccountId(Gpf_Session::getAuthUser()->getAccountId());
        $this->preferenceFormFields[] = $formField;
    }
    
    public function savePreferenceFormFields() {
        foreach ($this->preferenceFormFields as $formField) {
            try {
                $formField->loadFromData(array('formid', 'code'));
            } catch (Gpf_DbEngine_NoRowException $e) {
            }
            $formField->save();
        }
    }
    
    public function addProperty($name, $value) {
        $property = new Gpf_Db_GadgetProperty();
        $property->setGadgetId($this->getId());
        $property->setName($name);
        try {
            $property->loadFromData();
        } catch (Gpf_DbEngine_NoRowException $e) {
        }
        $property->setValue($value);
        $property->save();
    }   
    
    public function setClosed() {
        $this->setPositionType(self::POSITION_HIDDEN);
    }
}
?>
