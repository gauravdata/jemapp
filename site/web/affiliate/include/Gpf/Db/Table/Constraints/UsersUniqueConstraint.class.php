<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Users.class.php 24564 2009-06-05 08:37:35Z mjancovic $
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
class Gpf_Db_Table_Constraints_UsersUniqueConstraint extends Gpf_Object implements Gpf_DbEngine_Row_Constraint {
  
  /**
     * Validate Db_Row
     *
     * @param Gpf_DbEngine_Row $row
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function validate(Gpf_DbEngine_Row $row) {
      $role = new Gpf_Db_Role();
      $role->setId($row->getRoleId());
      $role->load();
      
      $select = new Gpf_SqlBuilder_SelectBuilder();
      $select->select->add('r.'.Gpf_Db_Table_Roles::TYPE);
      
      $select->from->add(Gpf_Db_Table_Users::getName(), 'u');
      $select->from->addInnerJoin(Gpf_Db_Table_Roles::getName(), 'r', 
        'u.'.Gpf_Db_Table_Users::ROLEID. '=r.'.Gpf_Db_Table_Roles::ID);
      
      $select->where->add('u.'.Gpf_Db_Table_Users::AUTHID, '=', $row->getAuthId());
      $select->where->add('u.'.Gpf_Db_Table_Users::ACCOUNTID, '=', $row->getAccountId());
      $select->where->add('r.'.Gpf_Db_Table_Roles::TYPE, '=', $role->getRoleType());
      $select->where->add('u.'.Gpf_Db_Table_Users::ID, '<>', $row->getPrimaryKeyValue());          
    
      try {
        $select->getOneRow();
      } catch (Gpf_DbEngine_NoRowException $e) {
        return;
      } catch (Gpf_DbEngine_TooManyRowsException $e) {
      }
      throw new Gpf_DbEngine_Row_ConstraintException('username', $this->_('Selected username already exists'));
    }
}
?>
