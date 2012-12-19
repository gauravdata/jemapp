<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
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
class Pap_Common_Banner_Text extends Pap_Common_Banner {
	
	protected function getBannerCode(Pap_Common_User $user, $flags) {
    	$title = $this->getTitle($user);
    	$description = $this->getDescription($user);
		$format = $this->getBannerFormat();
				
		$format = Pap_Common_UserFields::replaceCustomConstantInText('title', $title, $format);
		$format = Pap_Common_UserFields::replaceCustomConstantInText('description', $description, $format);
       	
		$format = $this->replaceUserConstants($format, $user);
    	$format = $this->replaceUrlConstants($format, $user, $flags, '');
    	$format = $this->replaceWidthHeightConstants($format, $flags);
    	
    	return $format;
	}
		
	private function getTitle(Pap_Common_User $user) {
		$title = $this->get(Pap_Db_Table_Banners::DATA1);
		
		$userFields = Pap_Common_UserFields::getInstance();
		$userFields->setUser($user);
		$title = $userFields->replaceUserConstantsInText($title);
		
		return $title;
	}

	public static function getBannerFormat() {
		return Gpf_Settings::get(Pap_Settings::TEXT_BANNER_FORMAT_SETTING_NAME);
	}
	
}

?>
