<?php

class Mage_Pap_Block_GoogleCheckout_Link extends Mage_GoogleCheckout_Block_Link
{
    public function _toHtml()
    {
        $html = parent::_toHtml();
        
        $config = Mage::getSingleton('pap/config'); // we'll need this
        
        $id = 'pap_x2s6df8d';
        global $have_pap_x2s6df8d;
        if (isset($have_pap_x2s6df8d) && $have_pap_x2s6df8d)
        {
          $id = 'pap_x2s6df8d_salestrack';
        }
        $have_pap_x2s6df8d = true;
        
        // Append the script to make the affiliate tracking work
        $html .= '<script id="'.$id.'" src="'.$config->getRemotePath().'/scripts/salejs.php" type="text/javascript"></script>';

        // Add a bit of script to add the additional hidden field to the form(s) with the link
        $html .= '<script type="text/javascript">';
        $html .= "var AnalyticsDataFields = document.getElementsByName('analyticsdata');";
        $html .= "for(var i = 0; i < AnalyticsDataFields.length; i++)";
        $html .= "{";
        $html .= "   var newinput = document.createElement('input');";
        $html .= "   newinput.setAttribute('type','hidden');";
        $html .= "   newinput.setAttribute('id','pap_ab78y5t4a_'+i);";
        $html .= "   newinput.setAttribute('name','pap-cookie-data');";
        $html .= "   AnalyticsDataFields[i].parentNode.insertBefore(newinput,AnalyticsDataFields[i].nextSibling);";
        // Write the tracking data to the form, rather than registering the sale immediately
        $html .= "   PostAffTracker.writeCookieToCustomField('pap_ab78y5t4a_'+i);";
        $html .= "}";
        $html .= '</script>';
        
        return $html;
    }
}