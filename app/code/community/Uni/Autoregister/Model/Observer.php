<?php

/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Autoregister
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
?>
<?php 
class Uni_Autoregister_Model_Observer {

    public function isGuest($observer)
    {
        $_order = $observer->getEvent()->getOrder();
        $_orderId = $_order->getId();
        $_autoRegHelper = Mage::helper('autoregister');
        //$_isGuest = Mage::getSingleton('checkout/session')->getIsGuest();
        $_isGuest =  $_order->getCustomerIsGuest();
        $_orderDetails = Mage::getModel('sales/order')->load($_orderId);

        if ($_autoRegHelper->isAutoRegistrationEnabled()) {
            if ($_isGuest) {
/////***Registration Start***/////////
                $_autoRegHelper = Mage::helper('autoregister');
                $_randPassword = $_autoRegHelper->getRandomPassword();

                $websiteId = Mage::app()->getWebsite()->getId();
                $store = Mage::app()->getStore();

                $customer = Mage::getModel("customer/customer");
                $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($_orderDetails->getCustomerFirstname())
                    ->setLastname($_orderDetails->getCustomerLastname())
                    ->setEmail($_orderDetails->getCustomerEmail())
                    ->setPassword($_randPassword);

                try {
                    Mage::log('Autoregister: Try to save customer ' . $_orderDetails->getCustomerEmail(), Zend_Log::DEBUG, 'twm.log');
                    $customer->save();
                    Mage::log('Autoregister: saved new customer '. $_orderDetails->getCustomerEmail(), Zend_Log::DEBUG, 'twm.log');
/////***Registration End***/////////
                } catch (Exception $e) {
                    Mage::log('Autoregister: '. json_encode($customer->debug()), Zend_Log::DEBUG, 'twm.log');
                    Mage::logException($e);
                    // customer already exists??
                    $customer = Mage::getModel('customer/customer')
                        ->setWebsiteId($websiteId)
                        ->loadByEmail($_orderDetails->getCustomerEmail());
                }

                try {
/////***Save Address Start***/////////

                    if ($customer->getId()) {
                        $_custid = $customer->getId();
                        Mage::log('Autoregister: set customer id "#'.$_custid.'" to order id "#'.$_order->getId().'"', Zend_Log::DEBUG, 'twm.log');
                        $_order->setCustomerId($_custid);
                        $_order->setCustomer($customer);
                        $_order->setCustomerGroupId(1);
                        $_order->setCustomerIsGuest(0);

                        $resource = Mage::getSingleton ( 'core/resource' );
                        $write = $resource->getConnection ( 'core_write' );
                        $write->query ( "UPDATE ".Mage::getSingleton('core/resource')->getTableName('sales_flat_order')." SET customer_id = '" . $_custid . "', customer_is_guest = '0', customer_group_id = '1' WHERE entity_id = '" . $_order->getId() . "'" );

                        $_orderBilling = $_orderDetails->getBillingAddress();
			$_orderShipping = $_orderDetails->getShippingAddress();

			$defaultBilling = $customer->getDefaultBillingAddress();
			$defaultShipping = $customer->getDefaultShippingAddress();
			$useForShipping = false;

			if (!$this->sameAddress($_orderBilling, $defaultBilling)) {
	                        $address = Mage::getModel("customer/address");
        	                $address->setCustomerId($_custid)
                	            ->setFirstname($customer->getFirstname())
                        	    ->setMiddleName($customer->getMiddlename())
	                            ->setLastname($customer->getLastname())
        	                    ->setCountryId($_orderBilling->getCountryId())
                	            //->setRegionId('1') //state/province, only needed if the country is USA
                        	    ->setPostcode($_orderBilling->getPostcode())
		                    ->setCity($_orderBilling->getCity())
                	            ->setTelephone($_orderBilling->getTelephone())
	                            ->setFax($_orderBilling->getFax())
	                            ->setCompany($_orderBilling->getCompany())
        	                    ->setStreet($_orderBilling->getStreet())
                	            ->setRegionId($_orderBilling->getRegionId())
	                            ->setIsDefaultBilling('1')
        	                    //        ->setIsDefaultShipping('1')
                	            ->setSaveInAddressBook('1');
			
				if ($this->sameAddress($_orderBilling, $_orderShipping)) {
					$address->setIsDefaultShipping('1');
					$useForShipping = true;
				}
        	                $address->save();
			}

			if (!$this->sameAddress($_orderBilling, $defaultShipping)) {
				if (!$useForShipping) {
		                        $address = Mage::getModel("customer/address");
        		                $address->setCustomerId($_custid)
                		            ->setFirstname($customer->getFirstname())
                        		    ->setMiddleName($customer->getMiddlename())
		                            ->setLastname($customer->getLastname())
        			            ->setCountryId($_orderShipping->getCountryId())
                	        	    //->setRegionId('1') //state/province, only needed if the country is USA
	                	            ->setPostcode($_orderShipping->getPostcode())
        	                	    ->setCity($_orderShipping->getCity())
	                	            ->setTelephone($_orderShipping->getTelephone())
        		              	    ->setFax($_orderShipping->getFax())
		        	            ->setCompany($_orderShipping->getCompany())
	                	            ->setStreet($_orderShipping->getStreet())
        	                	    ->setRegionId($_orderShipping->getRegionId())
	        	                    //->setIsDefaultBilling('1')
        	        	            ->setIsDefaultShipping('1')
		               	            ->setSaveInAddressBook('1');
	        	                $address->save();
				}
			}
                        
                    }
/////***Save Address End***/////////                
                } catch (Exception $e) {
                    Mage::logException($e);
		die($e->getMessage());
                }
                Mage::getSingleton('checkout/session')->unsIsGuest();
            }
        }
    }

    private function sameAddress($address1, $address2) {
	$excludeKeys = array('entity_id', 'customer_address_id', 'quote_address_id', 'region_id', 'customer_id', 'address_type', 'default_billing', 'default_shipping');
	if (!$address1) return false;
	if (!$address2) return false;
	$data1 = $address1->getData();
	$data2 = $address2->getData();
	$dataFiltered1 = array_diff_key($data1, array_flip($excludeKeys));
	$dataFiltered2 = array_diff_key($data2, array_flip($excludeKeys));

	$diff = array_diff($dataFiltered1, $dataFiltered2);
	if ($diff) {
		//Mage::log(implode('-', $diff), null, 'twm.log'');
		return false;
	}
	return true;
    }

}
