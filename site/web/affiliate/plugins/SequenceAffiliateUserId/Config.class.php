<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
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
 * @package PostAffiliatePro
 */
class SequenceAffiliateUserId_Config extends Gpf_Plugins_Config {
    const FIELD_KEY = 'sequenceid';

    protected function initFields() {
        $this->addTextBox("Current UserId", self::FIELD_KEY, "Enter ID from which will be generated userid sequence");
    }

    /**
     * Save sequence id
     *
     * @anonym
     * @service useridsequence write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(SequenceAffiliateUserId_Main::SETTING_USERID_SEQUENCE, $form->getFieldValue(self::FIELD_KEY));
        $form->setInfoMessage($this->_('Sequence key saved'));
        return $form;
    }

    /**
     * Load userid sequence number
     *
     * @anonym
     * @service useridsequence read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->addField(self::FIELD_KEY, Gpf_Settings::get(SequenceAffiliateUserId_Main::SETTING_USERID_SEQUENCE));
        return $form;
    }
}

?>
