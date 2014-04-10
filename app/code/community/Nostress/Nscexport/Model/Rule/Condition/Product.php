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

class Nostress_Nscexport_Model_Rule_Condition_Product extends Mage_CatalogRule_Model_Rule_Condition_Product
{
    /**
     *
     * @var
     */
    protected $_store = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->setType('nscexport/rule_condition_product');
    }

    public function getJsFormObject()
    {
        return 'conditions';
    }

    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $byInputType = $this->getOperatorByInputType();
        $byInputType['multiselect'] = array('==', '!=', '()', '!()');
        $this->setOperatorByInputType($byInputType);
        return $this;
    }

    public function loadAttributeOptions()
    {
        $attributes = array();
        $attributesValues = Mage::helper('nscexport/data_feed')->getAttributeOptions( $this->getStoreId(), true,"",false,$this->getFeedCode());
        unset( $attributesValues[0]);
        
        $disabledAttributes = Mage::helper('nscexport')->getConditionsDisabledAttributes();
        
        foreach( $attributesValues as $key => $attribute) {
            if( !in_array( $key, $disabledAttributes)) {
                $attributes[ $attribute['value']] = $attribute['label'];
            }
        }
        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getInputType()
    {
        if ($this->getAttribute()==='type_id') {
            return 'multiselect';
        }
        return parent::getInputType();
    }

    public function getValueElementType()
    {
        if ($this->getAttribute()==='type_id') {
            return 'multiselect';
        }
        return parent::getValueElementType();
    }

    public function getValueSelectOptions()
    {
        if ($this->getAttribute()==='type_id') {
            $arr = Mage::getSingleton('catalog/product_type')->getOptionArray();
            $options = array();
            foreach ($arr as $k=>$v) {
                $options[] = array('value'=>$k, 'label'=>$v);
            }
            return $options;
        }
        return parent::getValueSelectOptions();
    }
    
    protected function _getAttributeAliases( $attribute, $columns) {
        
        foreach( $columns as $tableAlias => $tableColumns) {
            foreach( $tableColumns as $columnAlias => $columnValue) {
                if( $attribute == $columnAlias) {
                    if( strpos( $columnValue, $tableAlias) !== false) {
                        return $columnValue;
                    } else {
                        return $tableAlias.".".$columnValue;
                    }
                }
            }
        }
        return false;
    }
    
    /**
     * @param Zend_Db_Select
     * @return bool|mixed|string
     */
    public function asSqlWhere( $columns)
    {
        $a = $where = $this->getAttribute();
        $o = $this->getOperator();
        $v = $this->getValue();
        if (is_array($v)) {
            $ve = addslashes(join(',', $v));
        } else {
            $ve = addslashes($v);
        }
        
        if(($attributeAlias = $this->_getAttributeAliases($a, $columns)) === false) {
            return false;
        }


        $attr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $a);

        // whether attribute is multivalue
        $m = $attr->getId() && ($attr->getFrontendInput() == 'multiselect');

        switch ($o) {
        case '==': 
            $wt = '{{ta}}'.'='."'{$ve}'";
            break;
        case '!=':
            $wt = '{{ta}}'.'<>'."'{$ve}' OR {{ta}} IS NULL";
            break;

        case '>=': case '<=': case '>': case '<':
            $wt = "{{ta}}{$o}'{$ve}'";
            break;

        case '{}':
            $wt = "{{ta}} LIKE '%{$ve}%'";
            break;
        case '!{}':
            $wt = "{{ta}} NOT LIKE '%{$ve}%' OR {{ta}} IS NULL";            
            break;

        case '()': 
            $va = preg_split('|\s*,\s*|', $ve);
            if (!$m) {
                $wt = "{{ta}} IN ('".join("','", $va)."')";
            } else {
                $w1 = array();
                foreach ($va as $v1) {
                    $w1[] = "find_in_set('".addslashes($v1)."', {{ta}})";
                }
                $wt = '('.join(') OR (', $w1).')';
            }
            break;
        
       case '!()':
            $va = preg_split('|\s*,\s*|', $ve);
            if (!$m) {
                $wt = "{{ta}} NOT IN ('".join("','", $va)."')";
            } else {
                $w1 = array();
                foreach ($va as $v1) {
                    $w1[] = "find_in_set('".addslashes($v1)."', {{ta}})";
                }
                $wt = '('.join(') OR (', $w1).')';
            }
            $wt = "({$wt}) OR {{ta}} IS NULL";
            
            break;

        default:
            return false;
        }
        
        $w = str_replace('{{ta}}', $attributeAlias, $wt);
        
        return $w;
    }

    public function getStoreId() {
        
        if (!$this->_store) {
            $profile = Mage::registry( 'nscexport_profile');
            if( $profile) {
                $this->_store = $profile->getStoreId();
            } else {
                $this->_store = Mage::registry('nscexport_store');
            }
        }
        return $this->_store;
    }
    
    public function getFeedCode() {
    
    	if (!$this->_feedCode) {
    		$this->_feedCode = Mage::registry( 'nsc_current_feed_code');
    	}
    	return $this->_feedCode;
    }
}