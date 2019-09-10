<?php

class MT_Email_Block_Adminhtml_Email_Mteditor
    extends Mage_Adminhtml_Block_Template
{
    const MEDIA_IMAGE_DIR = '/mt/email/images/';

    protected $_template = 'mt/email/mteditor/content.phtml';

    public function getConfig()
    {
        $templateId = Mage::app()->getRequest()->getParam('id');
        $config = array(
            'data' => $this->getTemplateBlockList(),
            'vars' => $this->getVars(),
            'action' => $this->getActions(),
            'formKey' => Mage::getSingleton('core/session')->getFormKey(),
            'imageList' => $this->getImageList(),
            'template_id' => $templateId,
            'body' => array(
                'css' => $this->getBodyCss()
            ),
            'contentHelper' => $this->getContentHelpers(),
            'template' => $this->getTemplateConfig(),
            'fontFamilyOptions' => $this->getFontFamilyOptionArray()
        );

        return $config;
    }


    public function getTemplateConfig()
    {
        $id = Mage::app()->getRequest()->getParam('id');
        $config = array();
        if (is_numeric($id)) {
            $template = Mage::getModel('core/email_template')->load($id);
            if ($template->getId()) {
                $config['code'] = $template->getTemplateCode();
                $config['subject'] = $template->getTemplateSubject();
                $config['store_id'] = $template->getStoreId();
            }
        }

        return $config;
    }
    public function getImageList()
    {
        $list = array();
        $baseUrl = Mage::getBaseUrl('media').self::MEDIA_IMAGE_DIR;
        if ($handle = opendir(Mage::getBaseDir('media').self::MEDIA_IMAGE_DIR)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry!= '..' && $entry!= '.htaccess') {
                    $list[] = str_replace('//mt/email', '/mt/email', $baseUrl.$entry);
                }
            }
            closedir($handle);
        }
        return $list;
    }

    public function getActions()
    {
        $actions = array(
            'back' => $this->getBackUrl(),
            'createTemplateUrl' => Mage::helper("adminhtml")->getUrl("adminhtml/email_mteditor/saveAjax/"),
            'initTemplateUrl' => Mage::helper("adminhtml")->getUrl("adminhtml/email_mteditor/getTemplateAjax/"),
            'uploadUrl' => Mage::helper("adminhtml")->getUrl("adminhtml/email_mteditor/upload/"),
            'saveUrl' => Mage::helper("adminhtml")->getUrl("adminhtml/email_mteditor/saveEmailContentAjax/"),
            'preparePreviewAjaxUrl' => Mage::helper("adminhtml")->getUrl("adminhtml/email_mteditor/preparePreviewAjax/"),
            'previewUrl' => Mage::helper("adminhtml")->getUrl("adminhtml/email_mteditor/preview/", array('id' => '0')),
            'sendTestEmilUrl' => Mage::helper("adminhtml")->getUrl("adminhtml/email_mteditor/sendTestEmilAjax/"),
            'saveInfo' => Mage::helper("adminhtml")->getUrl("adminhtml/email_mteditor/saveInfoAjax/"),
            'deleteTemplateAjax' => Mage::helper("adminhtml")->getUrl("adminhtml/email_mteditor/deleteTemplateAjax/"),
            'createNewBlock' => Mage::helper("adminhtml")->getUrl("adminhtml/email_mteditor/createNewBlockAjax/"),
            'deleteBlock' => Mage::helper("adminhtml")->getUrl("adminhtml/email_mteditor/deleteBlockAjax/"),
            'savePlainAjaxUrl' => Mage::helper("adminhtml")->getUrl("adminhtml/email_mteditor/savePlainAjax/"),
        );
        return $actions;
    }


    public function getEmailTemplate()
    {
        return Mage::registry('current_email_template');
    }

    public function getDefaultEmailTemplate()
    {
        $template = $this->getEmailTemplate();
        $defaultTemplate = Mage::getModel('core/email_template');
        if ($template->getId()) {
            $defaultTemplate->loadDefault($template->getData('orig_template_code'));
        }

        return $defaultTemplate;
    }

    public function getTemplateContent()
    {
        $template =  Mage::registry('current_email_template');
        $filter = $this->getTemplateFilter();
        $filter->setProcessVarFlag(false);
        $templateProcessed = $filter->filter($template->getTemplateText());
        return $templateProcessed;
    }

    public function getTemplateContentStyle()
    {
        $template =  Mage::registry('current_email_template');
        return $template->getData('template_styles');
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

    public function getBodyCss()
    {
        $template = Mage::registry('current_email_template');
        return $template->getData('template_styles');
    }

    public function getTemplateBlockList()
    {
        $template = $this->getDefaultEmailTemplate();
        if (!$template->getId()) {
            return false;
        }

        $skipVars = array('mtemail_block_body_css');
        $blockArray = array();
        $vars = $template->getVariablesOptionArray();

        $processor = $this->getTemplateFilter();
        $processor->setProcessVarFlag(false);

        if ($vars) {
            foreach ($vars as $var) {

                if (in_array($var['label'], $skipVars)) {
                    continue;
                }
                $key = false;
                $origContent = '';
                $result = '';

                if (substr($var['label'], -4) == '_css') {
                    $result = $processor->filter($var['value']);
                    $key = 'css';
                } elseif (substr($var['label'], -6) == '_image') {
                    $key = 'image';
                    $image = str_replace(array('{{', '}}'), '', $var['value']);
                    $result = Mage::getDesign()->getSkinUrl($image);
                } elseif (substr_count($var['label'], 'mt_email_block_') == 1) {
                    $result = $var['value'];
                    $key = 'content';
                }

                if ($key) {
                    $blockName = str_replace(array('_image', '_css'), '', $var['label']);
                    $blockArray[$blockName][$key] = $result;
                }

            }

        }

        return $blockArray;
    }

    public function getVars()
    {
        $template = $this->getDefaultEmailTemplate();
        if (!$template->getId()) {
            return false;
        }

        $varsArray = array();
        $vars = $template->getVariablesOptionArray();

        if ($vars) {
            foreach ($vars as $var) {

                if (substr_count($var['label'], 'mt_email_block') == 0) {
                    $varsArray[] = $var;
                }
            }

        }

        return $varsArray;
    }

    public function getContentHelpers()
    {
        $template = $this->getDefaultEmailTemplate();
        if (!$template->getId()) {
            return false;
        }

        $helperArray = array();
        $vars = $template->getVariablesOptionArray();

        if ($vars) {
            $varModel = Mage::getModel('mtemail/var');
            foreach ($vars as $var) {
                if (substr_count($var['label'], 'helper_') == 1) {
                    $key = str_replace('helper_', '', $var['label']);
                    $tmp = explode('_', $key);
                    $attr = $tmp[0];
                    unset($tmp[0]);
                    $key = implode('_', $tmp);

                    $helperArray[] = array(
                        'origVar' => $var['value'],
                        'varKey' => $key,
                        'attr' => $attr,
                        'fakeData' => $varModel->getDefaultValue($key)
                    );
                }
            }
        }

        return $helperArray;
    }

    public function getTemplateList()
    {
        $list = Mage_Core_Model_Email_Template::getDefaultTemplatesAsOptionsArray();
        $mtEmailList = array();
        foreach ($list as $template) {
            if (substr($template['value'], 0, 8) != 'mtemail_') {
                continue;
            }
            $mtEmailList[] = $template;
        }

        return $mtEmailList;
    }


    public function getLocaleOptions()
    {
        return Mage::app()->getLocale()->getOptionLocales();
    }

    public function getBackUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/system_email_template/index');
    }

    public function getFontFamilyOptionArray()
    {
        $list = unserialize(Mage::getStoreConfig('mtemail/editor/font_family'));
        if (count($list) == 0) {
            return array();
        }

        $optionArray = array();
        foreach ($list as $item) {
            $optionArray[] = $item['name'];
        }

        return $optionArray;
    }

    public function getTemplatePlainText()
    {
        $template =  Mage::registry('current_email_template');
        return $template->getTemplatePlainText();
    }

}