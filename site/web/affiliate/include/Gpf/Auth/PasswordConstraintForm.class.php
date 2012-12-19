<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TransactionsForm.class.php 19023 2008-07-08 12:50:59Z mfric $
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
class Gpf_Auth_PasswordConstraintForm extends Gpf_View_FormService {

    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Gpf_DbEngine_Row();
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Password Constraints");
    }

    /**
     * special handling
     *
     * @service password_constraints read
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $form->setField('minLength', Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MIN_LENGTH));
        $form->setField('maxLength', Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MAX_LENGTH));
        $form->setField('azChars', Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_LETTERS));
        $form->setField('digitsChars', Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_DIGITS));
        $form->setField('specialChars', Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_SPECIAL));
        return $form;
    }

    /**
     * special handling
     *
     * @service password_constraints write
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        return $this->save($params);
    }

    /**
     * special handling
     *
     * @service password_constraints write
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        if ($form->getFieldValue('minLength') > $form->getFieldValue('maxLength')) {
            $form->setFieldError('minLength', $this->_("Minimum password length can't be bigger as maximum password length"));
            return $form;
        }

        Gpf_Settings::set(Gpf_Settings_Gpf::PASSWORD_MIN_LENGTH, $form->getFieldValue('minLength'));
        Gpf_Settings::set(Gpf_Settings_Gpf::PASSWORD_MAX_LENGTH, $form->getFieldValue('maxLength'));
        Gpf_Settings::set(Gpf_Settings_Gpf::PASSWORD_LETTERS, $form->getFieldValue('azChars'));
        Gpf_Settings::set(Gpf_Settings_Gpf::PASSWORD_DIGITS, $form->getFieldValue('digitsChars'));
        Gpf_Settings::set(Gpf_Settings_Gpf::PASSWORD_SPECIAL, $form->getFieldValue('specialChars'));

        $form->setInfoMessage($this->_("Password constraints saved"));
        return $form;
    }
}

?>
