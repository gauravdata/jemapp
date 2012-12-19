<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Matej Kendera
 *   @since Version 1.0.0
 *   $Id: TransactionsForm.class.php 30880 2011-01-27 14:13:54Z mkendera $
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
class Pap_Merchants_Tools_ViewsForm extends Gpf_Object {

    /**
     * @service views write
     *
     * @param $id
     * @return Gpf_Rpc_Action
     */
    public function setDefaultView(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_("Error changing default view"));
        $action->setInfoMessage($this->_("Default view changed"));

        try {
            $viewType = $action->getParam('id');
            $activeView = new Gpf_Db_ActiveView();
            $activeView->setViewType($viewType);
            $activeView->setAccountUserId(Gpf_Session::getAuthUser()->getUserData()->get(Gpf_Db_Table_Users::ID));
            $activeView->loadFromData(array(Gpf_Db_Table_ActiveViews::VIEWTYPE, Gpf_Db_Table_ActiveViews::ACCOUNTUSERID));

            if ($activeView->getActiveViewId() != Gpf_View_ViewService::DEFAULT_VIEW_ID) {
                $activeView->setActiveViewId(Gpf_View_ViewService::DEFAULT_VIEW_ID);
                $activeView->save();
            }
            $action->addOk();
        } catch (Exception $e) {
            $action->addError();
        }

        return $action;
    }
}

?>
