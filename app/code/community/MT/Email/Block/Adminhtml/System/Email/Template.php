<?php

class MT_Email_Block_Adminhtml_System_Email_Template
    extends Mage_Adminhtml_Block_System_Email_Template
{
    protected function _prepareLayout()
    {
        $parent = parent::_prepareLayout();
        if (!Mage::helper('mtemail')->isActive()) {
            return $parent;
        }

        $addButton =  $this->getChild('add_button')
        ->setBefore('-');
        $mtEditor = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('adminhtml')->__('Create New with MTEditor'),
                'onclick'   => "window.location='" . $this->getMtEditorUrl() . "'",
                'class'     => 'add'

        ));

        $textList = $this->getLayout()->createBlock('adminhtml/text_list')
            ->insert($addButton, 'add_button', 'mt_editor', 'add_button')
            ->insert($mtEditor, 'mt_editor', '', 'mt_editor');

        $this->setChild('add_button', $textList);
        return $this;
    }

    public function getMtEditorUrl()
    {
       return Mage::helper("adminhtml")->getUrl("adminhtml/email_mteditor/index");
    }

}