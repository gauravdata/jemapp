<?php

class MT_Email_Model_Template
{

    private $__ignoreVars = array();

    private $__uniqueBlockIdArray = null;

    private $__idMap = array();

    private $__similarTemplateCollection = null;

    private $__varArray = null;

    protected $_templateFilter = null;

    public function saveTemplate($templateId, $templateContent, $vars, $css, $applyToAll)
    {

        $helper = Mage::helper('mtemail');
        $templateModel = Mage::getModel('adminhtml/email_template');
        if (!is_numeric($templateId)) {
            throw new Exception($helper->__('Bad template ID'));
        }
        $templateModel->load($templateId);

        if (!$templateModel->getId()) {
            throw new Exception($helper->__('Template is no longer available'));
        }

        $templateModel->setTemplateText($templateContent);
        $templateModel->setTemplateStyles($css);
        $templateModel->save();

        $this->saveVars($vars, $templateId, $applyToAll);
    }

    public function saveVars($vars, $templateId, $applyToAll)
    {
        if (count($vars) > 0) {
            $varModel = Mage::getModel('mtemail/var');
            foreach ($vars as $blockName => $blockIds) {
                if (count($blockIds) > 0) {
                    foreach ($blockIds as $blockId => $varList) {
                        if (count($varList) > 0) {
                            foreach ($varList as $key => $value) {
                                $varModel->saveVar($blockName, $blockId, $templateId, $key, $value, $applyToAll);
                            }
                        }
                    }
                }
            }
        }
    }

    public function preparePreviewVars($vars)
    {
        $this->removePreviewVars();
        $this->saveVars($vars, 0, false);
    }

    public function removePreviewVars()
    {
        $vars = Mage::getModel('mtemail/var')->getCollection()
            ->addFieldToFilter('template_id', 0);
        if ($vars->count() > 0) {
            foreach ($vars as $var) {
                $var->delete();
            }
        }
    }

    public function deleteTmpVars()
    {
        $vars = Mage::getModel('mtemail/var')->getCollection()
            ->addFieldToFilter('is_tmp', 1);
        if ($vars->count() > 0) {
            foreach ($vars as $var) {
                $var->delete();
            }
        }
    }

    public function createNewBock($template, $newBlockId, $content)
    {

        Mage::register('mt_email_tmp_var', 1);
        $templateCode = $template->getOrigTemplateCode();
        $locale = $template->getLocale();

        //copy vars for new block
        $tmpBlockName = explode('block_name="', $content);
        $tmpBlockName = explode('"', $tmpBlockName[1]);
        $blockName = $tmpBlockName[0];
        $content = str_replace('block_id=0', 'block_id='.$newBlockId, $content);

        $blockList = array($content);
        $this->updateDefaultPrivateVars($template, $blockList, false, true);
        $this->updateDefaultPublicVars($template, $blockList, true);
        $this->updatePublicVars($template, $blockList, true);

        $filter = $this->getTemplateFilter();
        $filter->setVariables(array('this' => $template));
        $filter->setProcessVarFlag(false);
        $templateProcessed = $filter->filter($content);
        return $templateProcessed;
    }

    public function deleteBlock($blockIds)
    {
        if (count($blockIds) > 0) {
            $varCollection = Mage::getModel('mtemail/var')->getCollection()
                ->addFieldToFilter('block_id', array('in' => array_keys($blockIds)));
            if ($varCollection->count() > 0) {
                foreach($varCollection as $var) {
                    $var->delete();
                }
            }
        }


        return true;
    }

    public function getTemplateFilter()
    {
        if (empty($this->_templateFilter)) {
            $this->_templateFilter = Mage::getModel('core/email_template_filter');
            $this->_templateFilter->setUseAbsoluteLinks(true);
        }
        return $this->_templateFilter;
    }

    /*
     *  create and assign unique ID for each block
     */

    public function assignUniqueBlockId($content)
    {
        $blockIdArray = $this->getUniqueBlockIdArray($content);
        if (count($blockIdArray) > 0) {
            $tmpContent = explode('}}', $content);
            $i = 0;
            foreach ($tmpContent as $key => $tmpBlock) {
                if (substr_count($tmpBlock, 'block_id=') == 1) {
                    $tmp1 = explode('block_id=', $tmpBlock);
                    $tmp2 = explode(' ', $tmp1[1]);

                    if (substr_count($tmp2[1], 'block_name="') == 1) {
                    //    Mage::helper('mtemail')->log('Error in template: Missing block_name or it is not the second parameter.');
                    }
                    $blockName = str_replace(array('block_name="', '"'),'', $tmp2[1]);

                    $newBlockId = $blockIdArray[$i];
                    $this->__idMap[$tmp2[0]] = array(
                        'block_id' => $newBlockId,
                        'block_name' => $blockName
                    );
                    $tmp2[0] = $newBlockId;
                    $tmp2 = implode(' ', $tmp2);
                    $tmpBlock = $tmp1[0]. ' block_id='.$tmp2;
                    $tmpContent[$key] = $tmpBlock;
                    $i++;
                } else {
                    break;
                }
            }
            $content = implode('}}', $tmpContent);
        }

        return $content;
    }

    public function getUniqueBlockIdArray($content)
    {
        if ($this->__uniqueBlockIdArray == null) {
            $count = $this->getBlockCount($content);
            $beginFrom = time();
            $idArray = array();
            for ($i = 0; $i < $count; $i++) {
                $idArray[] = $beginFrom+$i;
            }
            $this->__uniqueBlockIdArray = $idArray;
        }

        return $this->__uniqueBlockIdArray;
    }

    public function getBlockCount($content)
    {
        return substr_count($content, 'block_id=');
    }

    public function parseBlockNameList($content)
    {
        $tmpContent = explode('}}', $content);
        $data = array();
        if (count($tmpContent) > 0) {
            foreach ($tmpContent as $tmpBlock) {
                $tmpBlock = explode('block_name="', $tmpBlock);
                if (count($tmpBlock) == 2) {
                    $tmpBlock = explode('"', $tmpBlock[1]);
                    $data[] = $tmpBlock[0];
                }
            }
        }
        return $data;
    }

    public function getTemplateBlockNameIdList($content)
    {
        if (substr_count($content, 'block_name="')==0) {
            return false;
        }

        $tmpContent = explode('}}', $content);
        $data = array();
        foreach ($tmpContent as $tmpBlock) {
            if (substr_count($tmpBlock, 'block_name="') == 0 || substr_count($tmpBlock, 'block_id=') == 0 ) {
                continue;
            }
            $tmpBlock1 = explode('block_name="', $tmpBlock);
            $tmpBlock2 = explode('"', $tmpBlock1[1]);

            $tmpBlock3 = explode('block_id=', $tmpBlock);
            $tmpBlock4 = explode(' ', $tmpBlock3[1]);

            $data[$tmpBlock4[0]] = $tmpBlock2[0];
        }

        if (count ($data) == 0) {
            return false;
        }

        return $data;
    }


    /**
     * Copy private data from language file to template
     *
     * @param Mage_Core_Model_Email_Template $template
     * @param array $blockList
     * @param bool  $blockIdFilter
     * @param int  $isTmp
     * @return bool
     */
    public function updateDefaultPrivateVars($template, $blockList, $blockIdFilter = false, $isTmp = 0)
    {
        $templateId = $template->getId();
        $storeId = $template->getStoreId();
        $templateCode = $template->getOrigTemplateCode();
        $helper = Mage::helper('mtemail');
        $varModel = Mage::getModel('mtemail/var');
        $defaultVarCollection = $varModel->getDefaultVarCollection($template, 0);

        $createdVars = array();

        if ($defaultVarCollection->count() > 0) {
            foreach ($blockList as $block) {
                $blockData = $helper->parseBlockData($block);
                $uniqueBlockId = $blockData['block_id'];
                $blockName = $blockData['block_name'];

                foreach ($defaultVarCollection as $defaultVar) {
                    if ($defaultVar->getBlockName() == $blockName) {

                        //block ID filter for new template
                        if ($blockIdFilter) {
                            if ( !isset($this->__idMap[$defaultVar->getBlockId()])) {
                                continue;
                            }
                        } else {
                            //avoid duplication
                            if (isset($createdVars[$blockName][$defaultVar->getVarKey()])) {
                                continue;
                            }
                        }

                        //create new variable
                        $varData = $defaultVar->getData();
                        $varData['block_id'] = $uniqueBlockId;
                        $varData['template_id'] = $templateId;
                        $varData['store_id'] = $storeId;
                        $varData['template_code'] = $templateCode;
                        $varData['is_tmp'] = $isTmp;

                        $newVar = Mage::getModel('mtemail/var');
                        $newVar->setData($varData)
                            ->save();

                        $createdVars[$blockName][$defaultVar->getVarKey()] = 1;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Copy public data from language file to template
     *
     * @param Mage_Core_Model_Email_Template $template
     * @param array $blockList
     * @param int $isTmp
     * @return bool
     */

    public function updateDefaultPublicVars($template, $blockList, $isTmp = 0)
    {
        $templateId = $template->getId();
        $templateCode = $template->getOrigTemplateCode();
        $storeId = $template->getStoreId();
        $helper = Mage::helper('mtemail');
        $varModel = Mage::getModel('mtemail/var');
        $defaultVarCollection = $varModel->getDefaultVarCollection($template, 1);
        $createdVars = array();

        if ($defaultVarCollection->count() > 0) {
            foreach ($blockList as $block) {
                $blockData = $helper->parseBlockData($block);
                $uniqueBlockId = $blockData['block_id'];
                $blockName = $blockData['block_name'];

                foreach ($defaultVarCollection as $defaultVar) {
                    if ($defaultVar->getBlockName() == $blockName) {

                        //avoid duplication
                        if (isset($createdVars[$blockName][$defaultVar->getVarKey()])) {
                            continue;
                        }
                        //create new variable
                        $varData = $defaultVar->getData();
                        $varData['block_id'] = $uniqueBlockId;
                        $varData['template_id'] = $templateId;
                        $varData['store_id'] = $storeId;
                        $varData['template_code'] = $templateCode;
                        $varData['is_tmp'] = $isTmp;

                        $newVar = Mage::getModel('mtemail/var');
                        $newVar->setData($varData)
                            ->save();

                        $createdVars[$blockName][$defaultVar->getVarKey()] = 1;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Copy public data from existing template
     *
     * @param Mage_Core_Model_Email_Template $template
     * @param array $blockList
     * @param int $isTmp
     * @return bool
     */
    public function updatePublicVars($template, $blockList, $isTmp = 0)
    {
        $helper = Mage::helper('mtemail');
        $templateId = $template->getId();
        $storeId = $template->getStoreId();
        $templateCode = $template->getOrigTemplateCode();
        $varModel = Mage::getModel('mtemail/var');
        $globalVarCollection = Mage::getModel('mtemail/var')->getCollection()
            ->addFieldToFilter('global', 1)
            ->addFieldToFilter('template_id', array('neq' => $template->getId()))
            ->addFieldToFilter('store_id', $template->getStoreId())
            ->addFieldToFilter('block_name', array('in' => $helper->getBlockNameList($blockList)));
        $globalVarCollection->getSelect()->group('main_table.var_key');
        if ($globalVarCollection->count() == 0) {
            return false;
        }

        foreach ( $blockList as $block) {
            $blockData = $helper->parseBlockData($block);
            $blockName = $blockData['block_name'];
            $blockId = $blockData['block_id'];
            foreach ($globalVarCollection as $globalVar) {
                if ($blockName == $globalVar->getBlockName()) {

                    //check if exist
                    $var = $varModel->getVarObject($template, $blockName, $blockId, $globalVar->getVarKey());
                    if ($var == null) {
                        Mage::getModel('mtemail/var')
                            ->setData($globalVar->getData())
                            ->setId(null)
                            ->setBlockId($blockId)
                            ->setTemplateId($templateId)
                            ->setStoreId($storeId)
                            ->setTemplateCode($templateCode)
                            ->setGlobal(1)
                            ->setIsTmp($isTmp)
                            ->save();
                    } else {
                        $var->setVarValue($globalVar->getVarValue())
                            ->save();
                    }
                }
            }
        }
        return true;
    }


    /**
     * Prepare content before save template
     * Add Css and Body tag's
     *
     * @param $content
     * @return mixed
     */
    public function prepareContentBeforeSave($content)
    {
        $content = $this->addBodyTag($content);
        $content = $this->addCssTag($content);
        return $content;
    }

    /**
     * This method will add <body> tag to the email content
     *
     * @param $value
     * @return mixed
     */
    public function addBodyTag($value)
    {
        //this variable is available only for email content processor
        if ($this->isSubjectFilter()) {
            return $value;
        }
        if (substr_count($value, '<body>') == 0) {
            $value = '<body>'.$value;
        }

        if (substr_count($value, '</body>') == 0) {
            $value = $value.'</body>';
        }

        return $value;
    }

    /**
     * This method will add variable {{var non_inline_styles}} to the email content
     *
     * @param $value
     * @return mixed
     */
    public function addCssTag($value)
    {
        //this variable is available only for email content processor
        if ($this->isSubjectFilter()) {
            return $value;
        }
        if (substr_count($value, '<style>') == 0 && substr_count($value, '{{var non_inline_styles}}') == 0) {
            $value = '{{var non_inline_styles}}'.$value;
        }

        return $value;
    }

    public function updatePlainTextVersion($template)
    {
        $plainVersion = $template->getTemplatePlainText();

        if ($template->getId() && empty($plainVersion)) {
            $data = Mage::getConfig()->getNode('global/template/email/'.$template->getOrigTemplateCode());
            $templateText = Mage::app()->getTranslator()->getTemplateFile(
                (string)$data->file, 'email', $template->getLocale()
            );
            $matches = array();
            if (preg_match('/<!--@plain\s*(.*?)\s*@-->/s', $templateText, $matches)) {
                if (isset($matches[1]) && !empty($matches[1])) {

                    $template->setTemplatePlainText(str_replace(array('<!--@plain', '@-->'), '', $matches[1]));
                    $template->save();
                }
            }
        }
    }

    public function removePlainText($content)
    {
        if (preg_match("'<!--@plain(.*?)*@-->'si", $content, $matches)) {
            return str_replace($matches[0], '',$content);
        }

        return $content;
    }

    public function getProcessedPlainText($template, $vars)
    {
        $filter = $this->getTemplateFilter();
        $vars['this'] = $template;
        $filter->setVariables($vars);
        $templateProcessed = $filter->filter($template->getTemplatePlainText());

        return $templateProcessed;
    }
}