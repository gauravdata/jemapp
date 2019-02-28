<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookCatalog
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
class Apptrian_FacebookCatalog_Adminhtml_Apptrian_FbfeedController
    extends Mage_Adminhtml_Controller_Action
{
    
    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('system/config/apptrian_facebookcatalog');
    }
    
    /**
     * Generate product feed action.
     */
    public function generateAction()
    {
        set_time_limit(18000);
        
        $helper = Mage::helper('apptrian_facebookcatalog');
        
        try {
            $helper->generate();
            
            $message = $this
                ->__('Product feed generation completed successfully.');
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
        } catch (Exception $e) {
            $message = $this->__('Product feed generation failed.');
            Mage::getSingleton('adminhtml/session')->addError($message);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        
        $url = Mage::helper('adminhtml')->getUrl(
            'adminhtml/system_config/edit/section/apptrian_facebookcatalog'
        );
        Mage::app()->getResponse()->setRedirect($url);
    }
}
