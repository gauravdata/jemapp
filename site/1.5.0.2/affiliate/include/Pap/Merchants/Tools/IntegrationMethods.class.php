<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Google.class.php 18112 2008-05-20 07:17:10Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Merchants_Tools_IntegrationMethods extends Gpf_Object implements Gpf_Rpc_TableData  {

    /**
     * @service integration_methods read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Data_RecordSet
     */
    public function getRow(Gpf_Rpc_Params $params) {
        $integrationMethods = $this->getIntegrationMethodsList($params);
        $response = $integrationMethods->toShalowRecordSet();
        foreach ($integrationMethods as $row) {
            if ($row->get('integrationid') == $params->get(self::SEARCH)) {
                $response->add($row);
            }
        }
        return $response;
    }

    /**
     * @service integration_methods read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Data_Table
     */
    public function getRows(Gpf_Rpc_Params $params) {
        $data = new Gpf_Data_Table($params);
        $data->fill($this->getIntegrationMethodsList($params));
        return $data;
    }

    /**
     * Proxy request from server to Addons web server and return list of Integration methods
     * @service integration_methods read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Data_RecordSet
     */
    private function getIntegrationMethodsList(Gpf_Rpc_Params $params) {
        $proxyRequest = new Gpf_Rpc_Request('Aw_Db_Table_Integrations', 'getIntegrationsList');
        $this->sendRequest($proxyRequest, $params);     
        
        $recordSet = new Gpf_Data_RecordSet();
        $recordSet->loadFromObject($proxyRequest->getResponseObject()->toObject());
        $header = $recordSet->getHeader()->getIds();
        $header[0] = "id";
        $recordSet->setHeader($header);
        
        $this->processIntegrations($recordSet);
        
        return $recordSet;
    }

    private function processIntegrations(Gpf_Data_RecordSet $integrations) {
        foreach ($integrations as $integration) {
            $integration->set('description', $this->replaceBrandingTextVariables($integration->get('description')));
            $integration->set('description_footer', $this->replaceBrandingTextVariables($integration->get('description_footer')));
        }
    }
    
    /**
     * Proxy request from server to Addons web server and return list of Integration steps
     * @service integration_methods read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Data_RecordSet
     */
    public function getIntegrationStepsList(Gpf_Rpc_Params $params) {
        $proxyRequest = new Gpf_Rpc_Request('Aw_Db_Table_IntegrationSteps', 'getIntegrationStepsList');
        $this->sendRequest($proxyRequest, $params);         
        
        $recordSet = new Gpf_Data_RecordSet();
        $recordSet->loadFromObject($proxyRequest->getResponseObject()->toObject());
        
        $this->processIntegrationSteps($recordSet);
        
        return $recordSet;
    }

    private function processIntegrationSteps(Gpf_Data_RecordSet $integrationSteps) { 
        foreach ($integrationSteps as $integrationStep) {
            $integrationStep->set('text_code', $this->replaceBrandingTextVariables($integrationStep->get('text_code')));
            $integrationStep->set('text_before', $this->replaceBrandingTextVariables($integrationStep->get('text_before')));
            $integrationStep->set('text_after', $this->replaceBrandingTextVariables($integrationStep->get('text_after')));
            $integrationStep->set('name', $this->replaceBrandingTextVariables($integrationStep->get('name')));
        }        
    }

    private function sendRequest(Gpf_Rpc_Request $request, Gpf_Rpc_Params $params) {
        $request->setUrl('http://addons.qualityunit.com/scripts/server.php');
        // $request->setUrl('http://localhost/trunk/AddonsWeb/trunk/server/scripts/server.php');
        $params->add('application_code', Gpf_Application::getInstance()->getCode());
        $params->add('application_version', Gpf_Application::getInstance()->getVersion());
        $request->setParams($params);
        $request->sendNow();
    }
    
    private function replaceBrandingTextVariables($text) {
        $text = str_replace('{$PapAbbr}', Gpf_Settings::get(Pap_Settings::BRANDING_QUALITYUNIT_PAP), $text);
        $text = str_replace('{$PapFullname}', Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO), $text);
        return $text;      
    }
}

?>
