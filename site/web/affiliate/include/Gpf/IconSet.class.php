<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id:
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
class Gpf_IconSet extends Gpf_Object {
    private $icons;

    public function __construct() {
        $icons = array();
        $this->initIcons();
    }

    /**
     * @anonym
     * @service
     */
    public function getAllIcons(Gpf_Rpc_Params $params) {
        return $this->getAllIconsNoRpc();
    }

    public function getAllIconsNoRpc() {
        $response = new Gpf_Data_RecordSet();
        $response->setHeader(array("iconName",
                                   "smallIcon",
                                   "middleIcon",
                                   "bigIcon"));
        foreach ($this->icons as $iconName => $imageFile) {
            try {
                $response->add(array($iconName,
                $this->getImageUrl($imageFile, "small"),
                $this->getImageUrl($imageFile, "middle"),
                $this->getImageUrl($imageFile, "big")));
            } catch (Exception $e) {
                //TODO: add default image !
            }
        }
        return $response;
    }

    protected function getImageUrl($imageFile, $size) {
        return Gpf_Paths::getInstance()->getImageUrl($imageFile['imageName']."-".$size.".".$imageFile['extension']);
    }

    /**
     * @param string $iconName
     * @param string $imageName name of image file without extension and size suffix (-small, -middle, -big)
     * @param string $imageExtension extension of image file (default value is 'png');
     */
    public function addIcon($iconName, $imageName, $imageExtension = 'png') {
        $this->icons[$iconName] = array('imageName' => $imageName, 'extension' => $imageExtension);
    }

    /**
     * Init list of icons
     *
     */
    protected function initIcons() {
        $this->addIcon("Dialog", "icon-dialog");
        $this->addIcon("Content", "icon-communication");
        $this->addIcon("Message", "icon-message");
        $this->addIcon("Help", "icon-help");
        $this->addIcon("SystemConfiguration", "icon-systemconfiguration");
        $this->addIcon("ShowDesktop", "icon-showdesktop");
        $this->addIcon("Logout", "icon-logout");
        $this->addIcon("UserProfile", "icon-userprofile");
        $this->addIcon("OnlineUsers", "icon-onlineusers");
        $this->addIcon("ConfirmationRequired", "icon-confirmationrequired");
        $this->addIcon("AddGadget", "icon-addgadget");
        $this->addIcon("Import", "icon-import");
        $this->addIcon("Export", "icon-export");
        $this->addIcon("ExportFiles", "icon-exportfiles");
        $this->addIcon("ImportExport", "icon-importexport");
        $this->addIcon("Information", 'icon-informations');
        $this->addIcon('PanelSettings', 'icon-panelsettings');
        $this->addIcon('Plugins', 'icon-plugins');
        $this->addIcon('Features', 'icon-features');
        $this->addIcon('Newsletters', 'icon-newsletter');
        $this->addIcon('TutorialVideo', 'icon-tutorialvideo');
        $this->addIcon('UserRolePrivileges', 'icon-userroleprivileges');
        $this->addIcon('Admins', 'icon-admins');
        $this->addIcon("PendingBackgroundTasks", "icon-systemconfiguration");
    }
}

?>
