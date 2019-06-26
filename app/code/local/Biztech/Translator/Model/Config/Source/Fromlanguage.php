<?php
class Biztech_Translator_Model_Config_Source_Fromlanguage extends Varien_Data_Collection{
    public function toOptionArray(){
        $options = array();
        $languages = Mage::helper('translator/languages')->getLanguages();
        $options[] = array('label' => 'Auto detect', 'value' => 'auto');
        foreach($languages as $key => $language){
            $options[] = array(
                'label' => strtoupper($key).': '.$language,
                'value' => $key
            );
        }
        return ($options);
    }
}
