<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
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
 * @package PostAffiliatePro
 */
class CustomRefid_CustomRefidConstraint extends Gpf_Object implements Gpf_DbEngine_Row_Constraint {

    private $codeValidator;

    public function __construct() {
        $this->codeValidator = new Gpf_Common_CodeUtils_CodeValidator(Gpf_Settings::get(CustomRefid_Config::CUSTOM_REFID_FORMAT));
    }

    /**
     * Validate Db_Row
     *
     * @param Gpf_DbEngine_Row $row
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function validate(Gpf_DbEngine_Row $row) {        
        try {
            $user = $this->getUser($row->get(Pap_Db_Table_Users::ID));
            if ($row->get(Pap_Db_Table_Users::REFID) === $user->getRefId()) {
                return;
            }
        } catch (Gpf_Exception $e) {
        }
        $this->validateRefid($row);
    }
    
    /**
     * @throws Gpf_DbEngine_NoRowException
     * @throws Gpf_DbEngine_TooManyRowsException
     * @return Pap_Db_User
     */
    protected function getUser($userId) {
        $user = new Pap_Db_User();
        $user->setId($userId);
        $user->load();
        return $user;
    }

    protected function validateRefid(Gpf_DbEngine_Row $row) {
        if ($row->get(Pap_Db_Table_Users::TYPE) == Pap_Application::ROLETYPE_AFFILIATE &&
        !$this->codeValidator->validate($row->get(Pap_Db_Table_Users::REFID))) {
            throw new Gpf_DbEngine_Row_ConstraintException(Pap_Db_Table_Users::REFID,
            $this->_('Refid must be in format "%s". Format definition: {9} - will be replaced by any character in range [0-9], {z} - will be replaced by any character in range [a-z], {Z} - will be replaced by any character in range [A-Z], {X} - will be replaced by any character in range [0-9a-zA-Z], all other characters will be unchanged. Example of good format is e.g. {ZZZ}-{XXXXX}-{999}',
            Gpf_Settings::get(CustomRefid_Config::CUSTOM_REFID_FORMAT)));
        }
    }
}
