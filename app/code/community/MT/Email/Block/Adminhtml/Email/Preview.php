<?php

class MT_Email_Block_Adminhtml_Email_Preview
    extends Mage_Adminhtml_Block_Template
{

    protected function _toHtml()
    {
        $id = Mage::app()->getRequest()->getParam('id');
        $template = Mage::getModel('core/email_template');
        $storeId = 0;

        if (is_numeric($id) && $id > 0) {
            $template->load($id);

        } else {
            $data = Mage::getSingleton('core/session')->getPreviewTemplateData();
            if (isset($data['id'])) {
                $template->load($data['id']);
            }

            Mage::getModel('mtemail/template')->preparePreviewVars($data['vars']);
            $template->setTemplateId(0);
            $template->setTemplateType(2);
            $template->setTemplateText($data['content']);
            $template->setTemplateStyles($data['css']);
        }

        $storeId = $template->getStoreId();
        $vars = Mage::helper('mtemail')->getDemoVars($storeId);
        $templateProcessed = $template->getProcessedTemplate($vars);

        if (!is_numeric($id) && isset($data['css'])) {
            //$templateProcessed = '<style type="text/css">'.$data['css'].'</style>'.$templateProcessed;
        }

        return $templateProcessed;
    }

    public function getTemplateFilter()
    {
        if (empty($this->_templateFilter)) {
            $this->_templateFilter = Mage::getModel('core/email_template_filter');
            $this->_templateFilter->setUseAbsoluteLinks(true);
            ///->setStoreId($this->getDesignConfig()->getStore());
        }
        return $this->_templateFilter;
    }

}