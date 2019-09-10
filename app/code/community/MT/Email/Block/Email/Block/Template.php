<?php

class MT_Email_Block_Email_Block_Template
    extends Mage_Core_Block_Template
{

    public function __construct()
    {
        $this->setData('area','frontend');
    }

    protected function _beforeToHtml()
    {
        $this->setTemplate('mt/email/theme/'.$this->getParentBlock()->getTheme().'/'.$this->getTemplate());
        return parent::_toHtml();
    }

    public function getVarModel()
    {
        return $this->getParentBlock()->getVarModel();
    }

    public function isRTL()
    {
        return $this->getParentBlock()->isRTL();
    }
}