<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
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

class Pap_Common_Banner_PDF extends Pap_Common_Banner {
    
    protected function getBannerCode(Pap_Common_User $user, $flags) {
        if($flags & Pap_Common_Banner::FLAG_MERCHANT_PREVIEW || $flags & Pap_Common_Banner::FLAG_AFFILIATE_PREVIEW) {
    		return $this->_('Name').': <strong>'.$this->get(Pap_Db_Table_Banners::DATA1).'</strong><br/>'.$this->get(Pap_Db_Table_Banners::DATA3);
        } else {
        	return $this->get(Pap_Db_Table_Banners::DATA2);;
        }
    }
    
    /**
     * @param Pap_Common_User $user
     * @return string
     */
    public function getDescription(Pap_Common_User $user) {
        if ($user->getType() == Pap_Application::ROLETYPE_MERCHANT) {
            $description = $this->get(Pap_Db_Table_Banners::DATA1);
        } else {
            $description = $this->get(Pap_Db_Table_Banners::DATA2);
        }
        
        $description = str_replace('$Affiliate_id', $user->getId(), $description);
        $description = str_replace('$Affiliate_refid', $user->getRefId(), $description);
        $description = str_replace('$Affiliate_name', $user->getName(), $description);
        $description = str_replace('$Affiliate_username', $user->getUserName(), $description);
        
        return $description;
    }
}

?>
