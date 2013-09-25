<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Tracking_Click_SaveClick extends Gpf_Object implements Pap_Tracking_Common_Saver {

	protected $clicks = array();

	public function __construct() {
	}
	
	public function process(Pap_Contexts_Tracking $context) {
		$banner = $context->getBannerObject();
		$rawClick = $this->createRawClick($context, $banner);
		if($context->getDoTrackerSave() && $rawClick != null) {
			$this->saveRawClick($rawClick);
			$this->saveClick($context, new Pap_Db_Click(), $banner, $rawClick);
		}
		$context->debug('');
	}
	
	public function saveChanges() {
		foreach ($this->clicks as $click) {
			$click->save();
		}
	}

	private function getCampaignId(Pap_Contexts_Click $context) {
		$campaignObj = $context->getCampaignObject();
		if($campaignObj != null) {
			return $campaignObj->getId();
		}
		return null;
	}

	private function getChannel(Pap_Contexts_Click $context) {
		$channelObj = $context->getChannelObject();
		if($channelObj != null) {
			return $channelObj->getId();
		}
		return null;
	}

	protected function isClickUnique(Pap_Contexts_Click $context) {
	    return $context->getVisit()->isNewVisitor();
	}

	/**
	 * @param Pap_Contexts_Click $context
	 * @param Pap_Common_Banner $banner
	 *
	 * @return Pap_Db_RawClick
	 */
	private function createRawClick(Pap_Contexts_Click $context, Pap_Common_Banner $banner=null){
		$context->debug('    Creating raw click started');

		if ($context->getUserObject() == null) {
			$context->debug('    Raw clicked not created. User not set.');
			return null;
		}

		$click = new Pap_Db_RawClick();
		$click->setUserId($context->getUserObject()->getId());
		if($banner!=null){
			$click->setBannerId($banner->getId());
			$click->setParentBannerId($banner->getParentBannerId());
		}
		$click->setCampaignId($this->getCampaignId($context));
	    $click->setIp($context->getIp());
		$click->setCountryCode($context->getCountryCode());
		$click->setData1($context->getExtraDataFromRequest(1));
		$click->setData2($context->getExtraDataFromRequest(2));
		$click->setChannel($this->getChannel($context));
		$click->setDateTime($context->getVisitDateTime());
		$click->setRefererUrl($context->getReferrerUrl());
		$click->setBrowser($context->getUserAgent());
		$click->setProcessedStatus(true);
		$click->setType($this->getType($context));
		$context->setRawClickObject($click);
			
		$context->debug('    Creating raw click ended');

		return $click;
	}

	private function getType(Pap_Contexts_Click $context) {
		if($context->getClickStatus() == Pap_Db_ClickImpression::STATUS_DECLINED) {
			return Pap_Db_ClickImpression::STATUS_DECLINED;
		}
		if($this->isClickUnique($context)) {
			return Pap_Db_ClickImpression::STATUS_UNIQUE;
		}
		return Pap_Db_ClickImpression::STATUS_RAW;
	}
	
	private function hashClick(Pap_Db_Click $clickPrototype, $clickParams) {
		return md5(implode('_', array_values($clickParams)));
	}
	
	private function saveClick(Pap_Contexts_Click $context, Pap_Db_Click $clickPrototype,
	                            Pap_Common_Banner $banner=null, Pap_Db_RawClick $rawClick) {
	    $context->debug('Saving click (as object, not rawclick)');
		$clickParams = $this->getClickParamsArray($clickPrototype, $context, $banner);
		$hash = $this->hashClick($clickPrototype, $clickParams);
        if (!array_key_exists($hash, $this->clicks)) {
            $this->clicks[$hash] = $this->initClick($clickPrototype, $clickParams);
        }

        $click = $this->clicks[$hash];
        $context->debug('click type=' . $rawClick->getType());
        switch ($rawClick->getType()) {
            case Pap_Db_ClickImpression::STATUS_DECLINED:
                $click->addDeclined();
                break;
            case Pap_Db_ClickImpression::STATUS_UNIQUE:
                $click->addUnique();
            default:
                $click->addRaw();
                break;
        }
        $context->debug('Saving done');
	}

	protected function saveRawClick(Pap_Db_RawClick $rawClick) {
		Gpf_Log::debug('Calling save on raw click');
		$rawClick->save();
		Gpf_Log::debug('Saving done');
	}

	protected function fillClickParams(Pap_Db_ClickImpression $click, $clickParams) {
		foreach ($clickParams as $name => $value) {
			$click->set($name, $value);
		}
	}

	private function getClickParamsArray(Pap_Db_ClickImpression $click, Pap_Contexts_Click $context, Pap_Common_Banner $banner=null) {
	    $columns = array();
	    $columns[Pap_Db_Table_ClicksImpressions::ACCOUNTID] = $context->getAccountId();
        $columns[Pap_Db_Table_ClicksImpressions::USERID] = $context->getUserObject()->getId();
        $columns[Pap_Db_Table_ClicksImpressions::BANNERID] = $banner == null ? '' : $banner->getId();
        $columns[Pap_Db_Table_ClicksImpressions::PARENTBANNERID] = $banner == null ? '' : $banner->getParentBannerId();
        $columns[Pap_Db_Table_ClicksImpressions::CAMPAIGNID] = $context->getCampaignObject() == null ? '' : $context->getCampaignObject()->getId();
        $columns[Pap_Db_Table_ClicksImpressions::COUNTRYCODE] = $context->getCountryCode();
        $columns[Pap_Db_Table_ClicksImpressions::CDATA1] = $context->getExtraDataFromRequest(1);
        $columns[Pap_Db_Table_ClicksImpressions::CDATA2] = $context->getExtraDataFromRequest(2);
        $columns[Pap_Db_Table_ClicksImpressions::CHANNEL] = $this->getChannel($context);
        $timeNow = new Gpf_DateTime($context->getVisitDateTime());
        $columns[Pap_Db_Table_ClicksImpressions::DATEINSERTED] = $timeNow->format("Y-m-d H:00:00");
        return $columns;
	}
	
	protected function initClick(Pap_Db_ClickImpression $click, $clickParams) {
	    $this->fillClickParams($click, $clickParams);
		try {
			$click->loadFromData(array_keys($clickParams));
		} catch (Gpf_DbEngine_NoRowException $e) {
		} catch (Gpf_DbEngine_TooManyRowsException $e) {
		    $fixedClick = $this->fixTooManyRows($click->loadCollection(array_keys($clickParams)));
		    if (!is_null($fixedClick)) {
		        $click = $fixedClick;
		    }
		}
		return $click;
	}

	/**
	 * @param $clicks array<Pap_Db_ClickImpression>
	 * @return Pap_Db_ClickImpression
	 */
	private function fixTooManyRows(Gpf_DbEngine_Row_Collection $clicks) {
		if ($clicks->getSize() <= 0) {
			return null;
		}
		$first = true;
		foreach ($clicks as $click) {
			if ($first) {
				$firstClick = $click;
				$first = false;
				continue;
			}
			$firstClick->mergeWith($click);
			$click->delete();
		}
		$firstClick->save();
		return $firstClick;
	}
}

?>
