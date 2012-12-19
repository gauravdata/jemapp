<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro plugins
 */
class BusinessCatalyst_RetrieveCase extends BusinessCatalyst_Retrieve {

    private $visitorId = '';
    
    public function retrieveVisitorId($entityId) {
        $this->sendRequest($entityId);
        
        return $this->visitorId;
    }

    private function sendRequest($entityId) {
        $xmlRequest = $this->getXmlRequest(
        Gpf_Settings::get(BusinessCatalyst_Config::LOGIN),
        Gpf_Settings::get(BusinessCatalyst_Config::PASSWORD),
        Gpf_Settings::get(BusinessCatalyst_Config::SITE_ID),
        $entityId);
          
        $headers = $this->getPostHeader(strlen($xmlRequest), 'CaseList_EntityRetrieve');
        $response = $this->executeCurl($xmlRequest, $headers);

        if (strpos($response,'ERROR: No cases found for this entity') !== false) {
            return;
        }
        
        $this->visitorId = $this->getEntityVisitorId($response, Gpf_Settings::get(BusinessCatalyst_Config::PAP_CUSTOM_FIELD_NAME));
    }

    private function getEntityVisitorId($xml, $customFieldName) {
        $fieldName = '<fieldName>'.$customFieldName.'</fieldName>';
        $fieldValueLength = strlen('<fieldvalue>');
         
        $begin = strpos(strtolower($xml), strtolower($fieldName)) + strlen($fieldName);
        $begin = strpos(strtolower($xml), strtolower('<fieldValue>'), $begin) + strlen('<fieldValue>');
        
        $end = strpos(strtolower($xml), strtolower('</fieldValue>'), $begin);
        
        $fieldValue = substr($xml, $begin, $end - $begin - $fieldValueLength);
        
        if (($visitorIdEnd = strpos($fieldValue, ';')) !== false) {
            $fieldValue = substr($fieldValue, 0, $visitorIdEnd);
        }
        
        return $fieldValue;
    }

    private function getXmlRequest($username, $password, $siteId, $entityId) {
        $xmlRequest = '<?xml version="1.0" encoding="utf-8"?>
		<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
				xmlns:xsd="http://www.w3.org/2001/XMLSchema"
				xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			<soap:Body>
				<CaseList_EntityRetrieve xmlns="http://tempuri.org/CatalystDeveloperService/CatalystCRMWebservice">
                  <username>'.$username.'</username>
                  <password>'.$password.'</password>
                  <siteId>'.$siteId.'</siteId>
                  <entityId>'.$entityId.'</entityId>
                  <recordStart>0</recordStart>
                  <moreRecords>false</moreRecords>
    			</CaseList_EntityRetrieve>
			</soap:Body>
		</soap:Envelope>';
        return $xmlRequest;
    }

}

?>
