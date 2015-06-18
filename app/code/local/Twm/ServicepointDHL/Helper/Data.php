<?php
class Twm_ServicepointDHL_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getExtraHtml($code)
    {
		if ($code == 'servicepointdhl') {
			$block = Mage::getBlockSingleton('servicepointdhl/search');
			$block->setTemplate('servicepointdhl/search.phtml');
			return $block->toHtml();
		}
	}
    
}
