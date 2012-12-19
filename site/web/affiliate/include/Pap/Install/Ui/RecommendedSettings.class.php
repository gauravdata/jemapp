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

class Pap_Install_Ui_RecommendedSettings extends Gpf_Ui_Widget {
    public function render() {
        $settings = new Pap_Install_RecommendedSettings();
        
        $template = new Gpf_Templates_Template('installer_recommended_settings.stpl');
        $template->assign('settings', $settings->getSettings());
        return $template->getHTML();
    }
}

?>
