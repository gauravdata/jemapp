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
class Pap_Tracking_LinkCloakerGenerator extends Gpf_Object {

    /**
     * @service link_cloaker read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);

        $data->setValue("urlToProtect", Pap_Affiliates_MainPanelHeader::getAffiliateLink());

        return $data;
    }

    /**
     * Generate cloaked file
     *
     * @service link_cloaker read
     * @param Gpf_Rpc_Url
     */
    public function generateFile(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $urlToProtect = str_replace(array("H_","S_"),array("http://","https://"),$form->getFieldValue("urlToProtect"));
        $redirectionType = $form->getFieldValue("redirectionType");

        switch($redirectionType) {
            case 'PHP':
                $download = $this->createPHPCloaker($urlToProtect);
                break;
            case 'HTML':
                $download = $this->createHTMLCloaker($urlToProtect);
                break;
            default:
                $download = $this->createJavaScriptCloaker($urlToProtect);
        }

        $download->setAttachment(true);
        return $download;
    }

    /**
     * @return Gpf_File_Download_String
     */
    private function createPHPCloaker($urlToProtect) {
        return $this->createDownloadFile('redir.php',
                                         '<?php header("Location: '.$urlToProtect.'"); ?>');
    }

    /**
     * @return Gpf_File_Download_String
     */
    private function createHTMLCloaker($urlToProtect) {
        return $this->createDownloadFile('redir.html',
                                         '<html><body><META HTTP-EQUIV="Refresh" CONTENT="0;URL='.$urlToProtect.'"></body></html>');
    }

    /**
     * @return Gpf_File_Download_String
     */
    private function createJavaScriptCloaker($urlToProtect) {
        return $this->createDownloadFile('redir.html',
                                         '<html><head><SCRIPT type="text/javascript"> window.location="'.$urlToProtect.'";</SCRIPT></head><body</body></html>');
    }

    private function createDownloadFile($fileName, $urlToProtect) {
    	$download = new Gpf_File_Download_String($fileName, $urlToProtect);
    	$download->setAttachment(true);
    	return $download;
    }
}

?>
