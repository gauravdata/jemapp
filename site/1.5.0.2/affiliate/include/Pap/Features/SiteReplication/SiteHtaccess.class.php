<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Features_SiteReplication_SiteHtaccess extends Gpf_Object {
    
	/**
	 * @service banner write
	 * @param $bannerId
	 * @return Gpf_Rpc_Action
	 */
    public function generate(Gpf_Rpc_Params $params) {
        $response = new Gpf_Rpc_Action($params);
        $response->setInfoMessage('Generate .htaccess is not implemented yet');
        $response->addOk();
        return $response;
    }
    
    /**
     * @service banner read
     * @param $bannerId
     * @return Gpf_Rpc_Action
     */
    public function check(Gpf_Rpc_Params $params) {
        $response = new Gpf_Rpc_Action($params);
        $bannerFactory = new Pap_Common_Banner_Factory();
        $site = $bannerFactory->getBanner($params->get('id'));
        $site->setDestinationUrl(rtrim($params->get('url'), "/\\").'/');
        
        $response->setInfoMessage('Site replication .htaccess is working at this location');
        $response->setErrorMessage('Site replication .htaccess is not set up at this location or it is not working correctly. Please make sure that you have mod_rewrite and mod_proxy enabled in your Apache configuration');
        
        $testUser = new Pap_Common_User();
        $testUser->setRefId(Pap_Features_SiteReplication_Replicator::TEST_STRING);
        $request = new Gpf_Net_Http_Request();
        $request->setUrl($site->getUrl($testUser).Pap_Features_SiteReplication_Replicator::TEST_STRING);
        $httpClient = new Gpf_Net_Http_Client();
        try {
            $testResponse = $httpClient->execute($request);
            if ($testResponse->getBody() == Pap_Features_SiteReplication_Replicator::TEST_RESPONSE) {
                $response->addOk();
            } else {
                $response->addError();                
            }
        } catch (Gpf_Exception $e) {
            $response->addError();
        }
        
        return $response;
    }
    
    /**
     * @service banner read
     * @param $bannerId
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
    	$form = new Gpf_Rpc_Form($params);
    	$bannerFactory = new Pap_Common_Banner_Factory();
    	$site = $bannerFactory->getBanner($form->getFieldValue(Gpf_View_FormService::ID));
    	$form->addField('htAccessCode', $site->getHtaccessCode());
    	return $form;
    }
}

?>
