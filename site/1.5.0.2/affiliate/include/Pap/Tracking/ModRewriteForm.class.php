<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Maros Fric
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
class Pap_Tracking_ModRewriteForm extends Gpf_Object {

	/**
     * @service tracking_mod_rewrite read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $prefix = Gpf_Settings::get(Pap_Settings::MOD_REWRITE_PREFIX_SETTING_NAME);
        $separator = Gpf_Settings::get(Pap_Settings::MOD_REWRITE_SEPARATOR_SETTING_NAME);
        $suffix = Gpf_Settings::get(Pap_Settings::MOD_REWRITE_SUFIX_SETTING_NAME);

        $form->setField(Pap_Settings::MOD_REWRITE_PREFIX_SETTING_NAME, $prefix);
        $form->setField(Pap_Settings::MOD_REWRITE_SEPARATOR_SETTING_NAME, $separator);
        $form->setField(Pap_Settings::MOD_REWRITE_SUFIX_SETTING_NAME, $suffix);
        $form->setField("htaccess_code", $this->generateHtaccessCode($prefix, $separator, $suffix));

        return $form;
    }

    /**
     * @service tracking_mod_rewrite write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $prefix = $form->getFieldValue(Pap_Settings::MOD_REWRITE_PREFIX_SETTING_NAME);
        $separator = $form->getFieldValue(Pap_Settings::MOD_REWRITE_SEPARATOR_SETTING_NAME);
        $suffix = $form->getFieldValue(Pap_Settings::MOD_REWRITE_SUFIX_SETTING_NAME);
        $htaccessCode = $this->generateHtaccessCode($prefix, $separator, $suffix);

        if($separator == '') {
        	$form->setErrorMessage("Separator cannot be empty!");
        	return $form;
        }

        Gpf_Settings::set(Pap_Settings::MOD_REWRITE_PREFIX_SETTING_NAME, $prefix);
        Gpf_Settings::set(Pap_Settings::MOD_REWRITE_SEPARATOR_SETTING_NAME, $separator);
        Gpf_Settings::set(Pap_Settings::MOD_REWRITE_SUFIX_SETTING_NAME, $suffix);

        $form->setField("htaccess_code", $htaccessCode);

        $form->setInfoMessage($this->_("Changes saved"));

        Pap_Db_Table_CachedBanners::clearCachedBanners();

        return $form;
    }

    /**
     * generates code for .htaccess mod_rewrite
     *
     */
    private function generateHtaccessCode($prefix, $separator, $suffix) {
    	if($separator == '') {
    		$separator = '/';
    	}

    	$aid = Pap_Tracking_Request::getAffiliateClickParamName();
    	$bid = Pap_Tracking_Request::getBannerClickParamName();
    	$channel = Pap_Tracking_Request::getChannelParamName();
    	$data1 = Pap_Tracking_Request::getClickData1ParamName();

    	$tb = new Pap_Tracking_TrackerBase();
    	$scriptUrl = $tb->getScriptUrl("click.php");

    	$separators = str_replace($separator, '', '_-/');
    	
        $htaccessCode =
"# Start Post Affiliate SEO Code----\n
RewriteEngine On\n
RewriteRule ^".$prefix."([a-zA-Z0-9".$separators."]+)".$suffix."\$ $scriptUrl?$aid=\$1 [R=301,L]\n
RewriteRule ^".$prefix."([a-zA-Z0-9".$separators."]+)".$separator."([a-zA-Z0-9".$separators."]+)".$suffix."\$ $scriptUrl?$aid=\$1&$bid=\$2 [R=301,L]\n
RewriteRule ^".$prefix."([a-zA-Z0-9".$separators."]+)".$separator."([a-zA-Z0-9".$separators."]+)".$separator."([a-zA-Z0-9".$separators."]+)".$suffix."\$ $scriptUrl?$aid=\$1&$bid=\$2&$channel=\$3 [R=301,L]\n
RewriteRule ^".$prefix."([a-zA-Z0-9".$separators."]+)".$separator."([a-zA-Z0-9".$separators."]+)".$separator."([a-zA-Z0-9".$separators."]+)".$separator."([a-zA-Z0-9".$separators."]+)".$suffix."\$ $scriptUrl?$aid=\$1&$bid=\$2&$channel=\$3&$data1=\$4 [R=301,L]\n
# End of Post Affiliate SEO Code\n";

        return $htaccessCode;
    }
}


?>
