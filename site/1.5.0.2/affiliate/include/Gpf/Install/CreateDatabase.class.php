<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
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
class Gpf_Install_CreateDatabase extends Gpf_Install_Step {
    const DB_USERNAME = 'Username';
    const DB_PASSWORD = 'Password';
    const DB_HOSTNAME = 'Hostname';
    const DB_NAME = 'Dbname';
    
    public function __construct() {
        parent::__construct();
        $this->code = 'Create-Database';
        $this->name = $this->_('Create Database'); 
    }
    
    public function create() {
        $createDatabaseTask = new Gpf_Install_CreateDatabaseTask();
        try {
            $createDatabaseTask->run();
        } catch (Gpf_Tasks_LongTaskInterrupt $e) {
            throw new Gpf_Exception($this->_('Instalation was interrupted because maximal execution time was exceeded. Please refresh the browser, process will continue'));
        } 
    }
    
    /**
     * @anonym 
     * @param Gpf_Rpc_Params $params
     * @service
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        try {
            $db = $this->createDatabase();
            $db->connect();
            $form->setField(self::DB_HOSTNAME, $db->getHostname());
            $form->setField(self::DB_USERNAME, $db->getUsername());
            $form->setField(self::DB_PASSWORD, '*****');
            $form->setField(self::DB_NAME, $db->getDbname());
        } catch (Exception $e) {
        } 
        
        return $form;
    }
    
    protected function execute(Gpf_Rpc_Params $params) {
    	$form = new Gpf_Rpc_Form($params);
    	$form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Gpf_Install_CreateDatabase::DB_HOSTNAME, $this->_('Database Hostname'));
		$form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Gpf_Install_CreateDatabase::DB_USERNAME, $this->_('Username'));
		$form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Gpf_Install_CreateDatabase::DB_NAME, $this->_('Database Name'));
		if (!$form->validate()) {
			return $form;
		}
			
        $createDatabaseTask = new Gpf_Install_CreateDatabaseTask();
        $createDatabaseTask->setDBSettings($form->getFieldValue(Gpf_Install_CreateDatabase::DB_HOSTNAME),
        								   $form->getFieldValue(Gpf_Install_CreateDatabase::DB_USERNAME), 
        								   $form->getFieldValue(Gpf_Install_CreateDatabase::DB_NAME),
        								   $form->getFieldValue(Gpf_Install_CreateDatabase::DB_PASSWORD));
        try {
            $createDatabaseTask->run();
        } catch (Gpf_Tasks_LongTaskInterrupt $e) {
            $this->setResponseType($form, self::PART_DONE_TYPE);
            $form->setInfoMessage($e->getMessage());
            return $form;
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }
        $this->setNextStep($form);
        return $form;
    }
}
?>
