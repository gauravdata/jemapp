<?php
/**
* Magento Module developed by NoStress Commerce
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to info@nostresscommerce.cz so we can send you a copy immediately.
*
* @copyright Copyright (c) 2013 NoStress Commerce (http://www.nostresscommerce.cz)
*
*/

/**
*
* @category Nostress
* @package Nostress_Nscexport
*
*/

class Nostress_Nscexport_Model_Rule_Condition_Combine extends Mage_CatalogRule_Model_Rule_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('nscexport/rule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        $productCondition = Mage::getModel('nscexport/rule_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($productAttributes as $code=>$label) {
            $attributes[] = array('value'=>'nscexport/rule_condition_product|'.$code, 'label'=>$label);
        }
        $conditions = Mage_Rule_Model_Condition_Combine::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'nscexport/rule_condition_combine', 'label'=>Mage::helper('catalogrule')->__('Conditions Combination')),
            array('label'=>Mage::helper('catalogrule')->__('Product Attribute'), 'value'=>$attributes),
        ));
        return $conditions;
    }

    public function asSqlWhere( $columns)
    {
        $w = array();
        foreach ($this->getConditions() as $cond) {
            $w[] = $cond->asSqlWhere( $columns);
        }
        if (!$w) {
            return false;
        }
        $a = $this->getAggregator();
        $v = $this->getValue();
        return ($v ? '' : 'NOT ').'('.join(') '.($a=='all' ? 'AND' : 'OR').' '.($v ? '' : 'NOT ').'(', $w).')';
    }
}