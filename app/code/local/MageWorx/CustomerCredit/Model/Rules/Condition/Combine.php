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
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2010 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */
 
/**
 * Customer Credit extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_CustomerCredit_Model_Rules_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('customercredit/rules_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        
        $addressCondition  = Mage::getModel('customercredit/rules_condition_address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($addressAttributes as $code=>$label) {
            $attributes[] = array('value'=>'customercredit/rules_condition_address|'.$code, 'label'=>$label);
        }
        
        $actionCondition  = Mage::getModel('customercredit/rules_condition_action');
        $_actionAttributes = $actionCondition->loadAttributeOptions()->getAttributeOption();
        $actionAttributes = array();
        foreach ($_actionAttributes as $code=>$label) {
            $actionAttributes[] = array('value'=>'customercredit/rules_condition_action|'.$code, 'label'=>$label);
        }
        
        $conditionProductAttributes = Mage::getModel('catalogrule/rule_condition_product')->getAttributeOption();
        $productAttributes = array();
        foreach ($conditionProductAttributes as $code=>$label) {
            $productAttributes[] = array('value'=>'catalogrule/rule_condition_product|'.$code, 'label'=>$label);
        }
                

        $conditions = parent::getNewChildSelectOptions();
        $model = Mage::getModel('customercredit/rules');
        $rule_type = MageWorx_Customercredit_Model_Rules::CC_RULE_TYPE_APPLY;
        if(!Mage::app()->getRequest()->getParam('current_rule_type')) {
            $rule_type = $model->load(Mage::app()->getRequest()->getParam('id',false))->getRuleType();
        } else {
            $rule_type = Mage::app()->getRequest()->getParam('current_rule_type');
        }
        
        $arrays = array();
        $arrays[] = array('label'=>Mage::helper('salesrule')->__('Conditions combination'), 'value'=>'customercredit/rules_condition_combine');
        if($rule_type != MageWorx_Customercredit_Model_Rules::CC_RULE_TYPE_APPLY) {
            $arrays[] = array('label'=>Mage::helper('salesrule')->__('Customer Attributes'), 'value'=>$attributes);
            $arrays[] = array('label'=>Mage::helper('salesrule')->__('Customer Actions'), 'value'=>$actionAttributes);
        };
        
        $arrays[] = array('label'=>Mage::helper('catalogrule')->__('Product Attributes'), 'value'=>$productAttributes);

        $conditions = array_merge_recursive($conditions, $arrays);
        return $conditions;
    }
}
