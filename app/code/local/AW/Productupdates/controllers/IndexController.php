<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Productupdates
 * @version    2.1.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Productupdates_IndexController extends Mage_Core_Controller_Front_Action
{
    const PUN_COOKIE = 'pun';
    
    const PUN_COOKIE_NAME = 'pun_customer_name';
    
    protected $_cache = array();

    public function subscribeAction()
    {
        $this->loadLayout();
        /* always render subscribe form for configurable products */
        $product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id', false));
        if (!$product->getId()
            || ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
            && $product->getIsSalable())) {
            $this->_renderBlock('productupdates/subscribe');
            return $this;
        }

        $session = Mage::getSingleton('customer/session');
        if ($session->isLoggedIn()) {
            $customer = $session->getCustomer();
            $userInfo = $this->_combineData()
                ->setCustomer($customer)
                ->setEmail($customer->getEmail())
                ->setFullname($customer->getName())
                ->setRegId($customer->getId())
            ;
            $this->_model()->subscribeUser($userInfo);
            $this->_renderBlock('productupdates/subscribesuccess');
        } else {
            $email = $this->getRequest()->getCookie(self::PUN_COOKIE);
            if ($email) {
                $activeSubscriber = $this->_model()->validateEmail($email);
                if ($activeSubscriber) {
                    $userInfo = $this->_combineData()->setCookieSubscriber($activeSubscriber);
                    $this->_model()->subscribeUser($userInfo);
                    $this->_renderBlock('productupdates/subscribesuccess');
                    return $this;
                }
            }
            $this->_renderBlock('productupdates/subscribe');
        }
        return $this;
    }

    protected function _combineData()
    {
        $data = new Varien_Object(
            array(
                'product_id' => $this->getRequest()->getParam('id'),
                'subscr_store_id' => Mage::app()->getStore()->getId(),
                'reg_id'    => $this->_helper()->getCustomerIdentity(),
                'subscription_type' => $this->_helper()->getSubscriptionType(),
                'subscription_date' => $this->_model('core/date')->gmtDate(),
                'website_stores' => Mage::app()->getWebsite()->getStoreCollection()->getAllIds(),
                'parent' => null,
                'additional' => $this->_packAdditional()
            )
        );

        $childProduct = $this->_getChildIdentity();

        if ($childProduct) {
            $data->setProductId($childProduct);
            $data->setParent($this->getRequest()->getParam('id'));
            if (!$this->_cache['child']->getIsSalable()) {
                $data->setSubscriptionType(AW_Productupdates_Model_Source_SubscriptionTypes::WAITING_STOCK_CHANGE);
            } else {
                $data->setSubscriptionType(AW_Productupdates_Model_Source_SubscriptionTypes::WAITING_PRICE_CHANGE);
            }
        }

        return $data;
    }

    protected function _getChildIdentity()
    {
        $data = $this->_helper()->wrapRequestParams();
        if (!$data->getSuperAttribute()) {
            return null;
        }

        $product = Mage::getModel('catalog/product')->load($data->getId());
        if (!$product->getId()) {
            return null;
        }

        $child = $product->getTypeInstance(true)->getProductByAttributes($data->getSuperAttribute(), $product);
        if ($child instanceof Varien_Object) {
            $this->_cache['child'] = $child;
            return $child->getId();
        }
        return null;
    }

    protected function _packAdditional()
    {
        $data = $this->_helper()->wrapRequestParams();
        return $data->toJSON();
    }

    protected function _renderBlock($type)
    {
        $this->_initLayoutMessages('customer/session');
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock($type)
                ->addData((array) Mage::getSingleton('customer/session')->getFormData())
                ->toHtml()
        );
    }

    protected function _model($type = 'productupdates/subscribers')
    {
        return Mage::getModel($type);
    }

    protected function _singleton($type = 'catalog/session')
    {
        return Mage::getSingleton($type);
    }

    protected function _helper($type = 'productupdates')
    {
        return Mage::helper($type);
    }

    public function subscriptionsendAction()
    {
        /* get configurable item child by attributes */
        $data = $this->_helper()->wrapRequestParams();
        $post = $data->getSubscribe();

        try {
            if (!is_array($post) || !isset($post['email']) || !isset($post['fullname'])) {
                throw new Exception($this->_helper()->__('Subscription data is invalid'));
            }
            $product = $this->_model('catalog/product')->load($data->getId());
            if (!$product->getId()) {
                throw new Exception($this->_helper()->__('Invalid product data'));
            }
             
            $name = strip_tags(trim($post['fullname']));
            if (!Zend_Validate::is($name, 'NotEmpty')) {
                $error = true;
            }
            $email = trim($post['email']);
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $error = true;
            }
            if (isset($error)) {
                throw new Exception(
                    $this->_helper()->__('Registration failed. Be sure that you have entered valid Email and Full name')
                );
            }
        } catch (Exception $e) {
            $this->_singleton()->addError($this->_helper()->__($e->getMessage()));
            return $this->getResponse()->setRedirect($this->_getRefererUrl());
        }

        $this->_model('core/cookie')->set(self::PUN_COOKIE, $email, null, '/');
        $this->_model('core/cookie')->set(self::PUN_COOKIE_NAME, $name, null, '/');
        
        $userInfo = $this->_combineData()->setFullname($post['fullname'])->setEmail($post['email']);
        try {
            $this->_model()->subscribeUser($userInfo);
        } catch (Exception $e) {
            $this->_singleton()->addError($this->_helper()->__('Registration failed ('.$e->getMessage()));
            return $this->getResponse()->setRedirect($this->_getRefererUrl());
        }

        $this->_singleton()->addSuccess(
            $this->_helper()->__('You have been subscribed to %s updates', $product->getName())
        );
        $this->getResponse()->setRedirect($this->_getRefererUrl());
        return $this;
    }

    public function unsubscribeAction()
    {
        $params = $this->_helper()->decrypt($this->getRequest()->getParams());
        if (!$this->_validateParams($params, true)) {
            return $this->_redirectReferer();
        }

        try {
            if (!$this->_model('productupdates/productupdates')->clearByParams($params)) {
                $this->_singleton()->addError($this->_helper()->__('Unsubscription error'));
            } else {
                $this->_singleton()->addSuccess(
                    $this->_helper()->__(
                        'You have been unsubscribed from %s notifications',
                        $this->_model('catalog/product')->load($params['prod'])->getName()
                    )
                );
            }
        } catch (Exception $e) {
            $this->_helper()->log($e);
            $this->_singleton()->addError($this->_helper()->__('Unsubscription Error'));
        }

        $redirectParams = array(
            'id' => $params['catalog_prod']
        );
        if (array_key_exists('store', $params)) {
            $redirectParams['_store'] = intval($params['store']);
            $redirectParams['_store_to_url'] = true;
        }
        $this->_redirect("catalog/product/view/", $redirectParams);
        return $this;
    }

    public function unsubscribeallAction()
    {
        $params = $this->_helper()->decrypt($this->getRequest()->getParams());
        if (!$this->_validateParams($params)) {
            return $this->_redirectReferer();
        }

        $subscriber = $this->_model()->load($params['key']);
        if (!$subscriber->getId()) {
            return $this->_redirectReferer();
        }

        try {
            $subscriber->delete();
            $this->_singleton()->addSuccess($this->_helper()->__('You have been unsubscribed from all notifications'));
        } catch (Exception $e) {
            $this->_helper()->log($e);
            $this->_singleton()->addError($this->_helper()->__('Unsubscription Error'));
        }

        $this->_redirect("catalog/product/view/", array('id' => $params['catalog_prod']));
        return $this;
    }

    private function _validateParams($params, $deep = false)
    {
        if (!isset($params['key']) || !(int) $params['key'] || !isset($params['prod']) || !(int) $params['prod']) {
            return false;
        }
        if ($deep && (!isset($params['store']) || !isset($params['type']) || !(int) $params['type'])) {
            return false;
        }
        return true;
    }

}
