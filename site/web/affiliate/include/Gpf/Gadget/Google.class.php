<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Google.class.php 20833 2008-09-11 08:58:56Z aharsani $
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
class Gpf_Gadget_Google extends Gpf_Gadget  {

    public function __construct() {
        parent::__construct();
        $this->setType('G');
    }
    
    protected function getTemplateName() {
        return "gadget_google.stpl";
    }
    
    public function loadConfiguration($configurationContent) {
        if (strstr($configurationContent, '<Module>') == false) {
            throw new Gpf_Exception("Not a Google gadget");
        }
        $xml = @new SimpleXMLElement($configurationContent);
        if ($xml->getName() != "Module") {
            throw new Gpf_Exception("wrong format");
        }
        
        foreach ($xml->UserPref as $userPreference) {
            $this->processPreference($userPreference);
        }
        $modulePrefs = $xml->ModulePrefs;
        $this->setWidth(320);
        $this->setHeight(200);
    }
    
    private function processPreference($preference) {
        $type = (string) $preference['datatype'];
        if ($type == '' || $type == null) {
            $type = 'string';
        }
        if ($type == 'hidden') {
            return;
        }
        $formField = new Gpf_Db_FormField();
        $formField->setFormId($this->getPreferencesFormId());
        $formField->setCode((string) $preference['name']);
        $displayName = (string) $preference['display_name'];
        if ($displayName == '') {
            $displayName = (string) $preference['name'];
        }
        $formField->setName($displayName);
        if ($preference['required'] == "true") {
            $formField->setStatus(Gpf_Db_FormField::STATUS_MANDATORY);
        } else {
            $formField->setStatus(Gpf_Db_FormField::STATUS_OPTIONAL);
        }
        switch ($type) {
            case 'string':
                $formField->setType(Gpf_Db_FormField::TYPE_TEXT);
                break;
            case 'bool':
                $formField->setType(Gpf_Db_FormField::TYPE_CHECKBOX);
                break;
            case 'enum':
                $formField->setType(Gpf_Db_FormField::TYPE_LISTBOX);
                $formField->clearAvailableValues();
                foreach ($preference->EnumValue as $option) {
                    $value = (string) $option['value'];
                    $displayValue = (string) $option['display_value'];
                    if ($displayValue == '') {
                        $displayValue = $value;
                    }
                    $formField->addAvailableValue($value, $displayValue);
                }
                break;
        }
        $this->addPreferenceField($formField);
    }
}
?>
