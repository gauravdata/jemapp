<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak, Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: User.class.php 17743 2008-05-06 08:25:49Z mfric $
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
class Pap_Common_UserTree extends Gpf_Object  {

	private $affiliatesInDownline = null;
	private $parentUserCache = null;
	
	/**
	 * @return array
	 */
	public function getChildren(Pap_Common_User $parent, $offset = '', $limit = '') {
		$children = array();

		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
		$selectBuilder->select->addAll(Pap_Db_Table_Users::getInstance(), 'pu');
		$selectBuilder->select->addAll(Gpf_Db_Table_AuthUsers::getInstance(), 'au');

		$selectBuilder->from->add(Pap_Db_Table_Users::getName(), 'pu');
		$selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'gu', 'pu.accountuserid = gu.accountuserid');
		$selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au', 'gu.authid = au.authid');

		$selectBuilder->where->add(Pap_Db_Table_Users::PARENTUSERID, '=', $parent->getId());
		$selectBuilder->where->add(Gpf_Db_Table_Users::STATUS, 'IN', array(Pap_Common_Constants::STATUS_APPROVED, Pap_Common_Constants::STATUS_PENDING));
		$selectBuilder->orderBy->add(Pap_Db_Table_Users::DATEINSERTED);

		$selectBuilder->limit->set($offset, $limit);
			
		foreach ($selectBuilder->getAllRowsIterator() as $userRecord) {
			$user = new Pap_Common_User();
			$user->fillFromRecord($userRecord);
			$children[] = $user;
		}

		return $children;
	}

	public function startCheckingLoops() {
		$this->affiliatesInDownline = array();
	}

	public function stopCheckingLoops() {
		$this->affiliatesInDownline = null;
	}
	/**
	 * @return Pap_Common_User or null
	 */
	public function getParent(Pap_Common_User $child) {
        $parentUserId = $child->getParentUserId();
        if (!isset($this->parentUserCache[$parentUserId])) {
            if (is_array($this->affiliatesInDownline)) {
                $this->affiliatesInDownline[] = $child->getId();
                if (in_array($parentUserId, $this->affiliatesInDownline) || $child->getId() == $parentUserId) {
                    $child->setParentUserId('');
                    $child->save();
                    $this->parentUserCache[$parentUserId] = null;
                    return null;
                }
            }
            $this->parentUserCache[$parentUserId] = $child->getParentUser();
        }
        return $this->parentUserCache[$parentUserId];
    }

	/**
	 * @return Pap_Common_User or null
	 */
	public function getChosenUser($chosenUserId) {
		$user = new Pap_Common_User();
		$user->setId($chosenUserId);
		try {
			$user->load();
			return $user;
		} catch (Gpf_Exception $e) {
			return null;
		}
	}
}

?>
