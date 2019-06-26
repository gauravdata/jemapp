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
    class Biztech_Translator_Block_Adminhtml_Review_Add extends Mage_Adminhtml_Block_Review_Add
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
                $('add_review_form').insert({ after: '".$popup_data."'});
                translator = new BiztechTranslatorReviewPage('edit_form',BiztechTranslatorConfig);
                });

                function closewindow(){
                $('popup-error').style.display='none';
                $('error-overlay').style.display = 'none';
                }
                ";
            }else{
                       $this->_formInitScripts[] = '
            //<![CDATA[
            var review = function() {
                return {
                    productInfoUrl : null,
                    formHidden : true,

                    gridRowClick : function(data, click) {
                        if(Event.findElement(click,\'TR\').title){
                            review.productInfoUrl = Event.findElement(click,\'TR\').title;
                            review.loadProductData();
                            review.showForm();
                            review.formHidden = false;
                        }
                    },

                    loadProductData : function() {
                        var con = new Ext.lib.Ajax.request(\'POST\', review.productInfoUrl, {success:review.reqSuccess,failure:review.reqFailure}, {form_key:FORM_KEY});
                    },

                    showForm : function() {
                        toggleParentVis("add_review_form");
                        toggleVis("productGrid");
                        toggleVis("save_button");
                        toggleVis("reset_button");
                    },

                    updateRating: function() {
                        elements = [$("select_stores"), $("rating_detail").getElementsBySelector("input[type=\'radio\']")].flatten();
                        $(\'save_button\').disabled = true;
                        var params = Form.serializeElements(elements);
                        if (!params.isAjax) {
                            params.isAjax = "true";
                        }
                        if (!params.form_key) {
                            params.form_key = FORM_KEY;
                        }
                        new Ajax.Updater("rating_detail", "'.$this->getUrl('*/*/ratingItems').'", {parameters:params, evalScripts: true,  onComplete:function(){ $(\'save_button\').disabled = false; } });
                    },

                    reqSuccess :function(o) {
                        var response = Ext.util.JSON.decode(o.responseText);
                        if( response.error ) {
                            alert(response.message);
                        } else if( response.id ){
                            $("product_id").value = response.id;

                            $("product_name").innerHTML = \'<a href="' . $this->getUrl('*/catalog_product/edit') . 'id/\' + response.id + \'" target="_blank">\' + response.name + \'</a>\';
                        } else if( response.message ) {
                            alert(response.message);
                        }
                    }
                }
            }();

             Event.observe(window, \'load\', function(){
                 if ($("select_stores")) {
                     Event.observe($("select_stores"), \'change\', review.updateRating);
                 }
           });
           //]]>
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
            return Mage::helper('review')->__('Add New Review');
        }
}