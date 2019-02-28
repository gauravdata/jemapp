<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookCatalog
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
class Apptrian_FacebookCatalog_Block_Adminhtml_Button_Generate
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Retrieve element HTML markup
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(
        Varien_Data_Form_Element_Abstract $element
    ) {
        $elementOriginalData = $element->getOriginalData();
        
        if (isset($elementOriginalData['label'])) {
            $buttonLabel = $elementOriginalData['label'];
        } else {
            return '<div>Button label was not specified</div>';
        }
        
        $url = Mage::helper('adminhtml')->getUrl(
            'adminhtml/apptrian_fbfeed/generate'
        );
        
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('apptrian-facebookcatalog-admin-button-generate')
            ->setLabel($buttonLabel)
            ->setOnClick("setLocation('$url')")
            ->toHtml();
            
        return $html;
    }
}
