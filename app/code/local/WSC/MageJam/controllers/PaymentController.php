<?php

class WSC_MageJam_PaymentController extends Mage_Core_Controller_Front_Action
{

    /**
     * Authenticates user by basic access authentication
    */
    protected function _authenticate()
    {
        $login = $this->getRequest()->getServer('PHP_AUTH_USER', false);
        $pass = $this->getRequest()->getServer('PHP_AUTH_PW', false);

        /* @var $helper WSC_MageJam_Helper_Auth */
        $helper = Mage::helper('magejam/auth');
        if(!$helper->auth($login, $pass)) {
            header('WWW-Authenticate: Basic realm="magajam realm"');
            header('HTTP/1.0 401 Unauthorized');
            exit;
        }
    }

    /**
     *
     *
     * @return Mage_Core_Controller_Front_Action|void
     */
    public function preDispatch()
    {
        $this->_authenticate();
        return parent::preDispatch();
    }

    /**
     * Action for rendering payment methods without any css
     */
    public function indexAction()
    {
        /* @var $session Mage_Checkout_Model_Session */
        $session = Mage::getSingleton('checkout/session');
        $session->unsetData('success');
        $session->unsetData('payment_url');
        $session->getMessages(true);

        $quoteId = $this->getRequest()->getParam('quote_id');
        if (!$quoteId) {
            $this->_fault('Requires quote_id as param');
            return;
        }

        $quote = $this->_getQuote($quoteId);
        if (!$quote->getId()) {
            $this->_fault('Shopping cart id is not valid');
            return;
        }
        $session->replaceQuote($quote);

        $methodCode = $this->getRequest()->getParam('method');
        if(!$methodCode) {
            $this->_fault('Requires payment method as param');
            return;
        }

        /* @var $helper WSC_MageJam_Helper_Data */
        $helper = Mage::helper('magejam');
        $methodInstance = $helper->getMethod($methodCode);
        if(!$methodInstance) {
            $this->_fault('Invalid state for shopping cart');
            return;
        }

        $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
        if ($requiredAgreements) {
            /* @var $session Mage_Checkout_Model_Session */
            $session = Mage::getSingleton('checkout/session');
            $currentUrl = Mage::helper('core/url')->getCurrentUrl();
            $session->setPaymentUrl($currentUrl);

            $postedAgreements = $session->getPostedAgreements(array());
            if (!$postedAgreements || array_diff($requiredAgreements, $postedAgreements)) {
                $this->_redirect('*/*/agreement', array('_secure' => true));
                return;
            }
        }

        $this->loadLayout();
        $this->getLayout()->getBlock('checkout.payment.method')->setMethod($methodInstance);
        $this->renderLayout();
    }

    public function agreementAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function agreementPostAction()
    {
        $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
        /* @var $session Mage_Checkout_Model_Session */
        $session = Mage::getSingleton('checkout/session');

        if ($requiredAgreements) {
            $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
            $diff = array_diff($requiredAgreements, $postedAgreements);

            if ($diff) {
                $session->addError($this->__('Please agree to all the terms and conditions before next step.'));
                $this->_redirect('*/*/agreement', array('_secure' => true));
                return;
            }
            $session->setData('posted_agreements', $postedAgreements);
        }

        $paymentUrl = $session->getPaymentUrl();
        if($paymentUrl) {
            $this->_redirectUrl($paymentUrl);
            return;
        }
        $this->_fault('You need visit magejam/payment/index first');
    }

    public function backPaymentAction()
    {
        $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
        if ($requiredAgreements) {
            $this->_redirect('*/*/agreement', array('_secure' => true));
            return;
        }
        $this->_forward('backAgreement');
    }

    public function backAgreementAction()
    {
        /* @var $session Mage_Checkout_Model_Session */
        $session = Mage::getSingleton('checkout/session');
        $session->setSuccess(false);
        $session->setClickedBack(true);
        $session->setGotoSection('shipping_method');
        $this->_redirect('magejam/payment/result', array('_secure' => true));
    }

    /**
     * Used for setting error message into session and redirection to result page
     *
     * @param $message
     */
    protected function _fault($message)
    {
        /* @var $session Mage_Checkout_Model_Session */
        $session = Mage::getSingleton('checkout/session');
        $session->setSuccess(false);
        $session->addError($message);
        $this->_redirect('magejam/payment/result', array('_secure' => true));
    }


    /**
     * Action for saving entered data in to quote
     */
    public function saveAction()
    {
        /* @var $session Mage_Checkout_Model_Session */
        $session = Mage::getSingleton('checkout/session');
        try {
            if (!$this->getRequest()->isPost()) {
                $this->_fault('HTTP Method must be post');
                return;
            }

            $data = $this->getRequest()->getPost('payment', array());
            $quoteId = $this->getRequest()->getPost('quote_id', null);

            /* @var $quote Mage_Sales_Model_Quote */
            $quote = $this->_getQuote($quoteId);
            Mage::getSingleton('customer/session')->setCustomer($quote->getCustomer());
            /* @var $onepage Mage_Checkout_Model_Type_Onepage */
            $onepage = Mage::getSingleton('checkout/type_onepage');
            $onepage->setQuote($quote);
            $onepage->getCheckout()->setQuoteId($quoteId);
            $onepage->savePayment($data);

            $redirectUrl = $onepage->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if ($redirectUrl) {
                $this->_redirectUrl($redirectUrl);
                return;
            }
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $session->setData('fields', $e->getFields());
            }
            $session->addError($e->getMessage());
            $this->_fault($e->getMessage());
            return;
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
            $this->_fault($e->getMessage());
            return;
        } catch (Exception $e) {
            Mage::logException($e);
            $session->addError($this->__('Unable to set Payment Method.'));
            $this->_fault($e->getMessage());
            return;
        }

        if($this->_saveOrder()) {
            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
        }

        if (!isset($redirectUrl)) {
            $redirectUrl = Mage::getUrl('checkout/onepage/success', array('_secure' => true));
        }
        $this->_redirectUrl($redirectUrl);
    }


    /**
     * Create order action (copied & modified from Mage_Checkout_OnepageController::saveOrderAction())
     */
    protected function _saveOrder()
    {
        /* @var $session Mage_Checkout_Model_Session */
        $session = Mage::getSingleton('checkout/session');
        try {
            $data = $this->getRequest()->getPost('payment', array());
            if ($data) {
                $data['checks'] = WSC_MageJam_Helper_Data::CHECK_USE_CHECKOUT
                    | WSC_MageJam_Helper_Data::CHECK_USE_FOR_COUNTRY
                    | WSC_MageJam_Helper_Data::CHECK_USE_FOR_CURRENCY
                    | WSC_MageJam_Helper_Data::CHECK_ORDER_TOTAL_MIN_MAX
                    | WSC_MageJam_Helper_Data::CHECK_ZERO_TOTAL;
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }
            $this->getOnepage()->saveOrder();
            $success = true;
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $session->addError($message);
            }
            $success = false;
            $session->setData('goto_section', 'payment');
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());

            $session->addError($e->getMessage());
            $success = false;

            $gotoSection = $this->getOnepage()->getCheckout()->getGotoSection();
            if ($gotoSection) {
                $session->setData('goto_section', $gotoSection);
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }
            $updateSection = $this->getOnepage()->getCheckout()->getUpdateSection();
            if ($updateSection) {
                $session->setData('update_section', $updateSection);
                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $success = false;
            $session->addError($this->__('There was an error processing your order. Please contact us or try again later.'));
        }
        $this->getOnepage()->getQuote()->save();

        $session->setData('magejam', true);

        $session->setSuccess($success);
        return $success;
    }

    /**
     * Get one page checkout model
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }


    /**
     * Used for displaying results in json format
     */
    public function resultAction()
    {
        /* @var $session Mage_Checkout_Model_Session */
        $session = Mage::getSingleton('checkout/session');
        $session->unsetData('posted_agreements');
        $result = array();

        if(is_null($session->getSuccess())) {
            $result['success'] = false;
            $indexUrl = Mage::getUrl('magejam/payment/index', array('_secure' => true));
            $result['errors'][] = $this->__('Session is empty, you should start payment once again from ' . $indexUrl);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        }

        $result['success'] = $session->getSuccess();
        if($session->getCanceled()) {
            $result['canceled'] = $session->getCanceled();
            $session->unsetData('canceled');
        }
        if($session->getClickedBack()) {
            $result['clicked_back'] = $session->getClickedBack();
            $session->unsetData('clicked_back');
        }
        if ($result['success']) {
            $result['order_id'] = $session->getOrderId();
            $result['order_increment_id'] = $session->getLastRealOrderId();
        } else {
            foreach ($session->getMessages(true)->getErrors() as $error) {
                $result['errors'][] = $error->getText();
            }
            if($session->hasData('goto_section')) {
                $result['goto_section'] = $session->getData('goto_section');
            } else {
                $result['goto_section'] = 'cart';
            }
            if($session->hasData('update_section')) {
                $result['update_section'] = $session->getData('update_section');
            }
            if($session->hasData('fields')) {
                $result['fields'] = $session->getData('fields');
            }
        }

        $session->unsetData('magejam');
        $session->unsetData('success');
        Mage::getSingleton('customer/session')->logout();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Loads quote
     *
     * @param $quoteId
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote($quoteId)
    {
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getModel("sales/quote");
        $storeId = Mage::app()->getStore()->getId();
        $quote->setStoreId($storeId)
            ->load($quoteId);

        return $quote;
    }
}