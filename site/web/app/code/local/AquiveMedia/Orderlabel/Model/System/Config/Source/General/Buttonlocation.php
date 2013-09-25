<?php
/**
 * Author: Jeroen Smit - Smit Webdevelopment - www.smit-web.nl
 * Contact: jeroen@smit-web.nl
 * Copyright: Aquive Media
 * Created: 12/2/11
 */
class AquiveMedia_Orderlabel_Model_System_Config_Source_General_Buttonlocation
{
    public function toOptionArray()
    {
        return Mage::helper('orderlabel')->getButtonlocationOptionsArray();
    }
}
