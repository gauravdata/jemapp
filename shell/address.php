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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Shell
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_Address extends Mage_Shell_Abstract
{
    /**
     * Run script
     *
     */
    public function run()
    {
	$resource = Mage::getSingleton('core/resource');
	$db = $resource->getConnection('core_read');

	$query = '
		SELECT
		sales_flat_order.entity_id
		FROM
		sales_flat_order
		INNER JOIN customer_entity ON customer_entity.entity_id = sales_flat_order.customer_id
		LEFT JOIN customer_address_entity ON customer_address_entity.parent_id = customer_entity.entity_id AND customer_address_entity.parent_id = customer_entity.entity_id
		LEFT JOIN customer_address_entity_varchar ON customer_address_entity_varchar.entity_id = customer_address_entity.entity_id AND customer_address_entity_varchar.entity_id = customer_address_entity.entity_id aND customer_address_entity_varchar.attribute_id = 25
		WHERE customer_entity.created_at > DATE(\'2016-01-01\')
		AND customer_address_entity.updated_at is null
		LIMIT 500
	';
	//	AND sales_flat_order.state = \'processing\'
	$results = $db->fetchAll($query);

	foreach ($results as $result) {
		$_orderId = $result['entity_id'];
		$_orderDetails = Mage::getModel('sales/order')->load($_orderId);

		$_orderBilling = $_orderDetails->getBillingAddress();
                $_orderShipping = $_orderDetails->getShippingAddress();

	        $websiteId = 4;
        	$store = 0;

		$customer = Mage::getModel('customer/customer')
			->setWebsiteId($websiteId)
			->loadByEmail($_orderDetails->getCustomerEmail());
        try {
            if ($customer->getId()) {
				$useForShipping = false;
                $_custid = $customer->getId();
		echo $_custid . PHP_EOL;
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
		} catch (Exception $e) {
			echo $e->getMessage();
		}		
	}
    }

    private function sameAddress($address1, $address2) {
        $excludeKeys = array('entity_id', 'customer_address_id', 'quote_address_id', 'region_id', 'customer_id', 'address_type', 'default_billing', 'default_shipping');
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

$shell = new Mage_Shell_Address();
$shell->run();
