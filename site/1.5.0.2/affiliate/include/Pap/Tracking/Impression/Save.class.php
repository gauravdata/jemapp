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
class Pap_Tracking_Impression_Save extends Gpf_Object {

    public function save(Pap_Contexts_Impression $context) {
        $context->debug('  Saving impression started');

        $impression = $this->createRowImpression();
        $impression->setAccountId($context->getAccountId());
        $impression->setUserId($context->getUserObject()->getId());
        $impression->setBannerId($context->getBannerId());
        $impression->setParentBannerId($context->getParentBannerId());
        $impression->setCampaignId($context->getCampaignId());
        $impression->setChannel($context->getChannelId());
        $impression->setCountryCode($context->getCountryCode());
        $impression->setData1($context->getClickData1());
        $impression->setData2($context->getClickData2());
        $time = new Gpf_DateTime($context->getDate());
        $impression->setTime($time->getHourStart()->toDateTime());

        try {
        	$this->saveAndIncrementImpressionCount($context, $impression);
        } catch (Gpf_Exception $e) {
        	$context->debug($this->_('Saving impression interrupted: %s', $e->getMessage()));
        }
         
        $context->debug('  Saving impression ended');
    }

    protected function createRowImpression() {
        return new Pap_Db_Impression();
    }

    private function saveAndIncrementImpressionCount(Pap_Contexts_Impression $context, Pap_Db_Impression $impression) {
        $impression = $this->getImpression($impression);

        if ($context->isUnique()) {
            $context->debug('    Impression is unique');
            $impression->addUniqueCount($context->getCount());
        } else {
            $context->debug('    Impression is not unique');
        }

        $impression->addRawCount($context->getCount());
         
        $impression->save();

        Gpf_Plugins_Engine::extensionPoint('Tracker.impression.afterSave', $context);
    }

    /**
     * @return Pap_Db_Impression
     */
    private function getImpression(Pap_Db_Impression $impression) {
        // we have to explicitly set all columns, otherwise it does not behave correctly,
        // because it is ommitting columns with empty or null value
        $impressionsCollection = $impression->loadCollection(array(Pap_Db_Table_ClicksImpressions::USERID,
        Pap_Db_Table_ClicksImpressions::ACCOUNTID,
        Pap_Db_Table_ClicksImpressions::BANNERID,
        Pap_Db_Table_ClicksImpressions::PARENTBANNERID,
        Pap_Db_Table_ClicksImpressions::CAMPAIGNID,
        Pap_Db_Table_ClicksImpressions::COUNTRYCODE,
        Pap_Db_Table_ClicksImpressions::CDATA1,
        Pap_Db_Table_ClicksImpressions::CDATA2,
        Pap_Db_Table_ClicksImpressions::CHANNEL,
        Pap_Db_Table_ClicksImpressions::DATEINSERTED));

        if ($impressionsCollection->getSize() == 0) {
            return $impression;
        }

        if ($impressionsCollection->getSize() == 1) {
            return $impressionsCollection->get(0);
        }
        
        $firstImpression = $impressionsCollection->get(0);
        for ($i=1; $i<$impressionsCollection->getSize(); $i++) {
            $impression = $impressionsCollection->get($i);
            $firstImpression->addRawCount($impression->getRaw());
            $firstImpression->addUniqueCount($impression->getUnique());
            $impression->delete();
        }
        
        return $firstImpression;
    }
}

?>
