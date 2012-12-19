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

class Pap3Compatibility_Migration_OutputWriter extends Gpf_Object {
 	/**
 	 * reset (delete) all texts in session
 	 */
 	static public function reset() {
 		$_SESSION['pap3MigOutputTxts'] = array();
 	}
 	
 	/**
 	 * loads and outputs all old texts in the session
 	 */
 	static public function initialize() {
 		if(is_array($_SESSION['pap3MigOutputTxts'])) {
 			foreach($_SESSION['pap3MigOutputTxts'] as $txt) {
 				echo $txt;
 			}
 			flush();
 			echo ' ';
 		}
 	}

 	static public function log($message) {
   		$_SESSION['pap3MigOutputTxts'][] = $message;
   		Pap3Compatibility_Migration_OutputWriter::logNoHistory($message);
    }

 	static public function logOnce($message) {
    	if(!in_array($message, $_SESSION['pap3MigOutputTxts'])) {
    		Pap3Compatibility_Migration_OutputWriter::log($message);
    	}
    }
    
 	static public function logNoHistory($message) {
    	echo $message;
    	flush();
  	}
 	
  	static public function logDone($time1, $time2) {
  		Pap3Compatibility_Migration_OutputWriter::log("<span style=\"color:#009900; font-weight: bold;\">DONE</span> ".Pap3Compatibility_Migration_OutputWriter::timeDiff($time1, $time2)."s.<hr>");
  	}
  	
    static public function timeDiff($t1,$t2) {
    	if(($t1=="") || ($t2==""))
    	return null;

    	// now transform strings into numbers
    	sscanf($t1,"%f %d",$micros1,$sec1);
    	sscanf($t2,"%f %d",$micros2,$sec2);

    	$diff = (float) ($sec2-$sec1) + ($micros2-$micros1);
    	if($diff<0)
    	$diff *= -1.0;

    	return $diff;
    }  	
}
?>
