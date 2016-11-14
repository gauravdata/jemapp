<?php

/**
 * @category    Twm
 * @package     Twm_TranslationHelper
 * @author      The Webmen <info@thewebmen.com>
 * @copyright   2012 The Webmen
 */
class Twm_TranslationHelper_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid_Container {
    
    public function __construct() {
        //where is the controller
        $this->_controller = 'adminhtml_translations';
        $this->_blockGroup = 'translationhelper';

        //text in the admin header
        $this->_headerText = 'Translation Management';

        //value of the add button
        $this->_addButtonLabel = 'Add a translation';
        
        $this->_addButton('refresh', array(
            'label' => Mage::helper('translationhelper')->__('Reload from filesystem'),
            'onclick' => 'if (confirm(\'Are you sure? This can take a little while...\')) setLocation(\'' . Mage::getSingleton('adminhtml/url')->getUrl('translationhelper/adminhtml_index/refresh') . '\')'
        ));

        parent::__construct();
    }
    
}