<?php
/**
 * Api class for response message reading
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
class Comaxx_Docdata_Model_Api_Response {

	const SYSTEM_EXCEPTION = 'SYSTEM_EXCEPTION',
		  /* Tell the getNode function you want multiple of a node type */
		  MULTIPLE = 'multiple',
		  /* Tell the getNode function you want a single (first) of a node type */
		  SINGLE = 'single',
		  /* Implied value used for the getNode function telling it you accept either multiple or single values */
		  UNDEFINED = 'undefined';

	private $_soap_response;

	/**
	 * Sets response in current handler
	 *
	 * @param string $soap_response Response of the soap message
	 *
	 * @return void
	 */	
	public function setResponse($soap_response) {
		// Remove namespaces and load as xml ( since namespaces are slightly problematic to work with together with xpath )
		// Get the used namespace
		preg_match('<[a-zA-Z-]*:Envelope xmlns:(?<ns>[^=]*)="[^"]*">', $soap_response, $matches);
		$ns = isset($matches['ns']) ? $matches['ns'] : null;

		// Remove definitions from xml
		$soap_response = preg_replace('/ ?xmlns[^=]*="[^"]*"/i', '', $soap_response);

		// Remove namespace elements from soap call
		$soap_response = preg_replace("~(?:<$ns:Envelope><$ns:Body>)|(?:</$ns:Body></$ns:Envelope>)~", '', $soap_response);
		
		$xml = new SimpleXMLElement($soap_response);
		// get response, first '*[1]' is for message being sent,
		// 2nd is to go into the wrapper of [message]Success or [message]/Error
		$content = $xml->xpath("/*[1]/*[1]");
		
		$this->_soap_response = $content[0];
	}
	
	/**
	 * Sets error response in current handler
	 *
	 * @param string $error_message Error message to use
	 *
	 * @return void
	 */	
	public function setErrorResponse($error_message) {
		//create eror class to simulate soap response error
		$content = "<?xml version='1.0' encoding='utf-8'?>";
		$content .= "<root><error code=\"".self::SYSTEM_EXCEPTION."\">".$error_message."</error></root>";
		$this->_soap_response = new SimpleXMLElement($content);
	}
	
	/**
	 * Checks if current handler contains an error
	 *
	 * @return boolean True if handler has error, otherwise False
	 */	
	public function hasError() {
		
		$error = $this->_soap_response->xpath('error');
		if ($this->_soap_response !== null && count($error) > 0) {
			return true;
		}
		return false;
	}
	
	/**
	 * Gets error message if any
	 *
	 * @return String Error message if any, otherwise null
	 */	
	public function getErrorMessage() {
		if ($this->hasError()) {
			$error = $this->_soap_response->xpath('error');
			return (string)$error[0];
		}
		return null;
	}
	
	/**
	 * Gets a node at the target path
	 * 
	 * @param string $path Path of the desired node
	 *
	 * @return array Found node
	 */
	public function getNode($path) {
		$xml = $this->_soap_response->xpath($path);
		return $xml;
	}
	
	/**
	 * Converts object to array
	 * 
	 * @param Object $obj Object to convert
	 * @param array $result Array to fill/update
	 * @param boolean $first Determines if this is the top level of the array.
	 *
	 * @return array Converted object as an array
	 */
	function convertXmlObjToArr($obj, &$result, $first) {
		//loop through objects children
		foreach ($obj as $elementName => $node) {
			//in case this is the top level of the result (first) dont set additional array
			if (!isset($result[$elementName]) && !$first) {
				//container not present yet, add it
				$result[$elementName] = array();
			}
			//build current array element to add
			$currentElement = array();
			
			//loop through attributes
			$attributes = $node->attributes(); 
			foreach ($attributes as $attributeName => $attributeValue) { 
				$attribName = strtolower(trim((string)$attributeName)); 
				$currentElement[$attribName] = trim((string)$attributeValue); 
			}
			
			//check if element is endelement containing a value ($text)
			$text = trim((string)$node); 
			if (strlen($text) > 0) { 
				$currentElement['_'] = $text; 
			}
			
			//recursive in case item has children
			$this->convertXmlObjToArr($node, $currentElement, false);
			
			//in case this is toplevel of result array (first) then dont use additional container element
			if ($first) {
				$result[] = $currentElement;
			} else {
				$result[$elementName][] = $currentElement;
			}
		} 
		return $result; 
	}  
}