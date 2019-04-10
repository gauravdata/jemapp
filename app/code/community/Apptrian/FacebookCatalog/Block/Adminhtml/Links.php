<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookCatalog
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
class Apptrian_FacebookCatalog_Block_Adminhtml_Links
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
        $element   = null;
        
        $data = Mage::helper('apptrian_facebookcatalog')->getProductFeedLinks();
        
        $html = '<div>';
        
        foreach ($data as $d) {
            $html .= '<p><span>' . $d['name'] . ':</span><br />';
            $html .= '<a href="'. $d['url'] . '" download="' . $d['filename']
            . '" target="_blank">' . $d['url'] . '</a></p>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}
