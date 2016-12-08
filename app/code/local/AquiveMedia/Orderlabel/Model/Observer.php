<?php
/**
 * Author: Jeroen Smit - Smit Webdevelopment - www.smit-web.nl
 * Contact: jeroen@smit-web.nl
 * Copyright: Aquive Media
 * Created: 12/2/11
 */
class AquiveMedia_Orderlabel_Model_Observer
{
    public function addAction($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction) {
            if ($block->getParentBlock() instanceof Mage_Adminhtml_Block_Sales_Order_Grid || $block->getParentBlock() instanceof MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid) {
                $block->addItem('mass_print_labels', array(
                        'label' => Mage::helper('orderlabel')->__('Print Labels'),
                        'url' => $block->getUrl('orderlabel/adminhtml_orderlabel/massprint')
                    )
                );
            }
        }
    }

    public function addPrintButton($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $blockClass = get_class($block);
        switch ($blockClass) {
            case "Fooman_PdfCustomiser_Block_View":
            case "Mage_Adminhtml_Block_Sales_Order_View":
                $buttonOptions = array(
                    'name' => 'labelConfirm',
                    'label' => Mage::helper('orderlabel')->__('Loading...')
                );

                if (!Mage::helper('orderlabel')->isActiveLayoutValid()) {
                    $buttonOptions['name'] = 'labelError';
                    $buttonOptions['label'] = 'Print Label';
                    $buttonOptions['onclick'] = "alert('Module not fully configured. Please select a valid layout.');";
                }
                $block->addButton('labelConfirm', $buttonOptions, 0, 100, Mage::getStoreConfig('orderlabel/general/buttonlocation'));
                break;
            default:
                break;
        }
        return $this;
    }
}
