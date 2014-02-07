<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Customer Credit extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @author     MageWorx Dev Team
 */

class MageWorx_CustomerCredit_Block_Adminhtml_Customer_Edit_Tab_CustomerCredit_Adjust extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() {
        $model = Mage::registry('current_customer');        
        $customerId = $model->getId();               
        $data = $model->getData();
        
        
        $helper = Mage::helper('customercredit');
        $creditValue = $helper->getCreditValue($customerId, $model->getWebsiteId());        
        $data['credit_value'] = $creditValue;        
        
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('customercredit_');
        $form->setFieldNameSuffix('customercredit');
                
        $fieldset = $form->addFieldset('adjust_fieldset', array('legend'=>$helper->__('Adjust Credit')));        
        $expired = '';
        
//echo "<pre>"; print_r($model->getData()); exit;

        if(Mage::getStoreConfig('mageworx_customers/customercredit_expiration/expiration_enable') && $creditValue) {
            $daysLeft = Mage::helper('customercredit')->getCreditExpired($customerId, $model->getWebsiteId());
            if($daysLeft) {
                $expired = " (".$this->__('Expire in %s day(s)',"<b>".$daysLeft."</b>").")";
            }
        }
        $fieldset->addField('credit_value', 'hidden', array(
            'name'     => 'credit_value',            
            'after_element_html' => '</td></tr><tr><td class="label">'.$helper->__('Current Balance').'</td>
                                     <td id="customercredit_credit_website_value" class="value">
                                     '.Mage::helper('core')->currency($creditValue).$expired,
        ));
        
        $fieldset->addField('value_change', 'text', array(
            'name'     => 'value_change',
            'label'    => $helper->__('Credit Value'),
            'title'    => $helper->__('Credit Value'),
            'note'     => $helper->__('A negative value subtracts from the credit balance'),
            'class'    => 'validate-currency-dollar',
            'after_element_html' => '<div id="customercredit_currency_code"></div>',
        ));

        
        if ($helper->isScopePerWebsite()) {
            // js change balance
            $script = '';
            foreach (Mage::app()->getWebsites() as $website) {
                $script .= 'vs['.$website->getId().']=\''.$this->htmlEscape(Mage::helper('core')->currency($helper->getCreditValue($customerId, $website->getId()))).'\';';
            }
            $fieldset->addField('website_id', 'select', array(
                'name'     => 'website_id',
                'label'    => $helper->__('Website'),
                'title'    => $helper->__('Website'),
                'values'   => Mage::getModel('adminhtml/system_store')->getWebsiteValuesForForm(),
                'onchange' =>  "var vs = []; ".$script." if (vs[this.value]) $('customercredit_credit_website_value').innerHTML = vs[this.value];"
            ));
        }
        
        $fieldset->addField('comment', 'textarea', array(
            'name'     => 'comment',
            'label'    => $helper->__('Comment'),
            'title'    => $helper->__('Comment'),
            'class'    => 'mageworx_customercredit_comment',
        ));
        
        $form->setValues($data);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
    
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $jsCusrrency = $this->getLayout()->createBlock('core/template')->setTemplate('customercredit/currency_js.phtml');
        $this->setChild('js_currency', $jsCusrrency);
    }
    
    protected function _toHtml() {
        $html = parent::_toHtml();
        $html .= $this->getChild('js_currency')->toHtml();;
        return $html;
    }
    
    public function getWebsiteHtmlId() {
        return 'customercredit_website_id';
    }
}