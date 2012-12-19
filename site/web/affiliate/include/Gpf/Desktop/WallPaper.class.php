<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: WindowManager.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Desktop_WallPaper extends Gpf_File_DbUpload {

    const WALLPAPER = 'wallpaper';
    const WALLPAPER_POSITION = 'wallpaperPosition';
    const WALLPAPER_TYPE = 'wallpaperType';
    const BACKGROUND_COLOR = 'backgroundColor';
    const CUSTOM_URL = 'customUrl';
    const CUSTOM_COLOR = 'customColor';

    const TYPE_NONE = 'N';
    const TYPE_CUSTOM = 'C';
    const TYPE_THEME = 'T';

    public function __construct() {
        parent::__construct();
        $this->setAllowedFileExtensions(array('jpg', 'jpeg', 'gif', 'png'));
    }

    /**
     *
     * @return Gpf_Db_File
     */
    protected function saveUploadedFile() {
        $file = parent::saveUploadedFile();
        $file->addReference();
        $wallpaper = new Gpf_Db_Wallpaper();
        $wallpaper->setAccountUserId(Gpf_Session::getAuthUser()->getAccountUserId());
        $wallpaper->setFileId($file->getFileId());
        $wallpaper->setName($file->getFileName());
        $wallpaper->insert();
        return $file;
    }

    /**
     * @service wallpaper delete
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Action
     */
    public function delete(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('Failed to delete %s wallpaper(s)'));
        $action->setInfoMessage($this->_('%s wallpaper(s) successfully deleted'));

        foreach ($action->getIds() as $id) {
            try {
                $row = new Gpf_Db_Wallpaper();
                $row->setPrimaryKeyValue($id);
                $row->delete();
                $action->addOk();
            } catch (Exception $e) {
                $action->addError();
            }
        }
        return $action;
    }

    /**
     * @service wallpaper add
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Action
     */
    public function addUrlWallpaper(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setInfoMessage("Wallpaper added");

        $url = $action->getParam('url');
        $name = basename($url);

        $wallpaper = new Gpf_Db_Wallpaper();
        $wallpaper->setAccountUserId(Gpf_Session::getAuthUser()->getAccountUserId());
        $wallpaper->setName($name);
        $wallpaper->setUrl($url);
        $wallpaper->insert();

        $action->addOk();
        return $action;
    }

    /**
     * Load Wallpaper settings to form
     *
     * @service wallpaper read
     * @param Gpf_Rpc_Params $params
     */
    public function load(Gpf_Rpc_Params $params) {
        return $this->loadNoRpc();
    }

    /**
     * Load Wallpaper settings to form
     */
    public function loadNoRpc() {
        $form = new Gpf_Rpc_Form();
        $form->addField(self::WALLPAPER, '');
        $form->addField(self::WALLPAPER_POSITION, 'S');
        $form->addField(self::WALLPAPER_TYPE, 'N');
        $form->addField(self::BACKGROUND_COLOR, '#000000');

        try {
            $attributes = Gpf_Db_UserAttribute::getSettingsForGroupOfUsers(
            array(self::WALLPAPER, self::WALLPAPER_TYPE, self::WALLPAPER_POSITION, self::BACKGROUND_COLOR),
            array(Gpf_Session::getInstance()->getAuthUser()->getAccountUserId()));

            if (isset($attributes[Gpf_Session::getInstance()->getAuthUser()->getAccountUserId()])) {
                $attributes = $attributes[Gpf_Session::getInstance()->getAuthUser()->getAccountUserId()];
                if (isset($attributes[self::WALLPAPER])) {
                    $form->setField(self::WALLPAPER, $attributes[self::WALLPAPER]);
                }
                if (isset($attributes[self::WALLPAPER_TYPE])) {
                    $form->setField(self::WALLPAPER_TYPE, $attributes[self::WALLPAPER_TYPE]);
                }
                if (isset($attributes[self::WALLPAPER_POSITION])) {
                    $form->setField(self::WALLPAPER_POSITION, $attributes[self::WALLPAPER_POSITION]);
                }
                if (isset($attributes[self::BACKGROUND_COLOR])) {
                    $form->setField(self::BACKGROUND_COLOR, $attributes[self::BACKGROUND_COLOR]);
                }
            }
        } catch (Gpf_Exception $e) {
            $form->setErrorMessage($e->getMessage() . ' ' . $e->getLine());
        }
        return $form;
    }

    /**
     * Save Wallpaper settings from form
     *
     * @service wallpaper write
     * @param Gpf_Rpc_Params $params
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        try {
            $type = $form->getFieldValue(self::WALLPAPER_TYPE);
            $wallpaper = $form->getFieldValue(self::WALLPAPER);
            if ($type == self::TYPE_THEME) {
                $wallpaper = ltrim($wallpaper, self::TYPE_THEME);
            } else if ($type == self::TYPE_CUSTOM) {
                $wallpaper = ltrim($wallpaper, self::TYPE_CUSTOM);
            }
            Gpf_Db_UserAttribute::saveAttribute(self::WALLPAPER_TYPE, $type);
            Gpf_Db_UserAttribute::saveAttribute(self::WALLPAPER, $wallpaper);
            Gpf_Db_UserAttribute::saveAttribute(self::WALLPAPER_POSITION, $form->getFieldValue(self::WALLPAPER_POSITION));
            Gpf_Db_UserAttribute::saveAttribute(self::BACKGROUND_COLOR, $form->getFieldValue(self::BACKGROUND_COLOR));
        } catch (Gpf_Exception $e) {
            $form->setErrorMessage($this->_('Failed to save wallpaper with error %s', $e->getMessage()));
            return $form;
        }

        $form->setInfoMessage($this->_('Wallpaper saved.'));

        return $form;
    }

    /**
     * Save Wallpaper settings from form
     *
     * @service wallpaper add
     * @param Gpf_Rpc_Params $params
     */
    public function add(Gpf_Rpc_Params $params) {
        return $this->save($params);
    }


    /**
     * Get recordset of all wallpapers in directory {account_dir}/wallpapers/
     *
     * @return Gpf_Data_RecordSet
     */
    public function getAllWallpapersNoRpc() {
        return Gpf_Db_Table_Wallpapers::getInstance()->getAllWallpapers();
    }

    /**
     * Get recordset of default theme wallpapers
     *
     * @return Gpf_Data_RecordSet
     */
    public function getDefaultThemeWallpaperNoRpc() {
        $response = new Gpf_Data_RecordSet();
        $response->setHeader(array("filename", "name", "url"));

        foreach (Gpf_Paths::getInstance()->getThemeWallPaperDirPaths() as $dirName) {
            $dir = new Gpf_Io_DirectoryIterator($dirName);
            foreach ($dir as $fullName => $file) {

                $info = pathinfo($file);

                $response->add(array($file, str_replace('_', ' ', $info['filename']),
                Gpf_Paths::getInstance()->getResourceUrl($file, Gpf_Paths::IMAGE_DIR . Gpf_Paths::WALLPAPER_DIR)
                ));
            }
        }

        return $response;
    }

    /**
     * Get recordset of all wallpapers
     *
     * @service wallpaper read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Data_RecordSet
     */
    public function getAllWallpapers(Gpf_Rpc_Params $params) {
        return $this->getAllWallpapersNoRpc();
    }

    /**
     * Get recordset of default theme wallpapers
     *
     * @service wallpaper read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Data_RecordSet
     */
    public function getDefaultThemeWallpaper(Gpf_Rpc_Params $params) {
        return $this->getDefaultThemeWallpaperNoRpc();
    }

    /**
     * Get recordset of all selected wallpaper
     *
     * @service wallpaper read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Data_RecordSet
     */
    public function loadSelectedWallpaper(Gpf_Rpc_Params $params) {
        return $this->loadSelectedWallpaperNoRpc();
    }

    /**
     * Get recordset of all selected wallpaper
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Data_RecordSet
     */
    public function loadSelectedWallpaperNoRpc() {
        $response = new Gpf_Data_RecordSet();
        $response->addColumn(self::WALLPAPER_TYPE);
        $response->addColumn(self::WALLPAPER_POSITION);
        $response->addColumn(self::BACKGROUND_COLOR);
        $response->addColumn("fileId");
        $response->addColumn("url");

        $record = $response->createRecord();
        try {
            Gpf_Db_Table_UserAttributes::getInstance()->loadAttributes(Gpf_Session::getAuthUser()->getAccountUserId());

            $wallpaperType = Gpf_Db_Table_UserAttributes::getInstance()->getAttributeWithDefaultValue(
            self::WALLPAPER_TYPE, self::TYPE_THEME);

            $theme = new Gpf_Desktop_Theme();

            $wallpaperId = Gpf_Db_Table_UserAttributes::getInstance()->getAttributeWithDefaultValue(
            self::WALLPAPER, $theme->getDefaultWallpaper());

            $position = Gpf_Db_Table_UserAttributes::getInstance()->getAttributeWithDefaultValue(
            self::WALLPAPER_POSITION, $theme->getDefaultWallpaperPosition());

            $color = Gpf_Db_Table_UserAttributes::getInstance()->getAttributeWithDefaultValue(
            self::BACKGROUND_COLOR, $theme->getDefaultBackgroundColor());

            $record->set(self::WALLPAPER_POSITION, $position);
            $record->set(self::BACKGROUND_COLOR, $color);
            $record->set(self::WALLPAPER_TYPE, $wallpaperType);

            if ($wallpaperType == self::TYPE_THEME) {
                $record->set("url", Gpf_Paths::getInstance()->getResourceUrl($wallpaperId,
                Gpf_Paths::IMAGE_DIR . Gpf_Paths::WALLPAPER_DIR));
            } else if ($wallpaperType == self::TYPE_CUSTOM) {
                $wallpaper = new Gpf_Db_Wallpaper();
                $wallpaper->setPrimaryKeyValue($wallpaperId);
                $wallpaper->load();
                $record->set("fileId", $wallpaper->getFileId());
                $record->set("url", $wallpaper->getUrl());
            }
        } catch (Gpf_Exception $e) {
            $theme = new Gpf_Desktop_Theme();
            $record->set(self::WALLPAPER_POSITION, "S");
            $record->set(self::BACKGROUND_COLOR, "#000000");
            $record->set(self::WALLPAPER_TYPE, self::TYPE_NONE);
            $record->set("fileId", null);
            $record->set("url", "");
        }
        $response->addRecord($record);

        return $response;
    }
}
