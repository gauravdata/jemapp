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
class Pap_Merchants_User_UserDirectLinksGrid extends Pap_Common_User_DirectLinksGridBase {

    function __construct() {
        parent::__construct();
    }

    protected function initDefaultView() {
    	$this->addDefaultViewColumn(Pap_Db_Table_DirectLinkUrls::URL, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_DirectLinkUrls::STATUS, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_DirectLinkUrls::NOTE, '', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '', 'N');
    }

    protected function buildWhere() {
        parent::buildWhere();
        $this->_selectBuilder->where->add("l.".Pap_Db_Table_DirectLinkUrls::USER_ID, '=', $this->getUserId());
    }
    
    /**
     * @service direct_link read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    private function getUserId() {
        if ($this->_params->exists('userid')) {
            return $this->_params->get('userid');
        }
        throw new Gpf_Exception($this->_('Missing userid'));
    }
}
?>
