<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ViewService.class.php 24612 2009-06-11 13:28:02Z aharsani $
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
class Pap_Common_ViewServiceAffiliateEdit extends Gpf_View_ViewService {
    
    protected function getUserId() {
        return '';
    }
    
    /**
     * @service grid_view write
     *
     * @param $Id
     * @param $gridcode
     * @param $name
     * @param $rowsperpage
     * @param $columns
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $viewId = $form->getFieldValue('Id');

        $view = $this->getView($viewId);
        try {
            $form->fill($view);
            $this->saveView($view, $form);
        } catch (Exception $e) {
            $form->setErrorMessage($this->_("Error while saving ") . $form->getFieldValue('name') . " (" . $e->getMessage() . ")");
        }

        return $form;
    }
}
?>
