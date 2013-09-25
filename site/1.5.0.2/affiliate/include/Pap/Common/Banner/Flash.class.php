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
class Pap_Common_Banner_Flash extends Pap_Common_Banner {
	protected function getBannerCode(Pap_Common_User $user, $flags) {
		$flashUrl = $this->get('data1');
		$wmode = $this->get('data2');
		$loop = $this->get('data3');
			
		$format = $this->getBannerFormat();

		$format = Pap_Common_UserFields::replaceCustomConstantInText('flashurl', $flashUrl, $format);

		$wmode = ($wmode == '' ? "Opaque" : $wmode);
		$format = Pap_Common_UserFields::replaceCustomConstantInText('wmode', $wmode, $format);

		$paramLoop = (($loop == '' || $loop == Gpf::YES) ? 'true' : 'false');
		$format = Pap_Common_UserFields::replaceCustomConstantInText('loop', $paramLoop, $format);

		$format = $this->replaceUserConstants($format, $user);
		$format = $this->replaceUrlConstants($format, $user, $flags, '');
		$format = $this->replaceWidthHeightConstants($format, $flags);

		return $format;
	}

	public static function getBannerFormat() {
		return Gpf_Settings::get(Pap_Settings::FLASH_BANNER_FORMAT_SETTING_NAME);
	}
}

?>
