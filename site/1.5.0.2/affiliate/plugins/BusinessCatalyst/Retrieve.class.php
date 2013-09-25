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
class BusinessCatalyst_Retrieve  {

    public function retrieve() {
        $this->sendRequest();
    }

    protected function getPostHeader($length, $method) {
        return array(
        'POST '.'/catalystwebservice/catalystcrmwebservice.asmx'." HTTP/1.1",
        'HOST: '.Gpf_Settings::get(BusinessCatalyst_Config::BC_DOMAIN_NAME),
        'Content-Type: text/xml; charset=utf-8',
        'Content-Length: '.$length,
        'SOAPAction: '.'"http://tempuri.org/CatalystDeveloperService/CatalystCRMWebservice/'.$method.'"'
        );
    }

    protected function executeCurl($xmlRequest, $headers) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://'.Gpf_Settings::get(BusinessCatalyst_Config::BC_DOMAIN_NAME).'/catalystwebservice/catalystcrmwebservice.asmx');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);

        $response = curl_exec($ch);
        if ($response === false) {
            Pap_Contexts_Action::getContextInstance()->debug('Error in communication with bc: ' . curl_error($ch));
            throw new Gpf_Exception(curl_error($ch));
        }
        return $response;
    }

}

?>
