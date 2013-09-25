<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
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
class Pap_Features_ForcedMatrix_Matrix extends Gpf_Object {

	const SPILLOVER_ACTUAL_SPONSOR = 'S';
	const SPILLOVER_NO_SPONSOR = 'N';
	const SPILLOVER_CHOSEN_AFFILIATE = 'A';
	const SPILLOVER_EXPAND_MATRIX = 'E';
	const NONE_PARENT_USER_ID = '';

	/**
	 * @var Pap_Common_UserTree
	 */
	private $userTree;
	/**
	 * @var Pap_Features_ForcedMatrix_MatrixFillUserSettings
	 */
	private $matrixUserSettings;
	private $offset = '';
	private $limit = '';
	private $matrixWidth;
	private $matrixHeight;

	public function __construct(Pap_Common_UserTree $userTree) {
		$this->userTree = $userTree;
	}

	public function computeParentFor(Pap_Common_User $child) {
		$originalParent = $this->userTree->getParent($child);
		if ($originalParent === null) {
			$child->setParentUserId(self::NONE_PARENT_USER_ID);
			return;
		}
		$this->initMatrixSize($originalParent);		
		$parent = $this->getParent(array($originalParent));
		if ($parent === null) {
			$this->spillover($child, $originalParent);
			return;
		}
		$child->setParentUserId($parent->getId());
	}

	public function isFilled(Pap_Common_User $originalParent) {
		$this->initMatrixSize($originalParent);
		return $this->getParent(array($originalParent)) === null;
	}

	/**
	 * @return Pap_Common_User or null
	 */
	protected function getParent(array $levelUsers, $level = 0) {
		if (($this->isLimitedHeight() && $level >= $this->matrixHeight) || count($levelUsers) < 1) {
			return null;
		}
		$nextLevelUsers = array();
		foreach ($levelUsers as $levelUser) {
			$children = $this->userTree->getChildren($levelUser, $this->offset, $this->limit);
			if ($this->isParent($children, $level)) {
				return $levelUser;
			}
			$nextLevelUsers = $this->addChildren($children, $nextLevelUsers);
		}
		return $this->getParent($nextLevelUsers, ++$level);
	}

	private function spillover(Pap_Common_User $child, Pap_Common_User $originalParentUser) {
		if (Gpf_Settings::get(Pap_Settings::MATRIX_SPILLOVER) == Pap_Features_ForcedMatrix_Matrix::SPILLOVER_ACTUAL_SPONSOR) {
			$this->spilloverActualSponsor($child, $originalParentUser);
			return;
		}
		if (Gpf_Settings::get(Pap_Settings::MATRIX_SPILLOVER) == Pap_Features_ForcedMatrix_Matrix::SPILLOVER_CHOSEN_AFFILIATE) {
			$this->spilloverChosenAffiliate($child, $originalParentUser);
			return;
		}
		if (Gpf_Settings::get(Pap_Settings::MATRIX_SPILLOVER) == Pap_Features_ForcedMatrix_Matrix::SPILLOVER_EXPAND_MATRIX) {
			$this->spilloverExpandMatrix($child);
			return;
		}
		$child->setParentUserId(self::NONE_PARENT_USER_ID);
	}

	/**
	 * @param $originalParentUser
	 * @return Pap_Common_User or null
	 */
	private function getParentSpillover(Pap_Common_User $originalParentUser) {
		$limit = 1;
		do {
			$this->initLimit($limit, $this->matrixWidth - 1 + $limit);
			$children = $this->userTree->getChildren($originalParentUser, $this->offset, $this->limit);
			if (count($children) > 0) {
				$this->initLimit($this->matrixWidth);
				$parent = $this->getParent($children, 1);
				$limit++;
				continue;
			}
			return null;
		} while ($parent === null);
		return $parent;
	}

	private function spilloverActualSponsor(Pap_Common_User $child, Pap_Common_User $originalParentUser) {
		$parent = $this->getParentSpillover($originalParentUser);
		if ($parent === null) {
			$child->setParentUserId($originalParentUser->getId());
			return;
		}
		$child->setParentUserId($parent->getId());
	}

	private function spilloverChosenAffiliate(Pap_Common_User $child, Pap_Common_User $originalParentUser) {
		$user = $this->userTree->getChosenUser(Gpf_Settings::get(Pap_Settings::MATRIX_AFFILIATE));
		if ($user === null) {
			$child->setParentUserId(self::NONE_PARENT_USER_ID);
			return;
		}
		$this->initLimit($this->matrixWidth);
		$parent = $this->getParent(array($user));
		if ($parent === null) {
			$parent = $this->getParentSpillover($user);
			if ($parent === null) {
				$child->setParentUserId($user->getId());
				return;
			}
		}
		$child->setParentUserId($parent->getId());
	}

	private function spilloverExpandMatrix(Pap_Common_User $child) {		
		$this->matrixWidth += Gpf_Settings::get(Pap_Settings::MATRIX_EXPAND_WIDTH);
		$this->matrixHeight += Gpf_Settings::get(Pap_Settings::MATRIX_EXPAND_HEIGHT);
		$this->matrixUserSettings->setMatrixWidth($this->matrixWidth);
		$this->matrixUserSettings->setMatrixHeight($this->matrixHeight);
		$this->computeParentFor($child);
	}


	/**
	 * @return boolean
	 */
	private function isParent(array $children, $level) {
		if ($this->isLimitedHeight() && $level >= $this->matrixHeight) {
			return false;
		}
		if (count($children) > 0) {
			if ($this->isLimitedWidth() && count($children) >= $this->matrixWidth) {
				return false;
			} else {
				return true;
			}
		}
		return true;
	}

	private function initOffset($offset) {
		if ($offset >= 0) {
			$this->offset = $offset;
			return;
		}
		$this->offset = '';
	}

	private function initLimit($limit = '', $offset = '') {
		$this->initOffset($offset);
		if ($limit > 0) {
			$this->limit = $limit;
			return;
		}
		$this->limit = '';
	}

	private function addChildren(array $children, array $nextLevelUsers) {
		foreach ($children as $child) {
			$nextLevelUsers[] = $child;
		}

		return $nextLevelUsers;
	}

	private function isLimitedWidth() {
		return Pap_Settings::MATRIX_WIDTH_DEFAULT_VALUE < $this->matrixWidth;
	}

	private function isLimitedHeight() {
		return Pap_Settings::MATRIX_HEIGHT_DEFAULT_VALUE < $this->matrixHeight;
	}
	
	private function initMatrixSize(Pap_Common_User $originalParent) {
		$this->matrixUserSettings = new Pap_Features_ForcedMatrix_MatrixFillUserSettings($originalParent);
		$this->matrixWidth = $this->matrixUserSettings->getMatrixWidth();
		$this->matrixHeight = $this->matrixUserSettings->getMatrixHeight();
		$this->initLimit($this->matrixWidth);
	}
}
?>
