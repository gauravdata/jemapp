<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Channels.class.php 18660 2008-06-19 15:30:59Z aharsani $
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
class Pap_Merchants_Campaign_AffChannelSearchRichListBox extends Pap_Merchants_Campaign_ChannelSearchRichListBox {
    
	/**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function createSelectBuilder() {
    	$selectBuilder = parent::createSelectBuilder();
        if ($this->params->get('userid') != '') {
            $selectBuilder->where->add(Pap_Db_Table_Channels::USER_ID, '=', $this->params->get('userid'));
        } else {
            $selectBuilder->where->add(Pap_Db_Table_Channels::USER_ID, '=', Gpf_Session::getAuthUser()->getPapUserId());
        }
      
        return $selectBuilder;
    }
}
?>
