<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak, Maros Galik
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Pap_Tracking_Impression_ImpressionProcessor extends Gpf_Tasks_LongTask {
	
	const ROWS_FOR_PROCESSING_LIMIT = 100000;
	
	/**
	 * @var Gpf_Log_Logger
	 */
	private $logger;
	/**
	 * @var Pap_Tracking_Impression_Save
	 */
	private $impressionSaver;
	/**
	 * @var Pap_Common_Banner_Factory
	 */
	private $bannerFactory;

	private $bannerCache = array();
	private $affiliateCache = array();
	private $channelCache = array();
	private $campaignCache = array();
	private $processedTableIndex;

	public function __construct() {
		$this->logger = Pap_Logger::create(Pap_Common_Constants::TYPE_CPM);
		$this->impressionSaver = new Pap_Tracking_Impression_Save();
		$this->bannerFactory = new Pap_Common_Banner_Factory();
	}

	public function getName() {
		return $this->_('Impression processor');
	}
	
	public function runOnline(Pap_Db_RawImpression $impression) {
	    $this->importImpression($impression);
	}

	protected function execute() {
		$this->debug('Starting impression preocessor');

		$this->switchTables();

		$this->processedTableIndex = Gpf_Settings::get(Pap_Settings::IMPRESSIONS_TABLE_PROCESS);

		$this->processAllImpressions();

        $this->debug('All impressions from '.Pap_Db_Table_RawImpressions::getName($this->processedTableIndex).'. processed');

		$this->interrupt(30);
	}
	
	private function processAllImpressions() {
		$subSelect = $this->createSubSelect();
		$impressionsSelect = $this->getAllImpressions($subSelect);				
		$this->processImpressions($impressionsSelect, $subSelect);
	}
	
	protected function processImpressions(Gpf_SqlBuilder_SelectBuilder $impressionsSelect, Gpf_SqlBuilder_SelectBuilder $subSelect) {			
		$iterator = $this->getImpressions($impressionsSelect);		

		$count = 0;	
		foreach ($iterator as $impressionRecord) {
            $impression = new Pap_Db_RawImpression($this->processedTableIndex);
            $impression->fillFromRecord($impressionRecord);
            $this->importImpression($impression, $impressionRecord->get('count'));
            $count++;
        }        
     
        if ($count > 0) {        	
        	$this->deleteImpressions($subSelect);      	
        	$this->checkInterruption();
        	$this->processImpressions($impressionsSelect, $subSelect);
        }
	}
	
    protected function checkInterruption() {
        if($this->isTimeToInterrupt()) {
            $this->interrupt(30);
        }
    }

	/**
	 * @param $selectBuilder
	 * @return Gpf_SqlBuilder_SelectIterator
	 */
	protected function getImpressions(Gpf_SqlBuilder_SelectBuilder $selectBuilder) {					
		return $selectBuilder->getAllRowsIterator();
	}

	protected function deleteImpressions(Gpf_SqlBuilder_SelectBuilder $subSelect) {
		$delete = new Gpf_SqlBuilder_DeleteBuilder();
		$delete->delete->add('ri');
		$delete->from->add(Pap_Db_Table_RawImpressions::getName($this->processedTableIndex), 'ri');
		$delete->from->addInnerJoin('(' . $subSelect->toString() . ')', 'sri', 
		'ri.'.Pap_Db_Table_RawImpressions::ID.'=sri.'.Pap_Db_Table_RawImpressions::ID);
		$delete->delete();
	}	

	/**
	 * switching tables for writing and processing impressions
	 * table states: I - impressions are written to this table
	 *               W - table is waiting to be processed
	 *               P - table should be processed
	 *
	 * this method switches: I -> W, W -> P, P -> I
	 */
	private function switchTables() {
		Gpf_Settings::set(Pap_Settings::IMPRESSIONS_TABLE_INPUT,
		  (Gpf_Settings::get(Pap_Settings::IMPRESSIONS_TABLE_INPUT)+2) % 3);
        Gpf_Settings::set(Pap_Settings::IMPRESSIONS_TABLE_PROCESS,
          (Gpf_Settings::get(Pap_Settings::IMPRESSIONS_TABLE_PROCESS)+2) % 3);
	}

	/**
	 * @return Gpf_SqlBuilder_SelectBuilder
	 */
	private function getAllImpressions(Gpf_SqlBuilder_SelectBuilder $subSelect) {
		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();		
		$selectBuilder->select->add('ri.date', 'date');
		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::RTYPE, Pap_Db_Table_RawImpressions::RTYPE);
		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::USERID, Pap_Db_Table_RawImpressions::USERID);
		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::BANNERID, Pap_Db_Table_RawImpressions::BANNERID);
		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::PARENTBANNERID, Pap_Db_Table_RawImpressions::PARENTBANNERID);
		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::CHANNEL, Pap_Db_Table_RawImpressions::CHANNEL);
		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::IP, Pap_Db_Table_RawImpressions::IP);
		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::DATA1, Pap_Db_Table_RawImpressions::DATA1);
		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::DATA2, Pap_Db_Table_RawImpressions::DATA2);		
		$selectBuilder->select->add('count(ri.'.Pap_Db_Table_RawImpressions::ID.')', 'count');
		
		$selectBuilder->from->addSubselect($subSelect, 'ri');
		
		$selectBuilder->groupBy->add('DATE_FORMAT('.Pap_Db_Table_RawImpressions::DATE.', "%Y-%m-%d %H:00:00")', 'date');
		$selectBuilder->groupBy->add(Pap_Db_Table_RawImpressions::RTYPE);
		$selectBuilder->groupBy->add(Pap_Db_Table_RawImpressions::USERID);
		$selectBuilder->groupBy->add(Pap_Db_Table_RawImpressions::BANNERID);
		$selectBuilder->groupBy->add(Pap_Db_Table_RawImpressions::CHANNEL);
		$selectBuilder->groupBy->add(Pap_Db_Table_RawImpressions::DATA1);
		$selectBuilder->groupBy->add(Pap_Db_Table_RawImpressions::DATA2);

		Gpf_Plugins_Engine::extensionPoint('Tracker.ImpressionProcessor.getAllImpressions', $selectBuilder);
		
		return $selectBuilder;
	}

	/**	 
	 * @return Gpf_SqlBuilder_SelectBuilder
	 */
	private function createSubSelect() {
		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
		$selectBuilder->from->add(Pap_Db_Table_RawImpressions::getName($this->processedTableIndex));		
		$dateColumn = 'DATE_FORMAT('.Pap_Db_Table_RawImpressions::DATE.', "%Y-%m-%d %H:00:00")';
		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::ID);
		$selectBuilder->select->add($dateColumn, 'date');
		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::RTYPE);
		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::USERID);
		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::BANNERID);
		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::PARENTBANNERID);
		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::CHANNEL);
		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::IP);
		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::DATA1);
		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::DATA2);
		$selectBuilder->limit->set(0, self::ROWS_FOR_PROCESSING_LIMIT);
		return $selectBuilder;
	}
	
	/**
	 * @return Pap_Affiliates_User
	 * @throws Gpf_Exception
	 */
	protected function getUser($affiliateId) {
		if (!isset($this->affiliateCache[$affiliateId])) {
			$this->affiliateCache[$affiliateId] = Pap_Affiliates_User::loadFromId($affiliateId);
		}

		return $this->affiliateCache[$affiliateId];
	}

	/**
	 * @throws Gpf_Exception
	 * @return Pap_Common_Banner
	 * 
	 * do not change public to protected because of compatibility reasons with PAP3(sb.php) 
	 */
	public function getBanner($bannerId) {
		if (!isset($this->bannerCache[$bannerId])) {
			$this->bannerCache[$bannerId] = $this->bannerFactory->getBanner($bannerId);
		}
		return $this->bannerCache[$bannerId];
	}
	
    /**
     * @throws Gpf_Exception
     * @return Pap_Common_Campaign
     */
    protected function getCampaign($campaignId) {
        if (!isset($this->campaignCache[$campaignId])) {
            $campaign = new Pap_Common_Campaign();
            $campaign->setId($campaignId);
            $campaign->load();
            $this->campaignCache[$campaignId] = $campaign;
        }
        return $this->campaignCache[$campaignId];
    }

	/**
	 * @return Pap_Db_Channel
	 * @throws Gpf_Exception
	 */
	protected function getChannel($channelId, $impressionContext) {
		if (!isset($this->channelCache[$channelId])) {
			$recognizeChannel = new Pap_Tracking_Impression_RecognizeChannel();
			$this->channelCache[$channelId] = $recognizeChannel->getChannelById($impressionContext, $channelId);
		}
		return $this->channelCache[$channelId];
	}

	protected function importImpression(Pap_Db_RawImpression $impression, $count = 1) {
		$impressionContext = new Pap_Contexts_Impression($impression);
	    
	    try {
			$impressionContext->setUserObject($this->getUser($impression->getUserId()));
		} catch (Gpf_Exception $e) {
			$this->debug('Invalid user '.$impression->getUserId().'. Skipping');
			return;
		}

		try {
			$impressionContext->setBannerObject($this->getBanner($impression->getBannerId()));
		} catch (Gpf_Exception $e) {
			$this->debug('Invalid banner '.$impression->getBannerId().'. Skipping');
			return;
		}
		
		try {
		    $campaign = $this->getCampaign($impressionContext->getBannerObject()->getCampaignId());
		    $impressionContext->setAccountId($campaign->getAccountID(), Pap_Contexts_Tracking::ACCOUNT_RECOGNIZED_FROM_CAMPAIGN);
		} catch (Gpf_Exception $e) {
		    $this->debug('Invalid campaign '.$impressionContext->getBannerObject()->getCampaignId().'. Skipping');
		    return;
		}

		try {
			$impressionContext->setChannelObject($this->getChannel($impression->getChannel(), $impressionContext));
		} catch (Gpf_Exception $e) {
		}
		
		$impressionContext->setCount($count);
		$this->saveImpression($impressionContext);		
	}
	
	protected function saveImpression(Pap_Contexts_Impression $impressionContext) {
		$this->impressionSaver->save($impressionContext);
	}

	protected function loadTask() {
		$this->task->setClassName(get_class($this));
		$this->task->setNull(Gpf_Db_Table_Tasks::DATEFINISHED);
		$this->task->loadFromData();
	}
}
?>
