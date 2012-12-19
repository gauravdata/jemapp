<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
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

class Pap3Compatibility_Migration_TaskBanners extends Gpf_Object {
    
    public function run() {
    	Pap3Compatibility_Migration_OutputWriter::log("Migrating banners<br/>");
    	$time1 = microtime();
    	
    	try {
    		$this->migrateBanners();
    	} catch(Exception $e) {
    		Pap3Compatibility_Migration_OutputWriter::log("&nbsp;&nbsp;Errror: ".$e->getMessage()."<br/>");
    	}
    	
    	$time2 = microtime();
		Pap3Compatibility_Migration_OutputWriter::logDone($time1, $time2);
    }

    protected function migrateBanners() {
    	Pap3Compatibility_Migration_OutputWriter::log("&nbsp;&nbsp;Migrating banners.....");
    	
    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('*');
        $selectBuilder->from->add('wd_pa_banners', 'b');
        $selectBuilder->where->add('b.deleted', '=', '0' );

        $result = $selectBuilder->getAllRows();

        $count = 0;
        foreach($result as $record) {
        	$obj = new Pap_Common_Banner();
        	$obj->setId($record->get('bannerid'));
        	$obj->setCampaignId($record->get('campaignid'));
        	$obj->setAccountId(Pap3Compatibility_Migration_Pap3Constants::DEFAULT_ACCOUNT_ID);
        	if($record->get('destinationurl') != '') {
        		$obj->setDestinationUrl($record->get('destinationurl'));
        	} else {
        		$obj->setDestinationUrl(' ');
        	}
        	$obj->setName($record->get('name'));
        	if($record->get('dateinserted') != '') {
        		$obj->set('dateinserted', $record->get('dateinserted'));
        	} else {
        		$obj->set('dateinserted', Gpf_Common_DateUtils::now());
        	}
        	$obj->setStatus(($record->get('hidden') == 1 ? 'H' : 'A'));
        	
        	$bannerType = Pap3Compatibility_Migration_Pap3Constants::translateBannerType($record->get('bannertype'));
        	switch($bannerType) {
        		case Pap_Common_Banner_Factory::BannerTypeText:
        			$obj->setData1($record->get('sourceurl'));
        			$obj->setData2($this->replaceVariables($record->get('description')));
        			break;
        		case Pap_Common_Banner_Factory::BannerTypeImage:
        			$obj->setData1($record->get('sourceurl'));
        			break;
        		case Pap_Common_Banner_Factory::BannerTypeHtml:
        			$obj->setData1('N');
        			$obj->setData2($this->replaceVariables($record->get('description')));
        			break;
        		case Pap_Common_Banner_Factory::BannerTypePopup:
        		case Pap_Common_Banner_Factory::BannerTypePopunder:
        			// these banners are not yet supported in PAP4
        			continue;
        		case Pap_Common_Banner_Factory::BannerTypePromoEmail:
        			$obj->setData1($record->get('sourceurl'));
        			$obj->setData2($this->replaceVariables($record->get('description')));
       			break;
        	}
        	$obj->setBannerType($bannerType);
        	
        	try {
        	   $obj->save();
        	} catch (Gpf_Exception $e) {
        	    Pap3Compatibility_Migration_OutputWriter::log(
        	       sprintf(' Warning: banner %s not migrated. Reason: %s', $obj->getId(), $e->getMessage()));
        	}
        	$count++;
        }
    	Pap3Compatibility_Migration_OutputWriter::log(" ($count) ..... DONE<br/>");
    }
    
    private function replaceVariables($text) {
        return str_replace(array('$CLICKURL_NOTENCODED', '$CLICKURL'), array('{$targeturl_encoded}','{$targeturl}'), $text);
    }
}
?>
