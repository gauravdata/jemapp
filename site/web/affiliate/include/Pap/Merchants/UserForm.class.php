<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Ivan Ivanco
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
class Pap_Merchants_UserForm extends Gpf_Auth_UserForm  {

    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Pap_Common_AuthUser();
    }
}

?>
