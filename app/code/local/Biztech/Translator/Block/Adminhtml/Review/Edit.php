<?php
    /**
    * Magento
    *
    * NOTICE OF LICENSE
    *
    * This source file is subject to the Open Software License (OSL 3.0)
    * that is bundled with this package in the file LICENSE.txt.
    * It is also available through the world-wide-web at this URL:
    * http://opensource.org/licenses/osl-3.0.php
    * If you did not receive a copy of the license and are unable to
    * obtain it through the world-wide-web, please send an email
    * to license@magentocommerce.com so we can send you a copy immediately.
    *
    * DISCLAIMER
    *
    * Do not edit or add to this file if you wish to upgrade Magento to newer
    * versions in the future. If you wish to customize Magento for your
    * needs please refer to http://www.magentocommerce.com for more information.
    *
    * @category    Mage
    * @package     Mage_Adminhtml
    * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
    * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
    */

    /**
    * Category container block
    *
    * @category   Mage
    * @package    Mage_Adminhtml
    * @author      Magento Core Team <core@magentocommerce.com>
    */
    class Biztech_Translator_Block_Adminhtml_Review_Edit extends Mage_Adminhtml_Block_Review_Edit
    {
        public function __construct()
        {
            parent::__construct();
            $popup_data = '<div class="overlay_magento" id="admin-popup-overlay" style="display: none; position: fixed; top: 0px; left: 0px; z-index: 1001; width: 100%; height: 100%;"></div>';
            $popup_data.='<div class="overlay_magento" id="error-overlay" style="display: none; position: absolute; top: 0px; left: 0px; z-index: 1001; width: 100%; height: 1099px;"></div>';
            $popup_data.='<div id="popup-error" class="magento_content" style="position: absolute; display: none; width: 500px; z-index: 1011; top: 40%; left: 40%; height: 150px">';
            $popup_data.='<div class="magento_close" id="widget_window_close" onclick="closewindow()"></div>';
            $popup_data.='<div class="top table_window" style="height: 30px; padding-left: 10px;">';
            $popup_data.='<div class="magento_title">"'.$this->__('Error:').'"</div>';
            $popup_data.='</div>';
            $popup_data.='</div>';

            
            if(Mage::getStoreConfig('translator/translator_general/enabled')){
                $this->_formInitScripts[] = "
                Translator.add('Biztech Translator:','".$this->__('Biztech Translator:')."');
                Translator.add('Apply Translate:','".$this->__('Apply Translate:')."');
                Translator.add('Translate to:','".$this->__('Translate to:')."');
                Translator.add('Unknown Error!:','".$this->__('Unknown Error!:')."');
                Translator.add('TRANSLATE TO','".$this->__('TRANSLATE TO:')."');
                Translator.add('Select Language for this store in System->Config->Translator','".$this->__('Select Language for this store in System->Config->Translator')."');


                BiztechTranslatorConfig = '".Mage::helper('translator/languages')->getBiztechTranslatorReviewConfiguration()."';


                Event.observe(window, 'load', function() {  
                $('review_details').insert({ after: '".$popup_data."'});
                translator = new BiztechTranslatorReviewPage('edit_form',BiztechTranslatorConfig);
                });

                function closewindow(){
                $('popup-error').style.display='none';
                $('error-overlay').style.display = 'none';
                }
                ";
            }else{
                $this->_formInitScripts[] = '
            var review = {
                updateRating: function() {
                        elements = [
                            $("select_stores"),
                            $("rating_detail").getElementsBySelector("input[type=\'radio\']")
                        ].flatten();
                        $(\'save_button\').disabled = true;
                        new Ajax.Updater(
                            "rating_detail",
                            "' . $this->getUrl('*/*/ratingItems', array('_current'=>true)).'",
                            {
                                parameters:Form.serializeElements(elements),
                                evalScripts:true,
                                onComplete:function(){ $(\'save_button\').disabled = false; }
                            }
                        );
                    }
           }
           Event.observe(window, \'load\', function(){
                 Event.observe($("select_stores"), \'change\', review.updateRating);
           });
        ';
            }


        }



        protected function _prepareLayout()
        {
            $this->getLayout()->getBlock('head')->addJs('biztech/translator/popup_window.js');

            $this->getLayout()->getBlock('head')->addJs('biztech/translator/biztech_translator.js');
            $this->getLayout()->getBlock('head')->addCss('biztech/translator/style.css');
            $this->getLayout()->getBlock('head')->addCss('lib/prototype/windows/themes/magento.css');
            $this->getLayout()->getBlock('head')->addCss('prototype/windows/themes/default.css');
            parent::_prepareLayout();
        }
        public function getHeaderText()
        {
            if(Mage::registry('review_data') && Mage::registry('review_data')->getId()) {
                return Mage::helper('review')->__("Edit Review '%s'", $this->escapeHtml(Mage::registry('review_data')->getTitle()));
            } else {
                return Mage::helper('review')->__('New Review');
            }
        }
}