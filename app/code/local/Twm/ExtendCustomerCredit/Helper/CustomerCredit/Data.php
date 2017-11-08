<?php
/**
 * Created by PhpStorm.
 * User: mathijs
 * Date: 28-9-17
 * Time: 15:23
 */ 
class Twm_ExtendCustomerCredit_Helper_CustomerCredit_Data extends MageWorx_CustomerCredit_Helper_Data
{
    public function sendNotificationBalanceChangedEmail($customer) {
        if (!version_compare(Mage::getVersion(), '1.5.0', '>=')) {
            return $this->sendNotificationBalanceChangedEmailOld($customer);
        }

        $storeId = $customer->getStoreId();

        // Retrieve corresponding email template id and customer name
        $templateId = 'customercredit_email_credit_changed_template';
        if(Mage::getStoreConfig('mageworx_customers/customercredit_email_config/notification_template_balance_changed',$storeId)) {
            $templateId = Mage::getStoreConfig('mageworx_customers/customercredit_email_config/notification_template_balance_changed',$storeId);
        }

        $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();

        $creditData = $customer->getCustomerCreditData();

        if (isset($creditData['value_change'])) $valueChange = floatval($creditData['value_change']); else $valueChange = 0;
        if ($valueChange==0) return $this;

        if (isset($creditData['credit_value'])) $creditValue = floatval($creditData['credit_value']); else $creditValue = 0;
        $balance = $creditValue;

        if (isset($creditData['comment'])) $comment = trim($creditData['comment']); else $comment = '';

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $mailer = Mage::getModel('core/email_template_mailer');

        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($customer->getEmail(), $customerName);
        if(Mage::getStoreConfig('mageworx_customers/customercredit_email_config/enable_bcc')) {
            foreach(explode(',', Mage::getStoreConfig('mageworx_customers/customercredit_email_config/enable_bcc')) as $bcc) {
                $emailInfo->addBcc($bcc, 'Magento Recipient');
            }
        }
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig('sales_email/order_comment/identity', $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'balance'   => $balance,
                'customerName' => $customerName,
                'comment' => $comment,
                'customer' => $customer
            )
        );
        $translate->setTranslateInline(true);
        // print_r($customer->getData());
        $mailer->send();

        return $this;
    }

}