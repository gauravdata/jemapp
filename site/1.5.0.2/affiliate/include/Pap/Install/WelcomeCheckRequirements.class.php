<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: Merchant.class.php 18071 2008-05-16 08:02:18Z aharsani $
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


class Pap_Install_WelcomeCheckRequirements extends Gpf_Install_CheckRequirements {
    
    public function __construct() {
        parent::__construct();
        $this->code = 'Welcome';
        $this->name = $this->_('Welcome'); 
    }
    
    /**
     *
     * @service
     * @anonym
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Data
     */
    public function getHtml(Gpf_Rpc_Params $params) {
        $smarty = new Gpf_Templates_Template("installer_welcome.stpl");
        return $smarty->getDataResponse();
    }
    
    protected function getRequirements() {
        return new Pap_Install_Requirements();
    }
}
?>
