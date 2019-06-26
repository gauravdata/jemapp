<?php
class Biztech_Translator_Model_System_Config_Magemodules {
    public function toOptionArray() {
        $modules[] = array('label' => "All", 'value' => "all");
        $moduleKeys = array_keys((array) Mage::getConfig()->getNode('modules')->children());
        foreach ($moduleKeys as $key => $className) {
            $modules[] = array('label' => $className, 'value' => $className);
        }
        return $modules;
    }
    public function getInterfaceArray(){
        $options = array();
        $options[] = array(
            "value" => "frontend",
            "label" => Mage::helper('translator')->__("Frontend"));
        $options[] = array(
            "value" => "adminhtml",
            "label" => Mage::helper('translator')->__("Admin HTML"));
        
        return $options;
    }
}