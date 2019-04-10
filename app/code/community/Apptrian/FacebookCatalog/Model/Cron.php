<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookCatalog
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
class Apptrian_FacebookCatalog_Model_Cron
{
    /**
     * Cron method for executing product feed generation process.
     */
    public function generate()
    {
        // Must set current store to admin so cron can get config values
        Mage::app()->setCurrentStore(0);
        
        $helper = Mage::helper('apptrian_facebookcatalog');
        
        $cronJobEnabled = (int) $helper->getConfig(
            'apptrian_facebookcatalog/cron/enabled'
        );
            
        if ($cronJobEnabled) {
            try {
                $helper->generate();
                Mage::log('Product Feed generated successfully.');
            } catch (Exception $e) {
                Mage::log($e);
            }
        }
    }
}
