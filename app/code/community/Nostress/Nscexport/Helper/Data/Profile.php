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
* @copyright Copyright (c) 2009 NoStress Commerce (http://www.nostresscommerce.cz)
*
*/

/**
* Helper.
*
* @category Nostress
* @package Nostress_Nscexport
*
*/

class Nostress_Nscexport_Helper_Data_Profile extends Nostress_Nscexport_Helper_Data
{
	protected $_errorList;

	public function runAllProfiles()
	{
		$this->runProfiles(Mage::getModel('nscexport/profile')->getAllProfiles());
	}

	public function runProfilesByIds($profileIds)
	{
		$profiles = Mage::getModel('nscexport/profile')->getProfilesByIds($profileIds);
		$this->runProfiles($profiles);
		return true;
	}

	public function runProfilesByNames($profileNames)
	{
		$profiles = Mage::getModel('nscexport/profile')->getProfilesByNames($profileNames);
		$this->runProfiles($profiles);
		return true;
	}

	public function runProfiles($profiles, $upload = true)
	{
		if(empty($profiles))
			return;
		Mage::helper('nscexport/version')->validateLicenceBackend();
		$this->reloadProfilesCache($profiles);
		foreach ($profiles as $item)
		{
			try
			{
				$item->setReloadCache(false);
				$this->runProfile($item, $upload);
			}
			catch(Exception $e)
			{
				$this->log($e->getMessage());
			}
		}
	}

	public function runProfile($profile, $upload = true)
	{
		if (!$profile->getId()) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Invalid Profile ID'));
		} else {
			Mage::getModel('nscexport/unit_control')->run($profile);
			if( $upload) {
		        $this->_runUploadFeed($profile);
			}
		}
	}
	
	protected function _runUploadFeed( $profile) {
	    try {
	        $profile->uploadFeed();
	        $profile->setMessage( $profile->getMessage()." ".$this->__('Upload:')." OK");
	        $profile->setStatus( Nostress_Nscexport_Model_Profile::STATUS_FINISHED);
	    } catch( Exception $e) {
	        if( $e->getCode() != Nostress_Nscexport_Model_Profile::CODE_NOT_ENABLED) {
	            $profile->setMessageStatusError( $profile->getMessage()." ".$this->__('Upload:')." ".$e->getMessage(),
                    Nostress_Nscexport_Model_Profile::STATUS_ERROR);
	        }
	    }
	}

    protected function reloadProfilesCache($profiles)
    {
    	if(empty($profiles))
    		return;
    	$storeIds = array();
    	$websiteIds = array();
    	$ids = array();
    	foreach ($profiles as $profile)
    	{
    		if(is_numeric($profile))
    			$ids[] = $profile;
    		else
    		{
    			$storeId = $profile->getStoreId();
    			if(!in_array($storeId,$storeIds))
    				$storeIds[] = $storeId;
    		}
    	}

    	if(!empty($ids))
    		$storeIds = Mage::getModel('nscexport/profile')->getStoreIdsByProfileIds($ids);

    	foreach(Mage::app()->getWebsites() as $website)
    	{
    		$websiteStoreIds = $website->getStoreIds();
    		$intersec = array_intersect($websiteStoreIds,$storeIds);
    		if(!empty($intersec))
    			$websiteIds[] = $website->getWebsiteId();
    	}

    	Mage::helper('nscexport/data_loader')->reloadCache($storeIds,$websiteIds);
    }

    public function updateProfilesFeedConfig()
    {
        $profiles = Mage::getModel('nscexport/profile')->getAllProfiles();

        foreach ($profiles as $profile)
        {
            $profile->updateProfileFeedConfig();
        }
    }
}