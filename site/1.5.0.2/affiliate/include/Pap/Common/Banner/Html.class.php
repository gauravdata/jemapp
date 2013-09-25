<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
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

class Pap_Common_Banner_Html extends Pap_Common_Banner {

    protected function getBannerCode(Pap_Common_User $user, $flags) {
        $description = $this->getDescription($user);

        $description = $this->replaceUserConstants($description, $user);
        $description = $this->replaceUrlConstants($description, $user, $flags, '');
        $description = $this->replaceBannerConstants($description, $user);
        
        if($this->getData3() == 'N') {
            $description = preg_replace("/\<script.*?\<\/script\>/", '', $description);
        }
        return $description;
    }

    public function getPreview(Pap_Common_User $user) {
        if($this->getData3() == 'I') {
            return "<img src=" . $this->getData5() . " alt=\"\" />";
        }
        return parent::getPreview($user);
    }

    public function fillForm(Gpf_Rpc_Form $form) {
        $form->load($this);
        $this->fillIframeSize($form);

    }

    public function encodeSize(Gpf_Rpc_Form $form, $sizeFieldName) {
        parent::encodeSize($form, $sizeFieldName);
        if ($form->existsField(Pap_Db_Table_Banners::DATA4)) {
            parent::encodeSize($form, Pap_Db_Table_Banners::DATA4);
        }
    }

    private function fillIframeSize(Gpf_Rpc_Form $form) {
        $form->setField(Pap_Db_Table_Banners::DATA4, $this->getSizeType(Pap_Db_Table_Banners::DATA4));
        $sizeArray = explode('x', substr($this->getData4(), 1));
        if (count($sizeArray) < 2) {
            return;
        }

        if ($form->getFieldValue(Pap_Db_Table_Banners::DATA4) == Pap_Db_Banner::SIZE_PREDEFINED) {
            $form->setField('size_predefined', $sizeArray[0].'x'.$sizeArray[1]);
            return;
        }
        	
        if ($form->getFieldValue(Pap_Db_Table_Banners::DATA4) == Pap_Db_Banner::SIZE_OWN) {
            $form->setField('size_width', $sizeArray[0]);
            $form->setField('size_height', $sizeArray[1]);
        }
    }
}

?>
