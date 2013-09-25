<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: InvoiceFormatForm.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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
class Pap_Merchants_Banner_BannersInfoData extends Pap_Common_Overview_OverviewBase {

	/**
	 *
	 * @service banner_stats read
	 * @param $data
	 */
	public function loadFullStatistics(Gpf_Rpc_Params $params) {
		$data = new Gpf_Rpc_Data($params);

		$select = new Gpf_SqlBuilder_SelectBuilder();
		$select->from->add(Pap_Db_Table_Banners::getName());
		$select->select->add("COUNT(".Pap_Db_Table_Banners::ID.")", "count");
		$select->select->add(Pap_Db_Table_Banners::TYPE, 'type');
		$select->groupBy->add(Pap_Db_Table_Banners::TYPE);
		
		Gpf_Plugins_Engine::extensionPoint('AffiliateNetwork.modifyWhere', 
        new Gpf_Common_SelectBuilderCompoundRecord($select, new Gpf_Data_Record(array(), array())));
		
        $result = $select->getAllRowsIndexedBy('type');
		$bannerTypes = explode(',', $data->getParam('bannerTypes'));		
		
		$bannersCount = 0;
		foreach ($bannerTypes as $bannerType) {
			$bannerTypeCount = 0;
			try {
				$bannerTypeCount = $result->getRecord($bannerType)->get('count');
			} catch (Gpf_Data_RecordSetNoRowException $e) {				
			}
			$data->setValue($bannerType, "$bannerTypeCount");
			$bannersCount += $bannerTypeCount;
		}
		$data->setValue("bannersCount", "$bannersCount");		

		return $data;
	}

	/**
	 *
	 * @service banner_stats read
	 * @param $data
	 */
	public function loadFilteredStatistics(Gpf_Rpc_Params $params) {
		$data = new Gpf_Rpc_Data($params);
		$statsParams = new Pap_Stats_Params();		
		$statsParams->initFrom($data->getFilters());				
		$impressions = new Pap_Stats_Impressions($statsParams);
		$clicks = new Pap_Stats_Clicks($statsParams);
		$sales = new Pap_Stats_Sales($statsParams);

		$data->setValue("impressions", $impressions->getCount()->getAll());
		$data->setValue("rawClicks", $clicks->getCount()->getRaw());
		$data->setValue("uniqueClicks", $clicks->getCount()->getUnique());
		$data->setValue("sales", $sales->getCount()->getApproved());
		$data->setValue("ctr", 0);
		if ($impressions->getCount()->getAll() !== 0) {
			$data->setValue("ctr", $clicks->getCount()->getAll() / ($impressions->getCount()->getAll() / 100));
		}

		return $data;
	}
}

?>
