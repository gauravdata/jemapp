<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: GadgetManager.class.php 26315 2009-11-29 17:02:26Z vzeman $
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
class Gpf_GadgetManager extends Gpf_Object {

    /**
     * @var Gpf_Gadget_Factory
     */
    private $gadgetFactory;

    function __construct() {
        $this->gadgetFactory = new Gpf_Gadget_Factory();
    }

    /**
     * @service gadget read
     */
    public function renderGadget(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $gadgetId = $form->getFieldValue("gadgetId");
        $gadgetFactory = new Gpf_Gadget_Factory();
        $gadget = $gadgetFactory->getGadget($gadgetId);
        try {
            $panelWidth = $form->getFieldValue("panelWidth");
        } catch (Gpf_Data_RecordSetNoRowException $e) {
            $panelWidth = $gadget->getWidth();
        }
        try {
            $panelHeight = $form->getFieldValue("panelHeight");
        } catch (Gpf_Data_RecordSetNoRowException $e) {
            $panelHeight = $gadget->getHeight();
        }
        $gadget->setPanelSize($panelWidth, $panelHeight);

        return $gadget;
    }

    /**
     * @service gadget read
     */
    public function getGadgets(Gpf_Rpc_Params $params) {
        $gadgetId = $params->get("gadgetId");
        return $this->getGadgetsNoRpc($gadgetId);
    }

    /**
     * @return Gpf_Data_RecordSet
     */
    public function getGadgetsNoRpc($gadgetId = null) {
        return Gpf_Db_Table_Gadgets::getGadgets(Gpf_Session::getAuthUser()->getAccountUserId(), $gadgetId);
    }

    /**
     * @service gadget write
     * @return Gpf_Rpc_Action
     */
    public function saveGadgets(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setInfoMessage($this->_('Gadgets saved'));

        $gadgets = new Gpf_Data_RecordSet();
        $gadgets->loadFromArray($action->getParam('gadgets'));

        foreach ($gadgets as $gadgetRecord) {
            $gadget = new Gpf_Db_Gadget();
            $gadget->setPrimaryKeyValue($gadgetRecord->get("gadgetid"));
            try {
                $gadget->load();
            } catch (Gpf_Db_NoRowException $e) {
            }
            $gadget->fillFromRecord($gadgetRecord);
            $gadget->set('accountuserid', Gpf_Session::getAuthUser()->getAccountUserId());
            $gadget->save();
        }
        $action->addOk();
        return $action;
    }

    /**
     * @param string $name
     * @param string $url
     * @param string $accountUserId
     * @return Gpf_Gadget
     */
    public function addGadgetNoRpc($name, $url, $positionType, $accountUserId = null) {
        if ($accountUserId == null) {
            $accountUserId = Gpf_Session::getAuthUser()->getAccountUserId();
        }
        $gadget = $this->gadgetFactory->downloadGadget($url);
        $gadget->setName($name);
        $gadget->setUrl($url);
        $gadget->setAccountUserId($accountUserId);
        $gadget->setPositionType($positionType);
        $gadget->setPositionTop(40);
        $gadget->setPositionLeft(40);
        $gadget->insert();
        $gadget->savePreferenceFormFields();
        return $gadget;
    }
}
