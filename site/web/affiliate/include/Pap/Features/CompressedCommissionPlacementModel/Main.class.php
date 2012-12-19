<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Matej Kendera
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro plugins
 */
class Pap_Features_CompressedCommissionPlacementModel_Main extends Gpf_Plugins_Handler {

    public static function getHandlerInstance() {
        return new Pap_Features_CompressedCommissionPlacementModel_Main();
    }

    public function addToMenu(Pap_Merchants_Menu $menu) {
        $menu->getItem('Transactions-Overview')->addItem('Placement-Overview', $this->_('Placement Overview'));
        $menu->getItem('Tools')->addItem('Compressed-Commission-Settings', $this->_('Compressed Commission'));
    }

    public function initSettings(Gpf_Settings_Gpf $context) {
        $context->addDbSetting(Pap_Features_CompressedCommissionPlacementModel_Definition::DAY, '');
        $context->addDbSetting(Pap_Features_CompressedCommissionPlacementModel_Definition::ACTION, 'd');
        $context->addDbSetting(Pap_Features_CompressedCommissionPlacementModel_Definition::RECURRENCE, 'w');
        $context->addDbSetting(Pap_Features_CompressedCommissionPlacementModel_Definition::PROCESSING_ENABLED, Gpf::NO);
        $context->addDbSetting(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_EQUATION, 'H');
        $context->addDbSetting(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_EQUATION_VALUE1, '');
        $context->addDbSetting(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_EQUATION_VALUE2, '');
        $context->addDbSetting(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_STATUS, 'A');
        $context->addDbSetting(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_WHAT, 'C');
    }
}
?>
