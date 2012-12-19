<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric, Maros Galik
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
class Pap_Tracking_BackwardCompatibility_RecognizeAffiliate extends Pap_Tracking_Common_RecognizeAffiliate {

    protected function getUser(Pap_Contexts_Tracking $context) {
        if (($user = $this->getUserById($context, $context->getUserId())) != null) {
            return $user;
        }
        return null;
    }

}

?>
