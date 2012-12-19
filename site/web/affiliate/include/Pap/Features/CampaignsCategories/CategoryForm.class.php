<?php
/**
*   @copyright Copyright (c) 2010 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Juraj Simon
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
class Pap_Features_CampaignsCategories_CategoryForm extends Gpf_View_FormService {
    
    private $categoryCode;
    
    /**
     * @return Pap_Db_CampaignCategory
     */
    protected function createDbRowObject() {
        return new Pap_Db_CampaignCategory();
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Campaign Category");
    }

    /**
     *
     * @service campaigns_categories add
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        $form = parent::add($params);

        if ($form->isSuccessful() && $form->getFieldValue("code") == "Custom-Page") {
            try {
            	$templatePaths = Gpf_Paths::getInstance()->getTemplateSearchPaths("affiliates", "", true);
            	$fileName = $templatePaths[0] . $form->getFieldValue("templateName").".tpl";
            	$file = new Gpf_Io_File($fileName);
            	$file->open('w');
            	$file->write($form->getFieldValue("templateName").".tpl");
            	$file->close();
            } catch (Exception $e) {
                $form->setErrorMessage($e->getMessage());
                return $form;
            }
        }
        return $form;
    }
    
    /**
     * @service campaigns_categories read
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        try {
            $form = parent::load($params);
        } catch (Gpf_DbEngine_NoRowException $e) {
            $form = new Gpf_Rpc_Form($params);
            $cat = new Pap_Db_CampaignCategory();
            $this->loadForm($form, new Pap_Db_CampaignCategory());
        }
        $node = $this->getTreeNode($form->getFieldValue('Id'));
        $this->insertAdditionalDataToForm($node, $form);
        
        return $form;
    }
    
    /**
     *
     * @return Gpf_Db_HierarchicalDataNode
     */
    private function getTreeNode($code) {
        $node = new Gpf_Db_HierarchicalDataNode(Pap_Features_CampaignsCategories_Main::CAMPAIGNS_CATEGORIES_HIERARCHICAL_DATE_TYPE);
        $node->setCode($code);
        
        $node->loadFromData(array(Gpf_Db_Table_HierarchicalDataNodes::TYPE, Gpf_Db_Table_HierarchicalDataNodes::CODE));
        return $node;
    }
    
    
    private function insertAdditionalDataToForm(Gpf_Db_HierarchicalDataNode $node, Gpf_Rpc_Form $form) {
        $form->addField(Gpf_Db_Table_HierarchicalDataNodes::NAME, $node->get(Gpf_Db_Table_HierarchicalDataNodes::NAME));
        $form->addField(Gpf_Db_Table_HierarchicalDataNodes::STATE, $node->get(Gpf_Db_Table_HierarchicalDataNodes::STATE));
    }
    
    protected function fillAdd(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $dbRow) {
        parent::fillAdd($form, $dbRow);
        $dbRow->set('categoryid', $this->categoryCode);
    }

    /**
     * @service campaigns_categories write
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        $name = $form->getFieldValue('name');
        $state = $form->getFieldValue('state');
        
        $node = $this->getTreeNode($form->getFieldValue('Id'));
        $node->setName($name);
        $node->setState($state);
        $node->save();
        $form = parent::save($params);
        if ($form->getErrorMessage() != '') {
            $this->categoryCode = $form->getFieldValue('Id');
            $form = parent::add($params);
        }
        return $form;
    }
}

?>
