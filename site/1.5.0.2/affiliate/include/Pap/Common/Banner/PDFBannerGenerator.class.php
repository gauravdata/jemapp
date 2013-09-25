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
class Pap_Common_Banner_PDFBannerGenerator extends Pap_Common_Banner_PdfGenerator  {
	
	/**
	 * @var Pap_Affiliates_User
	 */
	private $affiliate;
	
    protected function setInitInfo() {
        $this->title = "";
        $this->creator = Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO);
        $this->author = "";
        $this->subject = "";
        $this->keywords = "";
        
        $this->fontInfo = array("font" => "freeserif",
                                "style" => "",
                                "size" => 10);
    }
    
    /**
     * Get pdf banner preview for merchant
     * 
     * @service banner read
     * @param Gpf_Rpc_Params $params
     */
    public function generatePdfFromHtml(Gpf_Rpc_Params $params) {  
        $form = new Gpf_Rpc_Form($params);   
        if ($form->existsField('html')) {
            $html = $form->getFieldValue('html');
        } else {
            throw new Gpf_Exception($this->_("Html data is not defined"));
        }
        
        if ($form->existsField('fileName')) {
            $fileName = $this->correctFilename($form->getFieldValue('fileName'));
        } else {
            throw new Gpf_Exception($this->_("File name is not defined"));
        }
    	
        if ($form->existsField('affiliate')) {
            try {
                $this->initAffiliate($form->getFieldValue('affiliate'));
            } catch (Gpf_DbEngine_NoRowException $e) {
            	return $this->getFormResponse($this->_('Affiliate is not defined'));
            }
        }
        $html = $this->decodeBanner($html, null, 0);
        $html = htmlspecialchars_decode($html);
        $this->generatePDF($html);
        
        return new Pap_Common_Banner_PdfGeneratorResponse($fileName, $this->pdf);        
    }
    
    /**
     * Get pdf banner for affiliate
     * 
     * @service banner read
     * @param Gpf_Rpc_Params $params
     */
    public function generateAffiliatePdf(Gpf_Rpc_Params $params) {
    	$form = new Gpf_Rpc_Form($params);
        $fileName = $this->correctFilename($form->getFieldValue('fileName'));
        
        try {
            $this->initAffiliate(Gpf_Session::getAuthUser()->getPapUserId());
        } catch (Gpf_DbEngine_NoRowException $e) {
            return $this->getFormResponse($this->_('Affiliate is not defined'));
        }
        
        $bannerFactory = new Pap_Common_Banner_Factory();
        try {
			$banner = $bannerFactory->getBanner($form->getFieldValue('bannerId'));
			
			$channel = $this->findChannel($form->getFieldValue('channel'));
			if($channel != null) {
				$banner->setChannel($channel);
			}
			
        	$html = $banner->get(Pap_Db_Table_Banners::DATA2);
        } catch (Pap_Common_Banner_NotFound $e) {
        	return $this->getFormResponse($this->_("Banner with id '".$params->get('bannerId')."' doesn't exist"));
        }
        
        $html = $this->decodeBanner($html, $banner, 0);
        $this->generatePDF($html);
        
        return new Pap_Common_Banner_PdfGeneratorResponse($fileName, $this->pdf);
    }
    
    private function correctFilename($filename) {
    	return str_replace('.pdf', '', $filename);
    }
    
    private function findChannel($channelId) {
    	$channel = new Pap_Db_Channel();
    	$channel->setPrimaryKeyValue($channelId);
    	$channel->set(Pap_Db_Table_Channels::USER_ID, Gpf_Session::getAuthUser()->getPapUserId());
    	try {
    		$channel->loadFromData(array('channelid', 'userid'));
    		return $channel;
    	} catch(Gpf_Exception $e) {
    	}
    	
    	return null;
    }
    
    private function initAffiliate($affiliateId) {
    	$this->affiliate = new Pap_Affiliates_User();
    	$this->affiliate->setPrimaryKeyValue($affiliateId);
        $this->affiliate->load();
        if ($this->affiliate->getType() != Pap_Application::ROLETYPE_AFFILIATE) {
        	throw new Gpf_Exception($this->_('User is not affiliate'));
        }
    }
    
    private function decodeBanner($html, $banner, $flags) {
    	if($banner == null) {
    		$banner = new Pap_Common_Banner_PDF();
    	}
    	
        $html = $banner->replaceUserConstants($html, $this->affiliate);
        $html = $banner->replaceUrlConstants($html, $this->affiliate, $flags, '');
        $html = $banner->replaceBannerConstants($html, $this->affiliate);
   	
    	return $html;
    }
    
    private function getFormResponse($message) {
    	$form = new Gpf_Rpc_Form();
    	$form->setErrorMessage($message);
    	
    	return $form;
    }
}

?>
