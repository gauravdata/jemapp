<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: LengthConstraint.class.php 24476 2009-05-19 14:49:33Z mgalik $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_DbEngine_Row_PasswordConstraint extends Gpf_Object implements Gpf_DbEngine_Row_Constraint {

    private $passwordField = 'password';

    /**
     * @param string $columnNames
     */
    public function __construct($passwordField = 'password') {
        $this->passwordField = $passwordField;
    }

    /**
     * Validate Db_Row
     *
     * @param Gpf_DbEngine_Row $row
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function validate(Gpf_DbEngine_Row $row) {

        if (Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MIN_LENGTH) > Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MAX_LENGTH)) {
            return;
        }

        if (strlen($row->get($this->passwordField)) < Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MIN_LENGTH)) {
            throw new Gpf_DbEngine_Row_PasswordConstraintException($this->passwordField,
                $this->_('Minimum length of password is %s characters', Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MIN_LENGTH)));
        }

        if (strlen($row->get($this->passwordField)) > Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MAX_LENGTH)) {
            throw new Gpf_DbEngine_Row_PasswordConstraintException($this->passwordField,
                $this->_('Maximum length of password is %s characters', Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MAX_LENGTH)));
        }

        if (Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_LETTERS) == Gpf::YES) {
            if (preg_match('/[a-zA-Z]/', $row->get($this->passwordField)) == 0) {
                throw new Gpf_DbEngine_Row_PasswordConstraintException($this->passwordField,
                    $this->_('Password has to contain at least one letter (a-z, A-Z)'));
            }
        }

        if (Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_DIGITS) == Gpf::YES) {
            if (preg_match('/[0-9]/', $row->get($this->passwordField)) == 0) {
                throw new Gpf_DbEngine_Row_PasswordConstraintException($this->passwordField,
                    $this->_('Password has to contain at least one digit (0-9)'));
            }
        }

        if (Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_SPECIAL) == Gpf::YES) {
            if (preg_match('/[' . preg_quote(Gpf_Common_String::SPECIAL_CHARS) . ']/', $row->get($this->passwordField)) == 0) {
                throw new Gpf_DbEngine_Row_PasswordConstraintException($this->passwordField,
                    $this->_('Password has to contain at least one special character (%s)', Gpf_Common_String::SPECIAL_CHARS));
            }
        }
    }
}
