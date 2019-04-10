<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookCatalog
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
class Apptrian_FacebookCatalog_Block_Adminhtml_Text
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Retrieve element HTML markup.
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(
        Varien_Data_Form_Element_Abstract $element
    ) {
        $element = null;
        
        $helper = Mage::helper('apptrian_facebookcatalog');
        
        $text = $helper->__('This field will be populated automatically.');
        
        $html = '<div style="padding: 0 0 10px 0;">' . $text . '</div>';
        
        return $html;
    }
}
