<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Galik
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
class Pap_Features_AffiliateTrackingCode_CodesForm extends Gpf_View_FormService {       
    /**
     * @return Pap_Db_AffiliateTrackingCode
     */
    public function createDbRowObject() {
        return new Pap_Db_AffiliateTrackingCode();
    }
    
    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_('Affiliate tracking code');
    }
}
?>
