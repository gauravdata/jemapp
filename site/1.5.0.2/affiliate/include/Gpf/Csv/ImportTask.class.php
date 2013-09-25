<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
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
 * @package PostAffiliatePro
 */
class Gpf_Csv_ImportTask extends Gpf_Tasks_LongTask {

    /**
     * @var Gpf_Rpc_Form
     */
    private $form;
    private $delimiter;
    private $codes;
    /**
     * @var Gpf_Data_RecordSet
     */
    private $importObjects;
    private $logger;

    private $outputMessage;

    public function __construct(Gpf_Rpc_Form $form) {
        $this->form = $form;
        $this->logger = Gpf_Log_Logger::getInstance();
		$this->logger->add(Gpf_Log_LoggerDatabase::TYPE, Gpf_Log::DEBUG);
		$this->outputMessage = '';
    }

    public function getName() {
        return $this->_('Import');
    }
    
    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_USER;
    }

    protected function execute() {
    	$this->logger->debug('IMPORT STARTED');
        $this->delimiter = Gpf_Csv_ImportExportService::getDelimiter(($this->form->getFieldValue("delimiter")));
        $this->codes = preg_split("/,/", $this->form->getFieldValue("dropModules"));
        $this->importObjects = Gpf_Db_ImportExport::getImportExportObjects();
        $fileUrl = $this->form->getFieldValue("fileName");

        if ($this->isPending($fileUrl, $this->_('Check file'))) {
            $this->checkFile($fileUrl);
        }
        $this->sendToImportObjects($fileUrl);
        $this->logger->debug('IMPORT ENDED');
    }

    /**
     * @throws Gpf_Exception
     * @param $fileUrl
     */
    private function checkFile($fileUrl) {
        $file = new Gpf_Io_File($fileUrl);
        if (!$file->isExists() && !Gpf_Common_UrlUtils::urlExists($fileUrl)) {
            throw new Gpf_Exception($this->_('File %s not exists', $fileUrl));
        }
    }

    private function sendToImportObjects($fileUrl) {
        foreach ($this->importObjects as $importObject) {
            if ($this->isPending($importObject->get(Gpf_Db_Table_ImportExports::CODE),
            $this->_('Import') . ' ' . $this->_localize($importObject->get(Gpf_Db_Table_ImportExports::NAME)))) {
            	$this->logger->debug(strtoupper($this->_localize($importObject->get(Gpf_Db_Table_ImportExports::NAME))) . ' STARTED');
                $objectImportExport = Gpf::newObj($importObject->get(Gpf_Db_Table_ImportExports::CLASS_NAME));
                $params = new Gpf_Rpc_Params();
                $params->add("fileUrl", $fileUrl);
                $params->add("delimiter", $this->delimiter);
                $params->add('startTime', $this->getStartTime());
                if (in_array($importObject->get(Gpf_Db_Table_ImportExports::CODE), $this->codes)) {
                    $params->add("delete", 'Y');
                }

                try {
                    $objectImportExport->importData($params);
                } catch (Gpf_Tasks_LongTaskInterrupt $e) {
                	$this->logger->debug('IMPORT INTERRUPTED');
                	$this->createOutputMessage($importObject, $objectImportExport->getOutputMessage());
                    $this->interrupt();
                } catch (Exception $e) {
                	$this->logger->error($e->getMessage());
                	$this->createOutputMessage($importObject, $objectImportExport->getOutputMessage());
                    $objectImportExport->forceFinishTask();
                    throw $e;
                }
                $this->logger->debug(strtoupper($this->_localize($importObject->get(Gpf_Db_Table_ImportExports::NAME))) . ' ENDED');
                $this->createOutputMessage($importObject, $objectImportExport->getOutputMessage());
                $this->setDone();
            }
        }
    }

    private function createOutputMessage($importObject, $outputMessage) {
        if ($outputMessage != '') {
            $this->outputMessage .= '<br />';
            $this->outputMessage .= '<span style="font-weight:bold">'.strtoupper($this->_localize($importObject->get(Gpf_Db_Table_ImportExports::NAME))). ' IMPORTING:</span>';
            $this->outputMessage .= '<br />' . $outputMessage . '<br />'; 
        }
    }
    
    public function getOutputMessage() {
        return $this->outputMessage;
    }
}
