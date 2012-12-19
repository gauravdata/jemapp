<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package ShopMachine
 *   @since Version 1.0.0
 *   $Id: ActionParser.class.php 16620 2008-03-21 09:21:07Z aharsani $
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
class Pap_Mail_AffiliateSendMassMail extends Pap_Mail_SendMassMail {

    /**
     * Load list of template variables for custom template
     *
     * @service mail_template read
     * @param Gpf_Rpc_Params $params
     */
    public function getTemplateVariables(Gpf_Rpc_Params $params) {
        $recordSet = new Gpf_Data_RecordSet();
        $recordSet->setHeader(array('code', 'name'));

        $objTemplate = new Pap_Mail_AffiliateUserMail();

        foreach ($objTemplate->getTemplateVariables() as $code => $name) {
            if($code == 'password' || $code == 'parent_password'){
                continue;  
            } 
            $recordSet->add(array($code, $name));
        }

        return $recordSet;
    }
    
    
}
