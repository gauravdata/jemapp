<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Michal Bebjak
*   @since Version 1.0.0
*   $Id: LanguageAndDate.class.php 18081 2008-05-16 12:17:32Z mfric $
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
class Pap_Merchants_Config_UrlsAndDirectories extends Gpf_Object {
    const DEFAULT_CHOOSING_LANGUAGE = Gpf::YES;
    const DEFAULT_DATE_TIME_FORMAT = "d.M.yyyy k:mm:ss";

    /**
     * @service url_setting read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $form->setField(Pap_Settings::MAIN_SITE_URL,
            Gpf_Settings::get(Pap_Settings::MAIN_SITE_URL));

        return $form;
    }

    /**
     * @service url_setting write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        Gpf_Settings::set(Pap_Settings::MAIN_SITE_URL, $form->getFieldValue(Pap_Settings::MAIN_SITE_URL));

        $form->setInfoMessage($this->_("URLs & Directories saved"));

        return $form;
    }
}

?>
