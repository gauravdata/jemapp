<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookCatalog
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
class Apptrian_FacebookCatalog_Model_Config_Additionalimagelink
    extends Mage_Core_Model_Config_Data
{
    public function _beforeSave()
    {
        $result = $this->validate();
        
        if ($result !== true) {
            Mage::throwException(implode("\n", $result));
        }
        
        return parent::_beforeSave();
    }
    
    public function validate()
    {
        $errors    = array();
        $helper    = Mage::helper('apptrian_facebookcatalog');
        $value     = $this->getValue();
        $validator = Zend_Validate::is(
            $value,
            'Regex',
            array('pattern' => '/^[0-9]*$/')
        );
        
        if (!$validator) {
            $errors[] = $helper->__(
                'Additional Image Link limit is not valid.'
            );
        }
        
        if ($value > 10) {
            $errors[] = $helper->__(
                'Allowed values for Additional Image Link Limit are 0 to 10.'
            );
        }
        
        if (empty($errors)) {
            return true;
        }
        
        return $errors;
    }
}
