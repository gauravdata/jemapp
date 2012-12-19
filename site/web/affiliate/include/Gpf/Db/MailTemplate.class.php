<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MailTemplate.class.php 28892 2010-07-26 09:02:20Z iivanco $
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
class Gpf_Db_MailTemplate extends Gpf_DbEngine_Row {

    private $uploadedFiles = false;

    private $includedImageFileIds = array();

    private static $setMethodMap = array(
    Gpf_Db_Table_MailTemplates::BODY_HTML=>'setBodyHtml',
    Gpf_Db_Table_MailTemplates::BODY_TEXT=>'setBodyText',
    Gpf_Db_Table_MailTemplates::SUBJECT=>'setSubject',
	'Id'=>'setId',
    'uploadedFiles'=>'setUploadedFiles');

    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_MailTemplates::getInstance());
        parent::init();
    }

    public function setId($id) {
        $this->set(Gpf_Db_Table_MailTemplates::ID, $id);
    }

    public function getId() {
        return $this->get(Gpf_Db_Table_MailTemplates::ID);
    }

    public static function getSetMethodMap() {
        return self::$setMethodMap;
    }

    /**
     * @param string $name php class name used for rendering of mail template
     */
    public function setClassName($name) {
        $this->set(Gpf_Db_Table_MailTemplates::CLASS_NAME, $name);
    }

    /**
     * @param string $name Name of template displayed to user
     */
    public function setTemplateName($name) {
        $this->set(Gpf_Db_Table_MailTemplates::TEMPLATE_NAME, $name);
    }

    /**
     * @param boolean $isCustom True/False if is customer defined mail template
     */
    public function setIsCustom($isCustom) {
        if ($isCustom == Gpf::YES || $isCustom === true) {
            $this->set(Gpf_Db_Table_MailTemplates::IS_CUSTOM, Gpf::YES);
        } else {
            $this->set(Gpf_Db_Table_MailTemplates::IS_CUSTOM, Gpf::NO);
        }
    }

    /**
     * Return boolean value if is template custom or system
     *
     * @return boolean
     */
    public function isCustom() {
        return $this->getIsCustom() == Gpf::YES;
    }

    /**
     * Return real value of is_custom field
     *
     * @return string
     */
    public function getIsCustom() {
        return $this->get(Gpf_Db_Table_MailTemplates::IS_CUSTOM);
    }

    public function setSubject($subject) {
        $this->set(Gpf_Db_Table_MailTemplates::SUBJECT, $subject);
    }

    public function setBodyHtml($body, $process = true) {
    	if (!$process) {
    		$this->set(Gpf_Db_Table_MailTemplates::BODY_HTML, $body);
    		return;
    	}
    	
        $body = $this->replaceAngleBracketsEntities($body);

        $this->set(Gpf_Db_Table_MailTemplates::BODY_HTML, $this->normalizeIncludedImages($body));

        $this->setIncludedFileIds($body);
    }


    public function setBodyHtmlOnPreviewImages() {
        $this->set(Gpf_Db_Table_MailTemplates::BODY_HTML,
        $this->previewIncludedImages($this->get(Gpf_Db_Table_MailTemplates::BODY_HTML)));
    }

    public static function getIncludedImageFileIds($body) {
        $fileIds = array();
        if (preg_match_all('/_gpf_src=[\'"](.*?)[\'"]/ms', $body, $matches)) {
            $fileIds = array_unique($matches[1]);
        }
        return $fileIds;
    }

    /**
     * @param $body Html body of mail
     * @return Gpf_Data_RecordSet
     */
    protected function getIncludedFilesRecordset($body) {
        $fileIds = $this->includedImageFileIds;
        if (count($fileIds) == 0) {
            return new Gpf_Data_RecordSet();
        }
        $sql = new Gpf_SqlBuilder_SelectBuilder();
        $sql->select->addAll(Gpf_Db_Table_Files::getInstance());
        $sql->from->add(Gpf_Db_Table_Files::getName());
        $sql->where->add(Gpf_Db_Table_Files::ID, 'IN', $fileIds);
        try {
            return $sql->getAllRows();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return new Gpf_Data_RecordSet();
        }
    }

    protected function normalizeIncludedImages($body) {
        $pattern = array();
        $replacement = array();
        foreach ($this->getIncludedFilesRecordset($body) as $record) {
            $pattern[] = sprintf('/\ssrc=[\'"][^"]*?%s[^"\']*?[\'"]/ms', $record->get(Gpf_Db_Table_Files::ID));
            $replacement[] = sprintf(' src="%s"', $record->get(Gpf_Db_Table_Files::FILE_NAME));
        }

        return preg_replace($pattern, $replacement, $body);
    }

    public function previewIncludedImages($body) {
        $pattern = array();
        $replacement = array();
        foreach ($this->getIncludedFilesRecordset($body) as $record) {
            $url = Gpf_Paths::getInstance()->getFullScriptsUrl() .
            'server.php?C=Gpf_File_Download&amp;M=download&amp;S=' . Gpf_Session::getInstance()->getId() .
            '&amp;FormRequest=Y&amp;FormResponse=Y&amp;fileid=' . $record->get(Gpf_Db_Table_Files::ID).
            '&amp;attachment=N';
            $pattern[] = sprintf('/<img([^>]*?) src=[\'"][^"\']*?[\'"]([^>]*?)_gpf_src=[\'"]%s[\'"]([^>]*?)>/ms', $record->get(Gpf_Db_Table_Files::ID));
            $replacement[] = sprintf('<img$1 src="%s"$2_gpf_src="%s"$3>',$url, $record->get(Gpf_Db_Table_Files::ID));
        }

        return preg_replace($pattern, $replacement, $body);
    }


    public function setBodyText($body) {
        $this->set(Gpf_Db_Table_MailTemplates::BODY_TEXT, $body);
    }

    public function getSubject() {
        return $this->get(Gpf_Db_Table_MailTemplates::SUBJECT);
    }

    public function getBodyHtml() {
        return $this->get(Gpf_Db_Table_MailTemplates::BODY_HTML);
    }

    public function getBodyText() {
        return $this->get(Gpf_Db_Table_MailTemplates::BODY_TEXT);
    }

    public function setAccountId($accountId) {
        $this->set(Gpf_Db_Table_MailTemplates::ACCOUNT_ID, $accountId);
    }

    public function getAccountId() {
        return $this->get(Gpf_Db_Table_MailTemplates::ACCOUNT_ID);
    }
    
    public function setUserId($userId) {
        $this->set(Gpf_Db_Table_MailTemplates::USERID, $userId);
    }

    public function insert() {
        try {
            $this->setIsCustom($this->isCustom());
        } catch (Gpf_Exception $e) {
            $this->setIsCustom(false);
        }

        if (!strlen($this->getAccountId())) {
            $this->setAccountId(Gpf_Session::getInstance()->getAuthUser()->getAccountId());
        }

        $this->setCreated(Gpf_Common_DateUtils::now());
        return parent::insert();
    }

    public function setCreated($datetime) {
        $this->set(Gpf_Db_Table_MailTemplates::CREATED, $datetime);
    }

    public function getCreated() {
        return $this->get(Gpf_Db_Table_MailTemplates::CREATED);
    }

    public function setUploadedFiles($uploadedFiles) {
        $this->uploadedFiles = $uploadedFiles;
    }

    /**
     * @throws Gpf_Templates_SmartySyntaxException
     */
    public function update($updateColumns = array()) {
        parent::update($updateColumns);
        $this->processAttachedFiles();
    }

    protected function setIncludedFileIds($body) {
        $this->includedImageFileIds = Gpf_Db_MailTemplate::getIncludedImageFileIds($body);
    }

    protected function processAttachedFiles() {
        if ($this->uploadedFiles === false) {
            return;
        }

        //delete all old attached files
        $this->deleteAllOldAtachments($this->getId(), false);

        if ($this->uploadedFiles == null) {
            return;
        }

        $this->insertNewAttachedFilesIntoDb();
    }

    protected function deleteAllOldAtachments($templateId = false, $fileId = false) {
        Gpf_Db_Table_MailTemplateAttachments::getInstance()->deleteAll($templateId, $fileId);
    }

    protected function insertNewAttachedFilesIntoDb() {
        $attachedFiles = explode(',',$this->uploadedFiles);
        foreach ($attachedFiles as $attachedFileId) {
            $attachedFileId = trim($attachedFileId);
            if (strlen($attachedFileId)) {
                $fileAtt = new Gpf_Db_MailTemplateAttachment();
                $fileAtt->setTemplateId($this->getId());
                $fileAtt->setFileId($attachedFileId);
                $fileAtt->setIsIncludedImage(in_array($attachedFileId, $this->includedImageFileIds));
                $fileAtt->insert();
            }
        }
    }

    protected function replaceAngleBracketsEntities($text) {
        preg_match_all($this->getCurlyBracesRegExp(), $text, $matches);

        foreach ($matches[0] as $match) {
            $new = str_replace('&gt;', '>', $match);
            $new = str_replace('&lt;', '<', $new);
            if ($new != $match) {
                $text = str_replace($match, $new, $text);
            }
        }

        return $text;
    }

    private function getCurlyBracesRegExp() {
        return '/\{[^\}]*\}/';
    }
}

?>
