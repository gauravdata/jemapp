<?php

include_once "Mage/Adminhtml/controllers/System/Email/TemplateController.php";

class MT_Email_Adminhtml_Email_MteditorController
    extends Mage_Adminhtml_System_Email_TemplateController
{
    public function indexAction()
    {
        $template = $this->_initTemplate('id');
        if (!$this->_validate()) {
            $this->_redirect('adminhtml/system_email_template/index');
            return $this;
        }
        Mage::getModel('mtemail/template')->updatePlainTextVersion($template);
        Mage::getModel('mtemail/template')->deleteTmpVars();

        Mage::register('mt_editor_edit_mode', 1);

        $this->_title($this->__('MTEditor'));
        $this->loadLayout();
        $this->renderLayout();
        return $this;
    }

    protected function _validate()
    {
        $helper = Mage::helper('mtemail');
        $template = Mage::registry('current_email_template');
        $storeId = $template->getStoreId();
        if (!$helper->getDemoOrder($storeId)->hasData('entity_id')) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('mtemail')->__('Please go to System > Configuration > MT Email > Preview Settings > Order ID and set correct ID.').$helper->getWikiLink('preview_settings')
            );
            return false;
        }

        if (!$helper->getDemoInvoice($storeId)->hasData('entity_id')) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('mtemail')->__('Please go to System > Configuration > MT Email > Preview Settings > Invoice ID and set correct ID').$helper->getWikiLink('preview_settings')
            );
            return false;
        }

        if (!$helper->getDemoShipment($storeId)->hasData('entity_id')) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('mtemail')->__('Please go to System > Configuration > MT Email > Preview Settings > Shipment ID and set correct ID').$helper->getWikiLink('preview_settings')
            );
            return false;
        }

        if (!$helper->getDemoCreditMemo($storeId)->hasData('entity_id')) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('mtemail')->__('Please go to System > Configuration > MT Email > Preview Settings > Creditmemo ID and set correct ID').$helper->getWikiLink('preview_settings')
            );
            return false;
        }

        return true;
    }

    public function getTemplateAjaxAction()
    {
        $templateCode = $this->getRequest()->getParam('template');
        $localeCode = $this->getRequest()->getParam('locale');
        $template = Mage::getModel('core/email_template')->loadDefault($templateCode, $localeCode);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'template' => $template->getData(),
            'newFormKey' => Mage::getSingleton('core/session')->getFormKey()
        )));
    }

    public function saveAjaxAction()
    {
        $success = 0;
        $error = '';
        $request = $this->getRequest();
        $id = $this->getRequest()->getParam('id');
        $templateCode = $request->getParam('orig_template_code');
        $template = $this->_initTemplate('id');
        $defaultTemplate =  Mage::getModel('core/email_template')->loadDefault($templateCode);
        $locale = $request->getParam('orig_locale');

        if (!$defaultTemplate->getId() || (!$template->getId() && $id)) {
            $error = Mage::helper('adminhtml')->__('This Email template no longer exists.');
        } else {
            try {
                $content = $defaultTemplate->getTemplateText();
                $modelTemplate = Mage::getModel('mtemail/template');

                $newContent = $modelTemplate->removePlainText($content);
                $newContent = $modelTemplate->assignUniqueBlockId($newContent);

                $template->setTemplateSubject($request->getParam('template_subject'))
                    ->setTemplateCode($request->getParam('template_code'))
                    ->setTemplateText($newContent)
                    ->setTemplateStyles($defaultTemplate->getTemplateStyles())
                    ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate())
                    ->setOrigTemplateCode($templateCode)
                    ->setIsMtemail(1)
                    ->setLocale(strtolower($locale))
                    ->setStoreId($request->getParam('store_id'))
                    ->setOrigTemplateVariables($request->getParam('orig_template_variables'));

                if (!$template->getId()) {
                    $template->setAddedAt(Mage::getSingleton('core/date')->gmtDate());
                    $template->setTemplateType(Mage_Core_Model_Email_Template::TYPE_HTML);
                }

                $template->save();

                $blockList = Mage::helper('mtemail')->parseBlockList($template);

                $modelTemplate->updateDefaultPrivateVars($template, $blockList, true);
                $modelTemplate->updateDefaultPublicVars($template, $blockList);
                $modelTemplate->updatePublicVars($template, $blockList);

                $success = 1;
            }
            catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'error' => $error,
            'success' => $success,
            'redirectTo' => Mage::helper("adminhtml")->getUrl("adminhtml/email_mteditor/index/", array('id' => $template->getId()))
        )));
    }

    public function uploadAction()
    {
        $success = 0;
        $fileUrl = '';
        $error = '';
        if(isset($_FILES['files']['name']) && (file_exists($_FILES['files']['tmp_name']))) {
            $file = $_FILES['files'];
            try {
                $uploader = new Varien_File_Uploader('files');
                $uploader->setAllowedExtensions(array('jpg','jpeg','png','gif'));
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $path = Mage::getBaseDir('media').DS.'mt'.DS.'email'.DS.'images/';
                $uploader->save($path, $file['name']);
                $fileUrl = Mage::getBaseUrl('media').'/mt/email/images/'.$file['name'];

                $success = 1;
            } catch (Mage_Core_Exception $e) {
                $error = $e->getMessage();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        } else {
            $error = 'File not found';
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'error' => $error,
            'success' => $success,
            'fileUrl' => $fileUrl,
        )));
    }


    public function saveEmailContentAjaxAction()
    {
        $vars = $this->getRequest()->getParam('vars');
        $css = $this->getRequest()->getParam('css');
        $templateContent = $this->getRequest()->getParam('template_content');
        $id = $this->getRequest()->getParam('template_id');
        $applyToAll = $this->getRequest()->getParam('apply_to_all');
        $removedBlockList = $this->getRequest()->getParam('removed_block_list');

        $success = 0;
        $error = '';
        try {
            Mage::getModel('mtemail/template')->saveTemplate($id, $templateContent, $vars, $css, $applyToAll);
            Mage::getModel('mtemail/template')->deleteBlock($removedBlockList);
            $success = 1;
        } catch (Exeption $e) {
            $error = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'success' => $success,
            'error' => $error
        )));
    }

    public function preparePreviewAjaxAction()
    {
        $vars = $this->getRequest()->getParam('vars');
        $css = $this->getRequest()->getParam('css');
        $templateContent = $this->getRequest()->getParam('content');
        $id = $this->getRequest()->getParam('id');

        Mage::getSingleton('core/session')->setPreviewTemplateData(array(
            'vars' => $vars,
            'css' => $css,
            'content' => $templateContent,
            'id' => $id
        ));

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'success' => 1
        )));
    }

    public function previewAction()
    {

        $this->loadLayout('mt_email_preview');
        $this->renderLayout();
    }

    public function sendTestEmilAjaxAction()
    {

        $templateId = $this->getRequest()->getParam('id');
        $content = $this->getRequest()->getParam('content');
        $vars = $this->getRequest()->getParam('vars');
        $email = $this->getRequest()->getParam('email');
        $css = $this->getRequest()->getParam('css');


        $error = '';
        $success = 0;
        try {
            $template = Mage::getModel('core/email_template')->load($templateId);
            $storeId = $template->getStoreId();

            $currentStoreId = Mage::app()->getStore()->getId();
            Mage::app()->setCurrentStore($storeId);

            Mage::getModel('mtemail/template')->preparePreviewVars($vars, 0);
            $template->setTemplateId(0);
            $template->setTemplateText($content);
            $template->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name', $storeId));
            $template->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email', $storeId));
            $template->setTemplateText($content);
            $template->setTemplateStyles($css);
            $contentVars = Mage::helper('mtemail')->getDemoVars($storeId);
            $template->send($email, null, $contentVars);
            Mage::app()->setCurrentStore($currentStoreId);
            $success = 1;
        } catch (Mage_Core_Exception $e) {
            $error = $e->getMessages();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'success' => $success,
            'error' => $error
        )));
    }

    public function saveInfoAjaxAction()
    {
        $success = 0;
        $error = '';
        $request = $this->getRequest();
        $id = $this->getRequest()->getParam('id');
        $templateCode = $request->getParam('template_code');
        $templateSubject = $request->getParam('template_subject');
        $storeId = $request->getParam('store_id');
        $template = $this->_initTemplate('id');

        if (!$template->getId() && $id) {
            $error = Mage::helper('adminhtml')->__('This Email template no longer exists.');
        } else {
            try {
                $template->setTemplateSubject($templateSubject)
                    ->setStoreId($storeId)
                    ->setTemplateCode($templateCode);
                $template->save();
                $success = 1;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'error' => $error,
            'success' => $success,
        )));
    }

    public function savePlainAjaxAction()
    {
        $success = 0;
        $error = '';
        $request = $this->getRequest();
        $id = $this->getRequest()->getParam('id');
        $plainText = $request->getParam('content');
        $template = $this->_initTemplate('id');

        if (!$template->getId() && $id) {
            $error = Mage::helper('adminhtml')->__('This Email template no longer exists.');
        } else {
            try {
                $template->setTemplatePlainText($plainText);
                $template->save();
                $success = 1;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'error' => $error,
            'success' => $success,
        )));
    }

    public function deleteTemplateAjaxAction()
    {
        $success = 0;
        $error = '';
        $request = $this->getRequest();
        $id = $this->getRequest()->getParam('id');
        $template = $this->_initTemplate('id');

        if (!$template->getId() && $id) {
            $error = Mage::helper('adminhtml')->__('This Email template no longer exists.');
        } else {
            try {
                $template->delete();
                $success = 1;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'error' => $error,
            'success' => $success,
        )));
    }

    public function createNewBlockAjaxAction()
    {
        Mage::register('mt_editor_edit_mode', 1);

        $success = 0;
        $error = '';
        $templateId = $this->getRequest()->getParam('template_id');
        $newBlockId = $this->getRequest()->getParam('block_id');
        $content = $this->getRequest()->getParam('content');
        $template = $this->_initTemplate('template_id');
        $blockContent = '';
        if (!$template->getId() && $templateId) {
            $error = Mage::helper('adminhtml')->__('This Email template no longer exists.');
        } else {
            try {
                $blockContent = Mage::getModel('mtemail/template')
                    ->createNewBock($template, $newBlockId, $content);
                $success = 1;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'error' => $error,
            'success' => $success,
            'block' => $blockContent,
        )));
    }

    public function deleteBlockAjaxAction()
    {
        $success = 0;
        $error = '';
        $blockId = $this->getRequest()->getParam('block_id');
        try {
            $blockContent = Mage::getModel('mtemail/template')
                ->deleteBock($blockId);
            $success = 1;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'error' => $error,
            'success' => $success,
        )));
    }
}
