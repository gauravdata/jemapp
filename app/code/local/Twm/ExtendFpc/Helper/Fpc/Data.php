<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 14-12-2015
 * Time: 20:48
 */ 
class Twm_ExtendFpc_Helper_Fpc_Data extends Lesti_Fpc_Helper_Data
{
    /**
     * @return mixed
     */
    protected function _getParams()
    {
        $params = parent::_getParams();

        if (is_array($params)) {
            $appliedIds = array();
            $session = Mage::getModel('checkout/cart')->getCheckoutSession();
            if ($session->hasQuote()) {
                $items = $session->getQuote()->getAllItems();
                foreach ($items as $item) {
                    if (!$item->getParentItemId()) {
                        continue;
                    }
                    $item = ($item->getParentItem() ? $item->getParentItem() : $item);
                    $appliedIds = array_merge($appliedIds, $item->getAppliedRuleIds());
                }
                $params['sales_rule'] = implode('-', $appliedIds);
            }
        }

        return $params;
    }
}