<?php
class Dealer4dealer_Exactonline_Model_Tools_Xml_Extended extends SimpleXMLElement
{
	public function addCData($cdata_text)
	{
		$node= dom_import_simplexml($this);
		$no = $node->ownerDocument;
		$node->appendChild($no->createCDATASection($cdata_text));
	}
}