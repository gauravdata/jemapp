<?php

class Twm_MinOrderQty_Model_System_Config_Source_Coupon
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$list = Mage::getModel('salesrule/rule')->getCollection();
		$result = array();
		foreach ($list as $item) {
			$result[] = array(
				'value' => $item->getId(),
				'label' => $item->getName()
			);
		}

		return $result;
	}
}
