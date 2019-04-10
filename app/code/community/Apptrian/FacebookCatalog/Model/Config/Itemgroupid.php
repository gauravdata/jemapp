<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookCatalog
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
class Apptrian_FacebookCatalog_Model_Config_Itemgroupid
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
            array('pattern' => '/^[\p{L}\p{N}_,;:!&#\+\*\$\?\|\'\.\-]*$/iu')
        );
        
        if (!$validator) {
            $errors[] = $helper->__(
                'Item Group ID attribute code is not valid.'
            );
        }
        
        if (empty($errors)) {
            return true;
        }
        
        return $errors;
    }
}
