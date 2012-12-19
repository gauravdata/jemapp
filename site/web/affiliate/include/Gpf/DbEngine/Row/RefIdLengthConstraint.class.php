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
 
 class Gpf_DbEngine_Row_RefIdLengthConstraint extends Gpf_DbEngine_Row_LengthConstraint {
     
     /**
     * Validate Db_Row
     *
     * @param Gpf_DbEngine_Row $row
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function validate(Gpf_DbEngine_Row $row) {
        if ($row->get(Pap_Db_Table_Users::TYPE) != Pap_Application::ROLETYPE_AFFILIATE) {
			return;
        }
        return parent::validate($row);
    }
}
