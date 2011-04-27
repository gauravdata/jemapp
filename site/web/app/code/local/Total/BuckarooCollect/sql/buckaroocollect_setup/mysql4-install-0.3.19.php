<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Biebersdorf
 * @package    Biebersdorf_CustomerOrderComment
 * @copyright  Copyright (c) 2010 Ottmar Biebersdorf (http://www.obiebersdorf.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */

/* @var $installer Mage_Sales_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

        // Create buckaroocollect_600 template

            // Load default template
            $default_mail=Mage::getModel('core/email_template')->loadDefault('buckaroocollect_600_template', Mage::app()->getLocale()->getLocaleCode());
            
            // Create new transactional email         
            $new_mail=Mage::getModel('core/email_template');
            $new_mail->setTemplateCode('buckaroocollect_600');
            $new_mail->setTemplateText(trim($default_mail->getTemplateText()));
            $new_mail->setTemplateStyles(trim($default_mail->getTemplateStyles()));
            $new_mail->setTemplateType(Mage_Core_Model_Email_Template::TYPE_HTML); //html
            $new_mail->setTemplateSubject(trim($default_mail->getTemplateSubject()));
            $new_mail->setTemplateSenderName('');
            $new_mail->setTemplateSenderEmail=('');
            $new_mail->setAddedAt(date('Y-m-d H:i:s', time()));
            $new_mail->setModifiedAt(date('Y-m-d H:i:s', time()));
            $new_mail->setOrigTemplateCode('buckaroocollect_600_template');
            $new_mail->setOrigTemplateVariables(trim($default_mail->getOrigTemplateVariables()));
            
            $new_mail->save();
        
        // end of creating buckaroocollect_600 template
        
        // Create buckaroocollect_602_603 template

            // Load default template
            $default_mail=Mage::getModel('core/email_template')->loadDefault('buckaroocollect_602_603_template', Mage::app()->getLocale()->getLocaleCode());
            
            // Create new transactional email         
            $new_mail=Mage::getModel('core/email_template');
            $new_mail->setTemplateCode('buckaroocollect_602_603');
            $new_mail->setTemplateText(trim($default_mail->getTemplateText()));
            $new_mail->setTemplateStyles(trim($default_mail->getTemplateStyles()));
            $new_mail->setTemplateType(Mage_Core_Model_Email_Template::TYPE_HTML); //html
            $new_mail->setTemplateSubject(trim($default_mail->getTemplateSubject()));
            $new_mail->setTemplateSenderName('');
            $new_mail->setTemplateSenderEmail=('');
            $new_mail->setAddedAt(date('Y-m-d H:i:s', time()));
            $new_mail->setModifiedAt(date('Y-m-d H:i:s', time()));
            $new_mail->setOrigTemplateCode('buckaroocollect_602_603_template');
            $new_mail->setOrigTemplateVariables(trim($default_mail->getOrigTemplateVariables()));
            
            $new_mail->save();        
        
        // end of creating buckaroocollect_602_603 template
        

$installer->endSetup();