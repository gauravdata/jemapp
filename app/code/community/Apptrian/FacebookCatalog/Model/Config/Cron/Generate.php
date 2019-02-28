<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookCatalog
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
class Apptrian_FacebookCatalog_Model_Config_Cron_Generate
    extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH =
        'crontab/jobs/apptrian_facebookcatalog_generate/schedule/cron_expr';
    
    protected function _afterSave()
    {
        $cronExprString = $this->getValue();
        
        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
        } catch (Exception $e) {
            Mage::throwException(
                Mage::helper('cron')->__('Unable to save the cron expression.')
            );
        }
    }
    
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
            array('pattern' => '/^[0-9,\-\?\/\*\ ]+$/')
        );
        
        if (!$validator) {
            $errors[] = $helper->__('Cron expression is invalid.');
        }
        
        if (empty($errors)) {
            return true;
        }
        
        return $errors;
    }
}
