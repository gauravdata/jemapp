<?php

class MT_Email_Block_Email_Block
    extends Mage_Core_Block_Template
{

    public function __construct()
    {
        $this->setData('area','frontend');
    }

    protected function _beforeToHtml()
    {
        Mage::unregister('current_email_block_theme');
        Mage::register('current_email_block_theme', $this->getTheme());
        $this->setTemplate('mt/email/theme/'.$this->getTheme().'/'.$this->getTemplate());
        return parent::_toHtml();
    }

    public function getVarModel()
    {
        $varModel = Mage::getSingleton('mtemail/var');

        $varModel->setTemplateId($this->getTemplateId());
        $varModel->setBlockId($this->getBlockId());
        $varModel->setBlockName($this->getBlockName());
        return $varModel;
    }

    public function getStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    public function getTemplateId()
    {
        $templateId = parent::getTemplateId();
        $paramTemplateId =  Mage::app()->getRequest()->getParam('id');
        if (!$templateId && $paramTemplateId) {
            $templateId = $paramTemplateId;
        }

        return $templateId;
    }

    public function isRTL()
    {
        return $this->getDirection() == 'rtl';
    }

    public function getDirection()
    {
        return Mage::getStoreConfig('mtemail/general/direction', $this->getStoreId());
    }

}