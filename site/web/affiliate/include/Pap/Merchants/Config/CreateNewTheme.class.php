<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
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
class Pap_Merchants_Config_CreateNewTheme extends Gpf_Object {

    /**
     * @service theme write
     *
     * @return Gpf_Rpc_Action
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $newThemeId = str_replace(' ', '_', $form->getFieldValue('name'));
        $srcTheme = new Gpf_Desktop_Theme($form->getFieldValue('Id')
        , $form->getFieldValue('panelName'));
        $srcPath = $srcTheme->getThemePath();
        $targetPath = new Gpf_Io_File($srcPath->getParent().$newThemeId);
        try{
            $targetPath->mkdir();
            $srcPath->recursiveCopy($targetPath);

            $newTheme = new Gpf_Desktop_Theme($newThemeId
            , $form->getFieldValue('panelName'));
            $newTheme->load();
            $newTheme->setName($form->getFieldValue('name'));
            $newTheme->setAuthor($form->getFieldValue('author'));
            $newTheme->setUrl($form->getFieldValue('url'));
            $newTheme->setDescription($form->getFieldValue('description'));
            $newTheme->setBuiltIn(false);
            $newTheme->save();
        }catch(Gpf_Exception $ex){
            $form->setErrorMessage($ex->getMessage());
        }
        $form->setField('themeId', $newThemeId);
        return $form;
    }
}

?>
