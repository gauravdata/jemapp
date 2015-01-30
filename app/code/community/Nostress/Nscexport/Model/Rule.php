<?php
/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    Unirgy_CatalogTest
 * @copyright  Copyright (c) 2008-2009 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */
class Nostress_Nscexport_Model_Rule extends Mage_Rule_Model_Rule
{
    public function getConditionsInstance()
    {
        return Mage::getModel('nscexport/rule_condition_combine');
    }
    
    public function parseConditionsPost(array $rulePost)
    {
        $arr = $this->_convertFlatToRecursive($rulePost);
        if (isset($arr['conditions'])) {
            return $arr['conditions'][1];
        } else {
            return false;
        }
    }
    
    public function initConditions( array $conditionsConfig)
    {
        $this->getConditions()->setConditions(array())->loadArray( $conditionsConfig);
        return $this;
    }
}