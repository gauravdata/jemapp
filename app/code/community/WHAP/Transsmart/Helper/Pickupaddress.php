<?php
class WHAP_Transsmart_Helper_Pickupaddress extends Transsmart_Shipping_Helper_Pickupaddress
{
	/**
	 * @param Mage_Sales_Model_Quote $quote
	 * @param Mage_Sales_Model_Quote_Address $pickupAddress
	 * @return $this
	 */
	public function setQuotePickupAddress($quote, $pickupAddress)
	{
		$currentPickupAddress = null;
		foreach ($quote->getAddressesCollection() as $_address) {
			if (!$_address->isDeleted() && $_address->getAddressType() == 'transsmart_pickup') {
				$currentPickupAddress = $_address;
				break;
			}
		}

		if (!empty($currentPickupAddress)) {
			$currentPickupAddress->addData($pickupAddress->getData());
		}
		else {
			$quote->addAddress($pickupAddress->setAddressType('transsmart_pickup'));
		}

		/** @var Mage_Sales_Model_Quote_Address $shippingAddress */
		$shippingAddress = $quote->getShippingAddress();
		$shippingAddress->setPrefix($pickupAddress->getPrefix())
		                ->setFirstname($pickupAddress->getFirstname())
		                ->setMiddlename($pickupAddress->getMiddlename())
		                ->setLastname($pickupAddress->getLastname())
		                ->setSuffix($pickupAddress->getSuffix())
		                ->setTelephone($pickupAddress->getTelephone())
		                ->setCompany($pickupAddress->getCompany())
		                ->setPostcode($pickupAddress->getPostcode())
		                ->setCity($pickupAddress->getCity())
		                ->setCountryId($pickupAddress->getCountryId())
		                ->setStreetFull(array($pickupAddress->getStreetFull(),$pickupAddress->getCompany(),$pickupAddress->getTranssmartServicepointId()));

		return $this;
	}
}