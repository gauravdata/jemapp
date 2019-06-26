<?php
class Biztech_Translator_Model_System_Config_Enabledisable{
    public function toOptionArray() 
    {
        $options = array(
            array('value' => 0, 'label'=>Mage::helper('translator')->__('No')),
        );
        $websites = Mage::helper('translator')->getAllWebsites();
        if(!empty($websites)){
        	$options[] = array('value' => 1, 'label'=>Mage::helper('translator')->__('Yes'));
        }
        return $options;
    }

}