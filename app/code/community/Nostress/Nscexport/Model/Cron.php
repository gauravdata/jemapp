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
* @copyright Copyright (c) 2012 NoStress Commerce (http://www.nostresscommerce.cz) 
* 
*/ 

/** 
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

class Nostress_Nscexport_Model_Cron extends Nostress_Nscexport_Model_Abstract
{   	
	const TIME_MAX = "23:59:59";
	const TIME_MIN = "00:00:00";
	
	const TIME_FROM = 'time_from';
	const TIME_TO = 'time_to';
	const DAY_OF_WEEK = 'dow'; 
	
	const COLUMN_EXPORT_ID = 'export_id';
	const COLUMN_DAY_OF_WEEK = 'day_of_week';
	const COLUMN_TIME = 'time';
	
	public function _construct() 
	{
		parent::_construct ();
		$this->_init ('nscexport/cron');
	}
	
	public function getScheduledProfiles()
	{
		$intervals = $this->getScheduledIntervals();
		
		$collection = $this->getCollection();
		$select = $collection->getSelect();
		
		$whereSql = "";
		foreach ($intervals as $interval)
		{
			if(!empty($whereSql))
				$whereSql .= "OR ";
			$whereSql .= "(time >= '{$interval[self::TIME_FROM]}' AND time <= '{$interval[self::TIME_TO]}' AND day_of_week = '{$interval[self::DAY_OF_WEEK]}') ";
		}
		$select->where($whereSql);
		$select->group("export_id");
		
//		echo $select->__toString();
//		exit();
		
		$collection->load();
		$profileIds = array();
		foreach ($collection as $record)
		{
			$profileIds[] = $record->getExportId();
		}
		return $profileIds;
	}
	
	public function getDaysPerProfile($exportId)
	{
		return $this->getColumPerProfile($exportId,"day_of_week");
	}
	
	public function getTimesPerProfile($exportId)
	{
		$times = $this->getColumPerProfile($exportId,"time");
		foreach($times as $index => $time)
		{
			$times[$index] = substr($time,0,strrpos($time,":"));			
		}		
		return $times;
	}
	
	public function deleteRecords($exportId,$days,$times)
	{
		$records = $this->prepereRecords($exportId,$days,$times);
		$this->getResource()->deleteRecords($records);
	}
	
	public function addRecords($exportId,$days,$times)
	{
		$records = $this->prepereRecords($exportId,$days,$times);
		$this->getResource()->insertRecords($records);
	}
	
	protected function getColumPerProfile($exportId,$columnName)
	{
		$collection = $this->getCollection();
		$collection->addFieldToFilter("export_id",$exportId);
		$collection->addFieldToSelect($columnName);
		$collection->getSelect()->group($columnName);
		$collection->load();
		
		$result = array();
		foreach ($collection as $item) 
		{
			$result[] = $item->getData($columnName);			
		}
		return $result;
	}
	
	protected function prepereRecords($exportId,$days,$times)
	{
		$records = array();
		foreach ($days as $day)
		{
			foreach ($times as $time)
			{
				$records[] = array(self::COLUMN_EXPORT_ID => $exportId,self::COLUMN_DAY_OF_WEEK => $day,self::COLUMN_TIME => $time);
			}
		}
		return $records;
	}
	
	/**
	 * Prepare interval(s) from last cron run. 
	 */
	protected function getScheduledIntervals()
	{
		$currentDateTime = $this->helper()->getDateTime(null,true);
		$dayOfWeek = $this->helper()->getDayOfWeek($currentDateTime);
		
		$timeFormated = $this->helper()->getTime(null,true);
		$time = $this->helper()->getTime();
		//$lastRunTimeFormated = "23:10:59";
		$lastRunTimeFormated = $this->helper()->getGeneralConfig(Nostress_Nscexport_Helper_Data::PARAM_CRON_LAST_RUN);
		
		$this->helper()->setGeneralConfig(Nostress_Nscexport_Helper_Data::PARAM_CRON_LAST_RUN,$timeFormated);		         
		$lastRunTime = strtotime($lastRunTimeFormated);
		$lastRunDayOfWeek = $dayOfWeek;
		
		if($lastRunTime > $time)
		{
			$lastRunDayOfWeek--;
			if($lastRunDayOfWeek < Nostress_Nscexport_Model_Config_Source_Dayofweek::MONDAY)
				$lastRunDayOfWeek = Nostress_Nscexport_Model_Config_Source_Dayofweek::SUNDAY;
		}
		
		$intervals = array();
		if($lastRunDayOfWeek == $dayOfWeek)
		{
			$intervals[] = array(self::TIME_FROM => $lastRunTimeFormated,self::TIME_TO => $timeFormated,self::DAY_OF_WEEK => $dayOfWeek);
		}
		else 
		{
			$intervals[] = array(self::TIME_FROM => $lastRunTimeFormated,self::TIME_TO => self::TIME_MAX,self::DAY_OF_WEEK => $lastRunDayOfWeek);
			$intervals[] = array(self::TIME_FROM => self::TIME_MIN,self::TIME_TO => $timeFormated,self::DAY_OF_WEEK => $dayOfWeek);
		}					
		return $intervals;
	}
}
?>