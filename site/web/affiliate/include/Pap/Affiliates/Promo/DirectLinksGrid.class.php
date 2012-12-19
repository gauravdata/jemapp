<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro 
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AffiliatesGrid.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Affiliates_Promo_DirectLinksGrid extends Pap_Common_User_DirectLinksGridBase {

    function __construct() {
        parent::__construct();
    }
    	
    protected function buildWhere() {
        parent::buildWhere();
        $this->_selectBuilder->where->add("l.userid", "=", Gpf_Session::getAuthUser()->getPapUserId());
    }  
    
    /**
     * @service direct_link read_own
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
}
?>
