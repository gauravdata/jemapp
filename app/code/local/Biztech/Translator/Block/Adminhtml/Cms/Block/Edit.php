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
    * Cms page edit form main tab
    *
    * @category   Mage
    * @package    Mage_Adminhtml
    * @author      Magento Core Team <core@magentocommerce.com>
    */

    class Biztech_Translator_Block_Adminhtml_Cms_Block_Edit extends Mage_Adminhtml_Block_Cms_Block_Edit

    {


        public function __construct()
        {

            $popup_data = '<div class="overlay_magento" id="admin-popup-overlay" style="display: none; position: fixed; top: 0px; left: 0px; z-index: 1001; width: 100%; height: 100%;"></div>';
            $popup_data.='<div class="overlay_magento" id="error-overlay" style="display: none; position: absolute; top: 0px; left: 0px; z-index: 1001; width: 100%; height: 1099px;"></div>';
            $popup_data.='<div id="popup-error" class="magento_content" style="position: absolute; display: none; width: 500px; z-index: 1011; top: 40%; left: 40%; height: 150px">';
            $popup_data.='<div class="magento_close" id="widget_window_close" onclick="closewindow()"></div>';
            $popup_data.='<div class="top table_window" style="height: 30px; padding-left: 10px;">';
            $popup_data.='<div class="magento_title">"'.$this->__('Error:').'"</div>';
            $popup_data.='</div>';
            $popup_data.='</div>';

            $this->_objectId = 'block_id';
            $this->_controller = 'cms_block';

            parent::__construct();

            $this->_updateButton('save', 'label', Mage::helper('cms')->__('Save Block'));
            $this->_updateButton('delete', 'label', Mage::helper('cms')->__('Delete Block'));

            $this->_addButton('saveandcontinue', array(
                    'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                    'onclick'   => 'saveAndContinueEdit()',
                    'class'     => 'save',
                ), -100);


            if(Mage::getStoreConfig('translator/translator_general/enabled')){
                $this->_formScripts[] = "
                function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                tinyMCE.execCommand('mceAddControl', false, 'block_content');
                } else {
                tinyMCE.execCommand('mceRemoveControl', false, 'block_content');
                }
                }

                function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
                }


                Translator.add('Biztech Translator:','".$this->__('Biztech Translator:')."');
                Translator.add('Apply Translate:','".$this->__('Apply Translate:')."');
                Translator.add('Translate to:','".$this->__('Translate to:')."');
                Translator.add('Unknown Error!:','".$this->__('Unknown Error!:')."');
                Translator.add('TRANSLATE TO','".$this->__('TRANSLATE TO:')."');
                Translator.add('Select Language for this store in System->Config->Translator','".$this->__('Select Language for this store in System->Config->Translator')."');

                BiztechTranslatorConfig = '".Mage::helper('translator/languages')->getBiztechTranslatorCmsblockConfiguration()."';


                Event.observe(window, 'load', function() {  
                $('block_base_fieldset').insert({ after: '".$popup_data."'});
                translator = new BiztechTranslatorCmsPage('edit_form',BiztechTranslatorConfig);
                });

                function closewindow(){
                $('popup-error').style.display='none';
                $('error-overlay').style.display = 'none';
                }

                ";
            }else{
                $this->_formScripts[] = "
                function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                tinyMCE.execCommand('mceAddControl', false, 'block_content');
                } else {
                tinyMCE.execCommand('mceRemoveControl', false, 'block_content');
                }
                }

                function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
                }
                ";
            }

        }

        /**
        * Get edit form container header text
        *
        * @return string
        */
        public function getHeaderText()
        {
            if (Mage::registry('cms_block')->getId()) {
                return Mage::helper('cms')->__("Edit Block '%s'", $this->escapeHtml(Mage::registry('cms_block')->getTitle()));
            }
            else {
                return Mage::helper('cms')->__('New Block');
            }
        }
    }
