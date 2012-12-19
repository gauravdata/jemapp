<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Uwa.class.php 19569 2008-08-01 14:23:38Z mbebjak $
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
class Gpf_Gadget_Uwa extends Gpf_Gadget  {
    
    const PREFERENCES_START_TAG = "<widget:preferences>";
    const PREFERENCES_END_TAG = "</widget:preferences>";
    
    public function __construct() {
        parent::__construct();
        $this->setType('U');
    }
    
    protected function getTemplateName() {
        return "gadget_uwa.stpl";
    }
    
    public function loadConfiguration($configurationContent) {
        if (strstr($configurationContent, 'xmlns:widget="http://www.netvibes.com/ns/"') == false) {
            throw new Gpf_Exception("Not an UWA widget");
        }
        $configStart = strpos($configurationContent, self::PREFERENCES_START_TAG);
        $configEnd = strpos($configurationContent, self::PREFERENCES_END_TAG);
        if ($configStart !== false && $configEnd !== false) {
            $config = substr($configurationContent,
                             $configStart,
                             $configEnd-$configStart + strlen(self::PREFERENCES_END_TAG));
            $this->loadPreferences($config);
        }
        $this->setWidth(400);
        $this->setHeight(300);
    }
    
    private function loadPreferences($config) {
        $xml = @(new SimpleXMLElement($config));
        foreach ($xml->preference as $preference) {
            $this->processPreference($preference);
        }
    }
    
    private function processPreference($preference) {
        $type = (string) $preference['type'];
        if ($type == 'hidden') {
            return;
        }
        $formField = new Gpf_Db_FormField();
        $formField->setFormId($this->getPreferencesFormId());
        $formField->setName((string) $preference['label']);
        $formField->setCode((string) $preference['name']);
        $formField->setStatus(Gpf_Db_FormField::STATUS_OPTIONAL);
        switch ($type) {
            case 'password':
                $formField->setType(Gpf_Db_FormField::TYPE_PASSWORD);
                break;
            case 'text':
                $formField->setType(Gpf_Db_FormField::TYPE_TEXT);
                break;
            case 'boolean':
                $formField->setType(Gpf_Db_FormField::TYPE_CHECKBOX);
                break;
            case 'range':
                $formField->setType(Gpf_Db_FormField::TYPE_LISTBOX);
                $formField->clearAvailableValues();
                for ($i=$preference['min']; $i<=$preference['max']; $i+=$preference['step']) {
                    $formField->addAvailableValue($i, $i);
                }
                break;
            case 'list':
                $formField->setType(Gpf_Db_FormField::TYPE_LISTBOX);
                $formField->clearAvailableValues();
                foreach ($preference->option as $option) {
                    $formField->addAvailableValue((string) $option['value'],
                                                  (string) $option['label']);
                }
                break;
        }
        $this->addPreferenceField($formField);
    }
}
?>
