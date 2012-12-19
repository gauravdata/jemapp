<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric, Andrej Harsani, Miso Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Settings.class.php 18002 2008-05-13 18:31:39Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * class reads and writes DirectLinks settings to/from settings file
 * 
 * @package PostAffiliatePro
 */
class Pap_Tracking_DirectLinksBase extends Gpf_Object {
	const FILE_NAME = 'directlinks.php';
	const TEMPORARY_FILE_NAME = 'dl';
	private $file;
	private $temporaryFileName;
    private $temporaryFile; 
	private $parameters = array();
	private $settingsDirectory;
	
    /**
     * @var instance
     */
    static private $instance = array();

    private function __construct($accountId = null) {
    	if ($accountId == null) {
    		throw new Gpf_Exception("Account Id cannot be null");
    	}
    	$this->settingsDirectory = Gpf_Paths::getInstance()->getRealAccountConfigDirectoryPath();
    	$this->file = $this->settingsDirectory . self::FILE_NAME;
    }
    
    /**
     * returns instance of Gpf_Settings class
     *
     * @return Pap_Tracking_DirectLinksBase
     */
    public static function getInstance($accountSettings = true) {
        if ($accountSettings) {
            $accountId = Gpf_Session::getAuthUser()->getAccountId();
        } else {
            $accountId = null;
        }
        if (!array_key_exists($accountId, self::$instance)) {
            self::$instance[$accountId] = new Pap_Tracking_DirectLinksBase($accountId);
        }
        return self::$instance[$accountId];
    }
    
    public function regenerateDirectLinksFile() {
    	$rs = new Gpf_Data_RecordSet();
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('userid', 'userid');
        $selectBuilder->select->add('url', 'url');
        $selectBuilder->select->add('channelid', 'channelid');
        $selectBuilder->select->add('campaignid', 'campaignid');
        $selectBuilder->select->add('bannerid', 'bannerid');
        $selectBuilder->from->add(Pap_Db_Table_DirectLinkUrls::getName()); 
       	$selectBuilder->where->add('rstatus', '=', Pap_Common_Constants::STATUS_APPROVED);
		$selectBuilder->orderBy->add(Pap_Db_Table_DirectLinkUrls::MATCHES, false);

        $rs->load($selectBuilder);

    	$this->createTemporaryFile();

    	foreach($rs as $record) {
    		$this->addUrl($record->get('userid'), $record->get('url'),
    						$record->get('channelid'),
    						$record->get('campaignid'),
    						$record->get('bannerid'));
    	}
    	$this->commitTemporaryFile();
    }
        
    public function createTemporaryFile() {
    	$this->temporaryFileName = $this->settingsDirectory . self::TEMPORARY_FILE_NAME . '_' . md5(uniqid(rand(), true));
   	
    	$this->temporaryFile = new Gpf_Io_File($this->temporaryFileName);
    	$this->temporaryFile->open('w');
    	$this->temporaryFile->write("<?php /**\r\n");
    }
    
    public function addUrl($userId, $url, $channelid, $campaignid, $bannerid) {
    	$matchingString = $this->getMatchingString($url);
    	$pregUrl = $this->transformToPreg($url);
    	
    	$this->temporaryFile->write("$userId|$matchingString|$url|$pregUrl|$channelid|$campaignid|$bannerid\r\n");
    }
    
	public function transformToPreg($str) {
	    $str = str_replace('https://', '', $str);
        $str = str_replace('http://', '', $str);
	    
		$str = str_replace('/', '\/', $str);
		$str = str_replace('.', '\.', $str);
		$str = str_replace(',', '\,', $str);
		$str = str_replace(';', '\;', $str);
		$str = str_replace('-', '\-', $str);
		$str = str_replace('=', '\=', $str);
		$str = str_replace('&', '\&', $str);
		$str = str_replace('?', '\?', $str);
		$str = str_replace('^', '\^', $str);
		$str = str_replace('$', '\$', $str);
		$str = str_replace('[', '\[', $str);
		$str = str_replace(']', '\]', $str);
		$str = str_replace('|', '\|', $str);
		$str = str_replace('(', '\(', $str);
		$str = str_replace(')', '\)', $str);
		$str = str_replace('{', '\{', $str);
		$str = str_replace('}', '\}', $str);
		//$str = str_replace("\\", "\\\\", $str);
		
		return '/^' . str_replace('*', '(.*?)', $str) . '$/iU';
	}
	
    /**
     * finds some matching string to quick match referrer with this URL
     *
     * @param unknown_type $url
     */
    private function getMatchingString($url) {
    	$arr = explode('*', $url);
    	 
    	if(!is_array($arr) || count($arr) <1) {
    		return '';
    	}
    	
    	if(count($arr) == 1) {
    		return $arr[0];
    	}
    	
    	$longestPart = '';
    	$longestPartLength = 0;
    	
    	foreach($arr as $part) {
    		$partLength = strlen($part);
    		if($partLength > $longestPartLength) {
    			$longestPart = $part;
    			$longestPartLength = $partLength;
    		}
    	}
  	
    	return $longestPart;
    }
    
    public function commitTemporaryFile() {
    	$this->temporaryFile->write("*/ ?>");
    	
    	$this->temporaryFile->close();
    	
    	$realFileName = $this->settingsDirectory . self::FILE_NAME;
    	if(file_exists($realFileName)) {
    		unlink($realFileName);
    	}
    	rename($this->temporaryFileName, $realFileName);
    }
    
    /**
     * check if the given URL matches some of the approved DirectLink URLs. 
     * If yes, return userID to whom this URL belongs 
     */
    public function checkDirectLinkMatch($url) {
    	$realFileName = $this->settingsDirectory . self::FILE_NAME;
    	if(!file_exists($realFileName)) {
    		throw new Gpf_Exception("DirectLink config file '$realFileName' does not exist!");
    	}

    	$url = str_replace('https://', '', $url);
    	$url = str_replace('http://', '', $url);
    	
    	$file = new Gpf_Io_File($realFileName);
    	$file->open();
        while (!$file->isEof()) {
        	$buffer = $file->readLine(4065);
        	if($buffer[0] == '<' || $buffer[0] == '?') {
        		continue;
        	}

        	$buffer = trim($buffer);
        	$arr = explode('|', $buffer, 7);
        	if(!is_array($arr) || count($arr) != 7) {
        		continue; 
        	}
        	
        	$userid = $arr[0];
        	$substring = $arr[1];
        	$clearUrl = $arr[2];
        	$preg = $arr[3];
        	$channelid = $arr[4];
        	$campaignid = $arr[5];
        	$bannerid = $arr[6];
        	if($this->isMatch($url, $substring, $preg)) {
        		$file->close();
        		return array('userid' => $userid, 
        					 'url' => $clearUrl,
        					 'channelid' => $channelid,
        					 'campaignid' => $campaignid,
        		     		 'bannerid' => $bannerid,
        		);
        	}
        }
        
        $file->close();
        return false;
    }
    	
    public function isMatch($url, $substring, $preg) {
    	$count = preg_match($preg, $url);
    	if($count > 0) {
    		return true;
    	}
    	
    	return false;
    }
}

?>
