<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * This class represents collection of Pap_Db_VisitorAffiliate objects
 *
 * @package PostAffiliatePro
 */
class Pap_Tracking_Common_VisitorAffiliateCollection extends Gpf_DbEngine_Row_Collection  {

    /**
     * @param compareFunction - Look at php.net - usort function
     */
    public function sort($compareFunction) {
        usort($this->rows, $compareFunction);
    }

    public function correctIndexes() {
        $this->rows = array_values($this->rows);
    }

    public function getValidSize() {
        $validSize = 0;
        $iterator = $this->getIterator();
        foreach ($iterator as $row) {
            if ($row->isValid()) {
                $validSize++;
            }
        }
        return $validSize;
    }

    /**
     * @return Pap_Db_VisitorAffiliate
     */
    public function getValid($i) {
        $position = 0;
        $iterator = $this->getIterator();
        foreach ($iterator as $row) {
            if (!$row->isValid()) {
                continue;
            }
            if ($position == $i) {
                return $row;
            }
            $position++;
        }
        return null;
    }

    /**
     * @return Pap_Db_VisitorAffiliate
     */
    public function getVisitorAffiliateByUserId($userId) {
        for($i = 0; $i < $this->getValidSize(); $i++) {
            $visitorAffiliate = $this->getValid($i);
            if($userId == $visitorAffiliate->getUserId()) {
                return $visitorAffiliate;
            }
        }
        throw new Gpf_Exception("No visitorAffiliate with userId '$userId' in visitorAffiliate collection");
    }
}
?>
