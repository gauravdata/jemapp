<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Maros Fric
*   @since Version 1.0.0
*   $Id: PayoutOption.class.php 19108 2008-07-14 10:00:32Z mfric $
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
class Pap_Db_AfterSignupAction extends Gpf_Db_FieldGroup {

    function __construct(){
        parent::__construct();
        $this->setType(Pap_Common_Constants::FIELDGROUP_TYPE_SIGNUPACTION);
    }
}

?>
