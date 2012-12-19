<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
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
class Pap_Features_PapGeoip_UserMailExtension extends Gpf_Plugins_Handler {

    /**
     * @var Pap_Features_PapGeoip_UserMailExtension
     */
    private static $instance;

    /**
     * @return Pap_Features_PapGeoip_UserMailExtension
     */
    public static function getHandlerInstance() {
        if (self::$instance == null) {
            self::$instance = new Pap_Features_PapGeoip_UserMailExtension();
        }
        return self::$instance;
    }
    
    public function initUserMailTemplateVariables(Gpf_Mail_Template $template) {
        $template->addVariable('affiliateIpCountryCode', $this->_("Ip - Country Code"));
        $template->addVariable('affiliateIpCountryName', $this->_("Ip - Country Name"));
        $template->addVariable('affiliateIpCity', $this->_("Ip - City"));
        $template->addVariable('affiliateIpLongitude', $this->_("Ip - Longitude"));
        $template->addVariable('affiliateIpLatitude', $this->_("Ip - Latitude"));
    }
    
    /**
     * @return GeoIp_Location
     * @throws Gpf_Exception
     */
    protected function getLocation($ip) {
        $location = new GeoIp_Location();
        $location->setIpString($ip);
        $location->load();
        return $location;
    }

    public function setUserMailTemplateVariables(Pap_Mail_UserMail $template) {
        try {
            if ($template->getUser() == null) {
                throw new Gpf_Exception('User not set in UserMail');
            }
            $location = $this->getLocation($template->getUser()->getIp()); //-----------------------
            
            $template->setVariable('affiliateIpCountryCode', $location->getCountryCode());
            $template->setVariable('affiliateIpCountryName', $location->getCountryName());
            $template->setVariable('affiliateIpCity', $location->getCity());
            $template->setVariable('affiliateIpLongitude', $location->getLongitude());
            $template->setVariable('affiliateIpLatitude', $location->getLatitude());

        } catch (Gpf_Exception $e) {
            $template->setVariable('affiliateIpCountryCode', $this->_('Undefined'));
            $template->setVariable('affiliateIpCountryName', $this->_('Undefined'));
            $template->setVariable('affiliateIpCity', $this->_('Undefined'));
            $template->setVariable('affiliateIpLongitude', $this->_('Undefined'));
            $template->setVariable('affiliateIpLatitude', $this->_('Undefined'));
        }
    }
}
?>
