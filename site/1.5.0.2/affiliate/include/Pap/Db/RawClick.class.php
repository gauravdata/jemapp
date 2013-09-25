<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: RawClick.class.php 32971 2011-06-01 13:36:24Z mkendera $
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
class Pap_Db_RawClick extends Gpf_DbEngine_Row {

	const RAW = "R";
	const UNIQUE = "U";
	const DECLINED = "D";

	const PROCESSED = 'P';

	function __construct(){
		parent::__construct();
	}

	function init() {
		$this->setTable(Pap_Db_Table_RawClicks::getInstance());
		parent::init();
	}

	public function getId() {
	    return $this->get(Pap_Db_Table_RawClicks::ID);
    }

    public function setId($value) {
        $this->set(Pap_Db_Table_RawClicks::ID, $value);
	}
	
    public function getCountryCode() {
        return $this->get(Pap_Db_Table_RawClicks::COUNTRYCODE);
    }

    public function setCountryCode($countryCode) {
        $this->set(Pap_Db_Table_RawClicks::COUNTRYCODE, $countryCode);
    }

	public function getBannerId() {
	    return $this->get(Pap_Db_Table_RawClicks::BANNERID);
	}

    public function getUserId() {
        return $this->get(Pap_Db_Table_RawClicks::USERID);
    }

	public function setUserId($id) {
		$this->set(Pap_Db_Table_RawClicks::USERID, $id);
	}

    public function getCampaignId() {
        return $this->get(Pap_Db_Table_RawClicks::CAMPAIGNID);
    }

	public function setCampaignId($id) {
		$this->set(Pap_Db_Table_RawClicks::CAMPAIGNID, $id);
	}

	public function setBannerId($id) {
		$this->set(Pap_Db_Table_RawClicks::BANNERID, $id);
	}

	public function setParentBannerId($id) {
		$this->set(Pap_Db_Table_RawClicks::PARENTBANNERID, $id);
	}

    public function getParentBannerId() {
        return $this->get(Pap_Db_Table_RawClicks::PARENTBANNERID);
    }

	public function setData1($value) {
		$this->set(Pap_Db_Table_RawClicks::DATA1, $value);
	}

    public function getData1() {
        return $this->get(Pap_Db_Table_RawClicks::DATA1);
    }

	public function setData2($value) {
		$this->set(Pap_Db_Table_RawClicks::DATA2, $value);
	}

    public function getData2() {
        return $this->get(Pap_Db_Table_RawClicks::DATA2);
    }

    public function setChannel($value) {
		$this->set(Pap_Db_Table_RawClicks::CHANNEL, $value);
	}

    public function getChannel() {
		return $this->get(Pap_Db_Table_RawClicks::CHANNEL);
    }

	public function setType($value) {
		$this->set(Pap_Db_Table_RawClicks::RTYPE, $value);
	}

    public function getType() {
        return $this->get(Pap_Db_Table_RawClicks::RTYPE);
    }

	public function setDateTime($value) {
		$this->set(Pap_Db_Table_RawClicks::DATETIME, $value);
	}

    public function getDateTime() {
        return $this->get(Pap_Db_Table_RawClicks::DATETIME);
    }

    public function getDateTimestamp() {
        return strtotime($this->get(Pap_Db_Table_RawClicks::DATETIME));
    }

	public function setRefererUrl($value) {
		$this->set(Pap_Db_Table_RawClicks::REFERERURL, substr($value, 0, 250));
	}

    public function getRefererUrl() {
        return $this->get(Pap_Db_Table_RawClicks::REFERERURL);
    }

	public function setIp($value) {
		$this->set(Pap_Db_Table_RawClicks::IP, $value);
	}

    public function getIp() {
        return $this->get(Pap_Db_Table_RawClicks::IP);
    }

	public function setBrowser($value) {
		$this->set(Pap_Db_Table_RawClicks::BROWSER, $value);
	}

    public function getBrowser() {
        return $this->get(Pap_Db_Table_RawClicks::BROWSER);
    }

	/**
	 * @param boolean $unique
	 */
	public function setUnique($unique) {
		if ($unique) {
			$this->setType(self::UNIQUE);
		} else {
			$this->setType(self::RAW);
		}
	}

	/**
	 * @param boolean $unique
	 */
	public function setProcessedStatus($status) {
		if ($status) {
			$this->set(Pap_Db_Table_RawClicks::RSTATUS, self::PROCESSED);
		} else {
			$this->set(Pap_Db_Table_RawClicks::RSTATUS, null);
		}
	}

	/**
	 * Get summary of clicks
	 *
	 * @param Gpf_SqlBuilder_WhereClause $whereRawClicks
	 * @return array with count of raw, unique and declined clicks
	 */
	public static function getClickCounts(Gpf_SqlBuilder_WhereClause $whereRawClicks) {
		$select = new Gpf_SqlBuilder_SelectBuilder();
        $select->from->add(Pap_Db_Table_RawClicks::getName(), "rc");
        $select->select->add("SUM(IF(".Pap_Db_Table_RawClicks::RTYPE."='R',1,0))", "raw");
        $select->select->add("SUM(IF(".Pap_Db_Table_RawClicks::RTYPE."='U',1,0))", "uniq");
        $select->select->add("SUM(IF(".Pap_Db_Table_RawClicks::RTYPE."='D',1,0))", "declined");
        $select->where = $whereRawClicks;
        $select->where->add(Pap_Db_Table_RawClicks::RSTATUS, "=", null);
        $clicks = $select->getOneRow();
        $clicks = array("raw" => $clicks->get("raw"), "unique" => $clicks->get("uniq"), "declined" => $clicks->get("declined"));

        return $clicks;
	}

    public function getNumberOfClicksFromSameIP($ip, $periodInSeconds, $bannerId = false, $dateCreated) {
        $select = new Gpf_SqlBuilder_SelectBuilder();

        $select->select->add("count(clickid)", "count");
        $select->from->add(Pap_Db_Table_RawClicks::getName());
        $select->where->add(Pap_Db_Table_RawClicks::IP, "=", $ip);
        if ($bannerId !== false) {
            $select->where->add(Pap_Db_Table_RawClicks::BANNERID, "=", $bannerId);
        }
        $select->where->add(Pap_Db_Table_RawClicks::RTYPE, "<>", Pap_Db_ClickImpression::STATUS_DECLINED);
        $dateFrom = new Gpf_DateTime($dateCreated);
        $dateFrom->addSecond(-1*$periodInSeconds);
        $select->where->add(Pap_Db_Table_RawClicks::DATETIME, ">", $dateFrom->toDateTime());

        $recordSet = new Gpf_Data_RecordSet();
        $recordSet->load($select);

        foreach($recordSet as $record) {
        	return $record->get("count");
        }
        return 0;
    }

    /**
     * returns latest undeclined click from the given IP address
     *
     * @param string $ip
     * @param int $periodInSeconds
     * @return unknown
     */
    public function getLatestClickFromIP($ip, $periodInSeconds) {
        $select = new Gpf_SqlBuilder_SelectBuilder();

        $select->select->add(Pap_Db_Table_RawClicks::USERID, "userid");
        $select->select->add(Pap_Db_Table_RawClicks::CHANNEL, "channel");
        $select->from->add(Pap_Db_Table_RawClicks::getName());
        $select->where->add(Pap_Db_Table_RawClicks::IP, "=", $ip);
        $select->where->add(Pap_Db_Table_RawClicks::RTYPE, "<>", Pap_Db_ClickImpression::STATUS_DECLINED);
        $dateFrom = new Gpf_DateTime();
        $dateFrom->addSecond(-1*$periodInSeconds);
        $select->where->add(Pap_Db_Table_RawClicks::DATETIME, ">", $dateFrom->toDateTime());
        $select->orderBy->add(Pap_Db_Table_RawClicks::DATETIME, false);
        $select->limit->set(0, 1);

        $recordSet = new Gpf_Data_RecordSet();
        $recordSet->load($select);

        foreach($recordSet as $record) {
        	return array('userid' => $record->get("userid"), 'channel' => $record->get("channel"));
        }
        return null;
    }
}

?>
