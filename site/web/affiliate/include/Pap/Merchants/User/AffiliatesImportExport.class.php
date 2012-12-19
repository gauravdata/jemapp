<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: CommissionsExport.class.php 19572 2008-08-01 16:43:19Z mjancovic $
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
class Pap_Merchants_User_AffiliatesImportExport extends Gpf_Csv_ObjectImportExport {

    const PAYOUT_OPTIONS = 'Payout options';

    protected $papUserColumns;
    protected $gpfUserColumns;
    protected $authUserColumns;
    private $papUserColumnsWithoutParentUserId;

    public function __construct() {
    	parent::__construct();
    	$this->initColumns();
        $this->setName(Gpf_Lang::_runtime("Affiliates"));
        $this->setDescription(Gpf_Lang::_runtime("AffiliatesImportExportDescription"));
        $this->paramsArray['papUserIds'] = array();
    }

    
    
    /**
     * Import data from file
     *
     * @service import_export import
     * @param Gpf_Rpc_Params $params
     */
    public function importData(Gpf_Rpc_Params $params) {
        $this->file = new Gpf_Csv_File($params->get("fileUrl"), 'r');
        $this->delimiter = $params->get("delimiter");
        $this->fileUrl = $params->get("fileUrl");
        $this->file->setDelimiter($this->delimiter);
        $this->delete = ($params->get("delete") == Gpf::YES ? true : false);
        $this->run($params->get('startTime') + 24 - time());
    }

    protected function execute() {
        if ($this->isBlockPending('check')) {
        	$this->logger->debug('Check affiliates');
            $this->checkData();
            $this->setBlockDone();
        }

        if ($this->delete &&
        $this->isBlockPending('deletePayoutOptions')) {
        	$this->logger->debug('Delete payout options');
            $this->deletePayoutOptions();
            $this->setBlockDone();
        }

        $this->readData();

        if ($this->delete &&
        $this->isBlockPending('deleteAffiliates')) {
        	$this->logger->debug('Delete affiliates');
            $this->deleteAffiliates();
            $this->setBlockDone();
        }
    }

    protected function checkData() {
        $this->setRequiredColumns(array('!USERID', '!RSTATUS'));   
        $headerColumns = $this->getArrayHeaderColumns($this->getAffiliates());
        unset($headerColumns['!ORIGINALPARENTUSERID']);
        $this->checkFile($headerColumns);
        $this->rewindFile();
    }

    protected function writeData() {
        $this->writeSelectBuilder($this->getAffiliates());
        $this->writeDataDelimiter();
         
        $this->writeDataHeader(self::PAYOUT_OPTIONS);
        $this->writeSelectBuilder($this->getPayoutOptions());
    }

    protected function deleteData() {
    }

    protected function initColumns() {
        $this->papUserColumns = $this->getPapUsers();
        $this->gpfUserColumns = $this->getGpfUsers();
        $this->authUserColumns = $this->getAuthUsers();
    }
    
    protected function readData() {
        $allColumns = $this->getAllAffiliateColumns();

        if ($this->isBlockPending('importAffiliates')) {
            $this->partName = $this->_('affiliates');
            $this->logger->debug($this->_('Import %s', $this->partName));
            $this->readAffiliateObject($allColumns);
            $this->rewindFile();
            $this->setBlockDone();
        }

        // Update ParentUserId for new affiliates
        if ($this->isBlockPending('importAffiliatesParentUserId')) {
            $this->partName = $this->_('affiliates parentuserid');
            $this->logger->debug($this->_('Import %s', $this->partName));
            $this->readAffiliateObject($allColumns);
            $this->rewindFile();
            $this->setBlockDone();
        }

        if ($this->isBlockPending('importPayoutOptions')) {
            $this->setDataHeader(self::PAYOUT_OPTIONS);
            $this->partName = $this->_('payout options');
            $this->logger->debug($this->_('Import %s', $this->partName));
            $this->readPayoutOptionsObject($this->getArrayHeaderColumns($this->getPayoutOptions()));
            $this->setBlockDone();
        }
    }

    private function getPayoutOptions() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(Pap_Db_Table_UserPayoutOptions::USERID, Pap_Db_Table_UserPayoutOptions::USERID, 'po');
        $selectBuilder->select->add(Gpf_Db_Table_FormFields::CODE, 'name', 'ff');
        $selectBuilder->select->add(Pap_Db_Table_UserPayoutOptions::VALUE, Pap_Db_Table_UserPayoutOptions::VALUE, 'po');
        $selectBuilder->from->add(Pap_Db_Table_UserPayoutOptions::getName(), 'po');
        $selectBuilder->from->addInnerJoin(Gpf_Db_Table_FormFields::getName(), 'ff',
            'po.'.Pap_Db_Table_UserPayoutOptions::FORMFIELDID.'=ff.'.Gpf_Db_Table_FormFields::ID);
         
        return $selectBuilder;
    }

    private function getAffiliates() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('pu.'.Pap_Db_Table_Users::ID, Pap_Db_Table_Users::ID);
        $selectBuilder->select->add('pu.'.Pap_Db_Table_Users::REFID, Pap_Db_Table_Users::REFID);
        $selectBuilder->select->add('pu.'.Pap_Db_Table_Users::DATEINSERTED, Pap_Db_Table_Users::DATEINSERTED);
        $selectBuilder->select->add('pu.'.Pap_Db_Table_Users::DATEAPPROVED, Pap_Db_Table_Users::DATEAPPROVED);
        $selectBuilder->select->add('pu.'.Pap_Db_Table_Users::DELETED, Pap_Db_Table_Users::DELETED);
        $selectBuilder->select->add('pu.'.Pap_Db_Table_Users::PARENTUSERID, Pap_Db_Table_Users::PARENTUSERID);
        $selectBuilder->select->add('pu.'.Pap_Db_Table_Users::PAYOUTOPTION_ID, Pap_Db_Table_Users::PAYOUTOPTION_ID);
        $selectBuilder->select->add('pu.'.Pap_Db_Table_Users::MINIMUM_PAYOUT, Pap_Db_Table_Users::MINIMUM_PAYOUT);
        $selectBuilder->select->add('pu.'.Pap_Db_Table_Users::NOTE, Pap_Db_Table_Users::NOTE);
        $selectBuilder->select->add('pu.'.Pap_Db_Table_Users::PHOTO, Pap_Db_Table_Users::PHOTO);
        $selectBuilder->select->add('pu.'.Pap_Db_Table_Users::ORIGINAL_PARENT_USERID, Pap_Db_Table_Users::ORIGINAL_PARENT_USERID);
        for ($i = 1; $i <= 25; $i++) {
            $selectBuilder->select->add('pu.data'.$i, 'data'.$i);
        }
        $selectBuilder->select->add('gu.'.Gpf_Db_Table_Users::STATUS, Gpf_Db_Table_Users::STATUS);
        $selectBuilder->select->add('gu.'.Gpf_Db_Table_Users::ACCOUNTID, Gpf_Db_Table_Users::ACCOUNTID);
        $selectBuilder->select->add('au.'.Gpf_Db_Table_AuthUsers::USERNAME, Gpf_Db_Table_AuthUsers::USERNAME);
        $selectBuilder->select->add('au.'.Gpf_Db_Table_AuthUsers::PASSWORD, Gpf_Db_Table_AuthUsers::PASSWORD);
        $selectBuilder->select->add('au.'.Gpf_Db_Table_AuthUsers::FIRSTNAME, Gpf_Db_Table_AuthUsers::FIRSTNAME);
        $selectBuilder->select->add('au.'.Gpf_Db_Table_AuthUsers::LASTNAME, Gpf_Db_Table_AuthUsers::LASTNAME);
        $selectBuilder->select->add('au.'.Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL, Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL);
        $selectBuilder->from->add(Pap_Db_Table_Users::getName(), 'pu');
        $selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'gu', 'gu.'.
        Gpf_Db_Table_Users::ID.' = pu.'.Pap_Db_Table_Users::ACCOUNTUSERID);
        $selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(),
             'au', 'au.'.Gpf_Db_Table_AuthUsers::ID.' = gu.'.Gpf_Db_Table_Users::AUTHID);
        $selectBuilder->where->add('pu.'.Pap_Db_Table_Users::TYPE, '=', Pap_Application::ROLETYPE_AFFILIATE);

        return $selectBuilder;
    }

    private function getPapUsers() {
        $columns = array();
        $columns[$this->encodeColumn(Pap_Db_Table_Users::ID)] = 'pu.'.Pap_Db_Table_Users::ID;
        $columns[$this->encodeColumn(Pap_Db_Table_Users::REFID)] = 'pu.'.Pap_Db_Table_Users::REFID;
        $columns[$this->encodeColumn(Pap_Db_Table_Users::DATEINSERTED)] = 'pu.'.Pap_Db_Table_Users::DATEINSERTED;
        $columns[$this->encodeColumn(Pap_Db_Table_Users::DATEAPPROVED)] = 'pu.'.Pap_Db_Table_Users::DATEAPPROVED;
        $columns[$this->encodeColumn(Pap_Db_Table_Users::DELETED)] = 'pu.'.Pap_Db_Table_Users::DELETED;
        $columns[$this->encodeColumn(Pap_Db_Table_Users::ACCOUNTUSERID)] = 'pu.'.Pap_Db_Table_Users::ACCOUNTUSERID;
        $columns[$this->encodeColumn(Pap_Db_Table_Users::PARENTUSERID)] = 'pu.'.Pap_Db_Table_Users::PARENTUSERID;
        $columns[$this->encodeColumn(Pap_Db_Table_Users::PAYOUTOPTION_ID)] = 'pu.'.Pap_Db_Table_Users::PAYOUTOPTION_ID;
        $columns[$this->encodeColumn(Pap_Db_Table_Users::MINIMUM_PAYOUT)] = 'pu.'.Pap_Db_Table_Users::MINIMUM_PAYOUT;
        $columns[$this->encodeColumn(Pap_Db_Table_Users::NOTE)] = 'pu.'.Pap_Db_Table_Users::NOTE;
        $columns[$this->encodeColumn(Pap_Db_Table_Users::PHOTO)] = 'pu.'.Pap_Db_Table_Users::PHOTO;
        $columns[$this->encodeColumn(Pap_Db_Table_Users::ORIGINAL_PARENT_USERID)] = 'pu.'.Pap_Db_Table_Users::ORIGINAL_PARENT_USERID;
        for ($i = 1; $i <= 25; $i++) {
            $columns[$this->encodeColumn('data'.$i)] = 'pu.data'.$i;
        }

        return $columns;
    }

    private function getGpfUsers() {
        $columns = array();
        $columns[$this->encodeColumn(Gpf_Db_Table_Users::ID)] = 'gu.'.Gpf_Db_Table_Users::ID;
        $columns[$this->encodeColumn(Gpf_Db_Table_Users::AUTHID)] = 'gu.'.Gpf_Db_Table_Users::AUTHID;
        $columns[$this->encodeColumn(Gpf_Db_Table_Users::STATUS)] = 'gu.'.Gpf_Db_Table_Users::STATUS;
        $columns[$this->encodeColumn(Gpf_Db_Table_Users::ROLEID)] = 'gu.'.Gpf_Db_Table_Users::ROLEID;
        $columns[$this->encodeColumn(Gpf_Db_Table_Users::ACCOUNTID)] = 'gu.'.Gpf_Db_Table_Users::ACCOUNTID;

        return $columns;
    }

    private function getAuthUsers() {
        $columns = array();
        $columns[$this->encodeColumn(Gpf_Db_Table_AuthUsers::ID)] = 'au.'.Gpf_Db_Table_AuthUsers::ID;
        $columns[$this->encodeColumn(Gpf_Db_Table_AuthUsers::USERNAME)] = 'au.'.Gpf_Db_Table_AuthUsers::USERNAME;
        $columns[$this->encodeColumn(Gpf_Db_Table_AuthUsers::PASSWORD)] = 'au.'.Gpf_Db_Table_AuthUsers::PASSWORD;
        $columns[$this->encodeColumn(Gpf_Db_Table_AuthUsers::FIRSTNAME)] = 'au.'.Gpf_Db_Table_AuthUsers::FIRSTNAME;
        $columns[$this->encodeColumn(Gpf_Db_Table_AuthUsers::LASTNAME)] = 'au.'.Gpf_Db_Table_AuthUsers::LASTNAME;
        $columns[$this->encodeColumn(Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL)] = 'au.'.Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL;

        return $columns;
    }

    private function deletePayoutOptions() {
        $deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBuilder->from->add(Pap_Db_Table_UserPayoutOptions::getName());
        $deleteBuilder->execute();
    }

    private function deleteAffiliates() {
        if (count($this->paramsArray['papUserIds']) > 0) {
            $deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
            $deleteBuilder->delete->add('pu');
            $deleteBuilder->delete->add('gu');
            $deleteBuilder->delete->add('au');
            $deleteBuilder->from->add(Pap_Db_Table_Users::getName(), 'pu');
            $deleteBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'gu', 'gu.'.
            Gpf_Db_Table_Users::ID.' = pu.'.Pap_Db_Table_Users::ACCOUNTUSERID);
            $deleteBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(),
                'au', 'au.'.Gpf_Db_Table_AuthUsers::ID.' = gu.'.Gpf_Db_Table_Users::AUTHID);
            $deleteBuilder->where->add('pu.'.Pap_Db_Table_Users::TYPE, '=', Pap_Application::ROLETYPE_AFFILIATE);
            $deleteBuilder->where->add('pu.'.Pap_Db_Table_Users::ID, 'NOT IN', $this->paramsArray['papUserIds'], 'AND', false);
            $deleteBuilder->delete();
        }
    }

    public function readPayoutOptionsObject(array $columns) {
        if ($this->findCode()) {
            $this->findDataHeader();
            if ($headerArray = $this->getFile()->readArray()) {
                if ($this->hasData($headerArray)) {
                    $headerColumns = $this->mapHeaderColumns($columns, $headerArray);
                    $this->appendOutputMessage($this->getPartNameMessage($this->getPartName()));
                    $this->readPayoutOptionRows($headerColumns, $headerArray);
                }
            }
        }
    }
    
    protected function getPapUserColumns() {
        return $this->papUserColumns;
    }
    
    protected function getGpfUserColumns() {
        return $this->gpfUserColumns;
    }
    
    protected function getAuthUserColumns() {
        return $this->authUserColumns;
    }

    public function readAffiliateObject(array $columns) {        
        if ($this->findCode()) {
            if (isset($this->paramsArray['importedLinesCount'])) {
                $this->importedLinesCount = $this->paramsArray['importedLinesCount'];
            }
            if (isset($this->paramsArray['wrongLinesCount'])) {
                $this->wrongLinesCount = $this->paramsArray['wrongLinesCount'];
            }
            $headerArray = $this->readHeaderArray();
            $headerColumns = $this->mapHeaderColumns($columns, $headerArray);
            $headerColumns = $this->removeAlias($headerColumns);
             
            $this->papUserColumns = $this->mapHeaderColumns($this->getPapUserColumns(), $headerArray);
            $this->papUserColumns = $this->removeAlias($this->getPapUserColumns());
             
            $this->gpfUserColumns = $this->mapHeaderColumns($this->getGpfUserColumns(), $headerArray);
            $this->gpfUserColumns = $this->removeAlias($this->getGpfUserColumns());
             
            $this->authUserColumns = $this->mapHeaderColumns($this->getAuthUserColumns(), $headerArray);
            $this->authUserColumns = $this->removeAlias($this->getAuthUserColumns());

            $this->appendOutputMessage($this->getPartNameMessage($this->getPartName()));
            $this->importAffiliateRows($headerArray);
        }
    }
    
    protected function getFile() {
        return $this->file;
    }
    
    /**
     * @return Pap_Db_User
     */
    protected function getPapDbUserObject() {
        return new Pap_Db_User();
    }
    
    /**
     * @return Gpf_Db_User
     */
    protected function getGpfDbUserObject() {
        return new Gpf_Db_User();
    }
    
    /**
     * @return Gpf_Db_AuthUser
     */
    protected function getGpfDbAuthUserObject() {
        return new Gpf_Db_AuthUser();
    }

    private function importAffiliateRows(array $headerArray) {
        while ($row = $this->getFile()->readArray()) {
            $this->logger->debug('Reading row ('.$this->getFile()->getActualCSVLineNumber().'): ' . implode($this->delimiter, $row));
            $this->paramsArray['importedLinesCount'] = $this->importedLinesCount;
            $this->paramsArray['wrongLinesCount'] = $this->wrongLinesCount;
            if ($this->isPending(implode($this->delimiter, $row), $this->_('Import %s rows', $this->getName()))) {
                if ($this->hasData($row)) {
                    if (count($row) >= count($headerArray)) {
                        $papUser = $this->getPapDbUserObject();
                        try {
                            $this->setDbRowPrimaryKey($papUser, $row, $headerArray, '!'.strtoupper(Pap_Db_Table_Users::ID));
                            $papUser->load();
                        } catch (Gpf_DbEngine_NoRowException $e) {
                            try {
                                $this->createNewAffiliate($headerArray, $row);
                                $this->paramsArray['papUserIds'][] = $papUser->getPrimaryKeyValue();
                                $this->setDone();
                                $this->incrementSuccessfulCount();
                                $this->logger->debug('Data from line: ' . $this->getFile()->getActualCSVLineNumber() . ' was inserted.');
                                continue;
                            } catch (Gpf_Exception $e) {
                                $this->appendOutputMessage($this->getSaveErrorMessage($this->getFile()->getActualCSVLineNumber(), $e->getMessage()));
                                $this->incrementWrongCount();
                                continue;
                            }
                        } catch (Gpf_Exception $e) {
                            $this->logger->debug($this->_sys('Internal processing error during import from %s on line %s. Throw (horrible) exception: %s', $this->getFile()->getFileName(), implode($this->delimiter, $row), $e->getMessage()));                       
                            $this->appendOutputMessage($this->getSaveErrorMessage($this->getFile()->getActualCSVLineNumber(), $e->getMessage()));
                            $this->incrementWrongCount();
                            continue;
                        }
						
                        $gpfUser = $this->getGpfDbUserObject();
                        try {
                            $gpfUser->setPrimaryKeyValue($papUser->getAccountUserId());
                            $gpfUser->load();
                        } catch (Gpf_Exception $e) {
                            $this->logger->debug($this->_sys('Internal processing error during import from %s on line %s. Throw (horrible) exception: %s', $this->getFile()->getFileName(), implode($this->delimiter, $row), $e->getMessage()));                       
                            $this->appendOutputMessage($this->getSaveErrorMessage($this->getFile()->getActualCSVLineNumber(), $e->getMessage()));
                            $this->incrementWrongCount();
                            continue;
                        }
                        
                        $authUser = $this->getGpfDbAuthUserObject();                    
                        try {
                            $authUser->setPrimaryKeyValue($gpfUser->get(Gpf_Db_Table_Users::AUTHID));
                            $authUser->load();
                        } catch (Gpf_Exception $e) {
                            $this->logger->debug($this->_sys('Internal processing error during import from %s on line %s. Throw (horrible) exception: %s', $this->getFile()->getFileName(), implode($this->delimiter, $row), $e->getMessage()));
                            $this->appendOutputMessage($this->getSaveErrorMessage($this->getFile()->getActualCSVLineNumber(), $e->getMessage()));
                            $this->incrementWrongCount();
                            continue;
                        }

                        try {
                            $this->setDbRowData($papUser, $row, $headerArray, $this->getPapUserColumns());
                            if (!in_array("!ORIGINALPARENTUSERID", $headerArray)) {
                                $papUser->setOriginalParentUserId($papUser->getParentUserId());
                            }
                            $papUser->update();
                            $this->setDbRowData($gpfUser, $row, $headerArray, $this->getGpfUserColumns());
                            $gpfUser->update();
                            $this->setDbRowData($authUser, $row, $headerArray, $this->getAuthUserColumns());
                            $authUser->update();
                            $this->incrementSuccessfulCount();
                            $this->logger->debug('Data from line: ' . $this->getFile()->getActualCSVLineNumber() . ' was updated.');                            
                        } catch (Gpf_Exception $e) {
                            $this->logger->debug($this->_sys('Error occured during import from %s on line %s. Can not update affiliate with id %s. Throw exception: %s', $this->getFile()->getFileName(), implode($this->delimiter, $row), $papUser->getId(), $e->getMessage()));                       
                            $this->appendOutputMessage($this->getSaveErrorMessage($this->getFile()->getActualCSVLineNumber(), $e->getMessage()));
                            $this->incrementWrongCount();
                            continue;
                        }
                        $this->paramsArray['papUserIds'][] = $papUser->getPrimaryKeyValue();
                    } else {
                        $this->logger->debug($this->getLessItemsMessage($this->getFile()->getActualCSVLineNumber(), false));
                        $this->appendOutputMessage($this->getLessItemsMessage($this->getFile()->getActualCSVLineNumber()));
                        $this->incrementWrongCount();
                    }
                } else {
                    $this->logger->debug($this->getEndDataMessage($this->getFile()->getActualCSVLineNumber(), ($this->getPartName() != null ? $this->getPartName() : $this->getName())));
                    $this->setDone();
                    break;
                }
                $this->setDone();
            }
        }
        $this->logger->debug($this->getEndFileMessage($this->getPartName() != null ? $this->getPartName() : $this->getName()));
        $this->appendOutputData($this->importedLinesCount, $this->wrongLinesCount);
        $this->resetImportedCounts();
    }

    protected function readPayoutOptionRows(array $columns, array $headerArray) {
        while ($row = $this->getFile()->readArray()) {
            $this->logger->debug('Reading row ('.$this->getFile()->getActualCSVLineNumber().'): ' . implode($this->delimiter, $row));
            $this->paramsArray['importedLinesCount'] = $this->importedLinesCount;
            $this->paramsArray['wrongLinesCount'] = $this->wrongLinesCount;
            if ($this->isPending(implode($this->delimiter, $row), $this->_('Import %s rows', $this->getName()))) {
                if ($this->hasData($row)) {
                    if (count($row) >= count($headerArray)) {
                        $payoutOptionData = array();
                        for ($i = 0; $i < count($headerArray); $i++) {
                            if (array_key_exists($headerArray[$i], $columns)) {
                                $payoutOptionData[$columns[$headerArray[$i]]] = $row[$i];
                            }
                        }
                        $this->processPayoutOption($payoutOptionData, $row);
                    } else {
                        $this->logger->debug($this->getLessItemsMessage($this->getFile()->getActualCSVLineNumber(), false));
                        $this->appendOutputMessage($this->getLessItemsMessage($this->getFile()->getActualCSVLineNumber()));
                        $this->incrementWrongCount();
                    } 
                } else {
                    $this->logger->debug($this->getEndDataMessage($this->getFile()->getActualCSVLineNumber(), ($this->getPartName() != null ? $this->getPartName() : $this->getName())));
                    $this->setDone();
                    break;
                }
                $this->setDone();
            }
        }
        $this->logger->debug($this->getEndFileMessage($this->getPartName() != null ? $this->getPartName() : $this->getName()));
        $this->appendOutputData($this->importedLinesCount, $this->wrongLinesCount);
        $this->resetImportedCounts();
    }

    /**
     * @param array $payoutOptionData
     */
    private function processPayoutOption($payoutOptionData, $row) {
        $user = new Pap_Affiliates_User();
        $user->setId($payoutOptionData[Pap_Db_Table_UserPayoutOptions::USERID]);
        try {
            $user->load();

            $payoutOption = new Pap_Db_PayoutOption();
            $payoutOption->setID($user->getPayoutOptionId());
            $payoutOption->load();

            $payoutField = new Gpf_Db_FormField();
            $payoutField->setFormId($payoutOption->getFormId());
            $payoutField->setCode($payoutOptionData[Gpf_Db_Table_FormFields::CODE]);
            $payoutField->loadFromData(array(Gpf_Db_Table_FormFields::FORMID, Gpf_Db_Table_FormFields::CODE));

            $userPayoutOption = new Pap_Db_UserPayoutOption();
            $userPayoutOption->setUserId($payoutOptionData[Pap_Db_Table_UserPayoutOptions::USERID]);
            $userPayoutOption->setFormFieldId($payoutField->getId());
            $userPayoutOption->setValue($payoutOptionData[Pap_Db_Table_UserPayoutOptions::VALUE]);
            $userPayoutOption->save();
            $this->incrementSuccessfulCount();
            $this->logger->debug('Data from line: ' . $this->getFile()->getActualCSVLineNumber() . ' was inserted.');
        } catch (Gpf_Exception $e) {
        	$this->logError('Payout option', $row, $e);
        	$this->appendOutputMessage($this->getSaveErrorMessage($this->getFile()->getActualCSVLineNumber(), $e->getMessage()));
            $this->incrementWrongCount();
        }
    }

    protected function createNewAffiliate(array $headerArray, array $row) {
        $affiliate = new Pap_Affiliates_User();
        $affiliate->setSendNotification(false);
         
        $this->setDbRowData($affiliate, $row, $headerArray, $this->getPapUserColumnsWithoutParentUserId());
        $this->setDbRowData($affiliate, $row, $headerArray, $this->getGpfUserColumns());
        $this->setDbRowData($affiliate, $row, $headerArray, $this->getAuthUserColumns());
        
        if($affiliate->getDeleted() == "") {
            $affiliate->setDeleted(false); 
        }
        
        try {
            $affiliate->save();
            $this->logger->debug('New affiliate was created');
        } catch (Gpf_Exception $e) {
        	$this->logError('Affiliate3', $row, $e);
        	throw new Gpf_Exception($e->getMessage());
        }
    }

    private function  setDbRowPrimaryKey($dbRowObject, array $row, array $headerArray, $indexFieldName) {
        for ($i = 0; $i < count($row); $i++) {
            if ($headerArray[$i] == $indexFieldName) {
                $dbRowObject->setId($row[$i]);
                return;
            }
        }
        throw new Gpf_Exception($this->_('Can not locate primary key item for ' . $dbRowObject . ' in CSV file on row: ' . implode(',', $row)));        
    }

    protected function setDbRowData($dbRowObject, array $row, array $headerArray, array $dbRowArray) {
        for ($i = 0; $i < count($row); $i++) {
            if (array_key_exists($headerArray[$i], $dbRowArray)) {
                if($dbRowArray[$headerArray[$i]] == Pap_Db_Table_Users::DELETED && $row[$i] == '') {
                    $row[$i] = Gpf::NO;
                }
                $dbRowObject->set($dbRowArray[$headerArray[$i]], $row[$i]);
            }
        }
    }

    private function getAllAffiliateColumns() {
        $allColumns = array();
         
        foreach ($this->getPapUserColumns() as $key => $value) {
            $allColumns[$key] = $value;
        }
         
        foreach ($this->getGpfUserColumns() as $key => $value) {
            $allColumns[$key] = $value;
        }

        foreach ($this->getAuthUserColumns() as $key => $value) {
            $allColumns[$key] = $value;
        }

        return $allColumns;
    }

    private function getPapUserColumnsWithoutParentUserId() {
        if ($this->papUserColumnsWithoutParentUserId === null) {
            $this->papUserColumnsWithoutParentUserId = $this->getPapUserColumns();
            unset($this->papUserColumnsWithoutParentUserId[$this->encodeColumn(Pap_Db_Table_Users::PARENTUSERID)]);
        }
        return $this->papUserColumnsWithoutParentUserId;
    }
    
    protected function loadFromTask() {
        parent::loadFromTask();
        $json = new Gpf_Rpc_Json();
        $values = $json->decode($this->getParams());
        $this->paramsArray['papUserIds'] = $values->papUserIds;
    }
}
?>
