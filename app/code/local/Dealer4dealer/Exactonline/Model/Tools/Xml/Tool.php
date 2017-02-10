<?php
class Dealer4dealer_Exactonline_Model_Tools_Xml_Tool extends Mage_Core_Model_Abstract
{
    /**
     * Get XML template for type.
     *
     * @param $type
     * @return SimpleXMLElement
     */
    public function getXmlTemplate($type)
    {
        $tool = Mage::getSingleton('exactonline/tools_tooling');

		switch($type){
			case "Address":
				$xmlLocation = '/lib/xmlTemplates/Address.xml';
				break;
			case "Product":
				$xmlLocation = '/lib/xmlTemplates/Product.xml';
				break;
			case "Customer":
				$xmlLocation = '/lib/xmlTemplates/Customer.xml';
				break;
			case "Invoice":
				$xmlLocation = '/lib/xmlTemplates/Invoice.xml';
				break;
			case "CreditInvoice":
				$xmlLocation = '/lib/xmlTemplates/CreditInvoice.xml';
				break;
			case "InvoiceBulk":
				$xmlLocation = '/lib/xmlTemplates/InvoiceBulk.xml';
				break;
			case "SalesOrderBulk":
				$xmlLocation = '/lib/xmlTemplates/SalesOrderBulk.xml';
				break;
			case "Transaction":
				$xmlLocation = '/lib/xmlTemplates/Transaction.xml';
				break;
			case "ProductBulk":
				$xmlLocation = '/lib/xmlTemplates/ProductBulk.xml';
				break;
			case "CustomerBulk":
				$xmlLocation = '/lib/xmlTemplates/CustomerBulk.xml';
				break;
			case 'CreditInvoiceBulk':
				$xmlLocation = '/lib/xmlTemplates/CreditInvoiceBulk.xml';
				break;
			case 'Deliveries':
				$xmlLocation = '/lib/xmlTemplates/Deliveries.xml';
				break;
            case 'Memorial':
                $xmlLocation = '/lib/xmlTemplates/Memorial.xml';
                break;
			default:
				$xmlLocation = '/lib/xmlTemplates/Empty.xml';
				break;
		}

        $xmlLocation = $tool->getModuleDir() . $xmlLocation;

        $xml = simplexml_load_file($xmlLocation, NULL, LIBXML_NOCDATA);

        return Mage::getModel('exactonline/tools_xml_extended', $xml->asXML());
	}

	public function strToXml($string)
	{
		$xml = simplexml_load_string($string);
		if($xml) {
            return Mage::getModel('exactonline/tools_xml_extended', $xml->asXML());
		}else {
			return false;
		}
	}
}