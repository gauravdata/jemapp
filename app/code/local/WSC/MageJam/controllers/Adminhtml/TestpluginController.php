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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Testing plogin controller
 */
class WSC_MageJam_Adminhtml_TestpluginController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Return some checking result
     *
     * @return void
     */
    public function checkAction()
    {
        $customerId = $this->getRequest()->getParam('customerId');
        $storeId = $this->getRequest()->getParam('storeId');
        $result = array();
        $result['wsi'] = (bool) Mage::helper('api/data')->isComplianceWSI();
        $result['cache'] = (bool) Mage::getStoreConfig('api/config/wsdl_cache_enabled');
        if ($result['wsi'] && !empty($customerId) && !empty($storeId)){
            // check customer address
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $customerAddresses = array();
            foreach ($customer->getAddresses() as $address)
            {
                $customerAddresses[] = $address;
            }
            if (sizeof($customerAddresses) == 0){
                $result['customer_error'] = 'Please choose a customer with valid default shipping and billing address for testing shopping cart function';
            }else{
                try {
                    // create cart
                    /*@var $quote Mage_Sales_Model_Quote*/
                    $quote = Mage::getModel('sales/quote');
                    $quote->setStoreId($storeId)
                        ->setIsMultiShipping(false)
                        ->save();

                    // set shipping address
                    $address = Mage::getModel("sales/quote_address");
                    $address->importCustomerAddress($customerAddresses[0]);
                    $address->implodeStreetAddress();
                    $address->setCollectShippingRates(true)
                        ->setSameAsBilling(0);
                    $quote->setShippingAddress($address);

                    $quote->collectTotals()
                        ->save();

                    $session = Mage::getSingleton('checkout/session');
                    $session->replaceQuote($quote);

                    // get shipping method
                    $shippingMethods = $this->getLayout()->getBlockSingleton('checkout/onepage_shipping_method_available')->getShippingRates();
                    $strShippingMethods = '';
                    foreach ($shippingMethods as $code => $_rates){
                        $shipingMethodGroup = $this->getCarrierName($code);
                        foreach ($_rates as $_rate){
                            $strShippingMethods = $strShippingMethods.'<li>'.$shipingMethodGroup.' - '.$_rate->getMethodTitle().'</li>';
                        }
                    }
                    $result['shippingMethods'] = $strShippingMethods;

                    // get payment method
                    $strMethods = '';
                    $helper = Mage::helper('magejam');
                    foreach (Mage::helper('payment')->getStoreMethods($storeId, $quote) as $method) {
                        if ($helper->_canUseMethod($method)) {
                            $strMethods = $strMethods.'<li>'.$method->getTitle().'</li>';
                        }
                    }
                    $result['paymentMethods'] = $strMethods;

                    // delete the new cart
                    $quote->setIsActive(false);
                    $quote->delete();
                } catch (Mage_Core_Exception $e) {
                    Mage::logException(e);
                    $result['customer_error'] = 'testing_plugin_fault'.$e->getMessage();
                }
            }
        }
        Mage::app()->getResponse()->setBody(json_encode($result));
    }

    /**
     * get carrier name
     *
     * @param $carrierCode
     * @return string
     */
    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title')) {
            return $name;
        }
        return $carrierCode;
    }
}