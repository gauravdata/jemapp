<?php

class Mage_Pap_Block_GoogleCheckout_Redirect extends Mage_GoogleCheckout_Block_Redirect
{
    public function getMethod ()
    {
        return 'POST'; // we're stuffing arbitrary data through, so it'll have to be posted
    }
    
    protected function _toHtml()
    {
        $form = new Varien_Data_Form();
        $form->setAction($this->getTargetURL())
            ->setId($this->getFormId())
            ->setName($this->getFormId())
            ->setMethod($this->getMethod())
            ->setUseContainer(true);
        foreach ($this->_getFormFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }
        
        //*******************************************
        // BEGIN PAP TRACKING EDITS
        //*******************************************
        
        $config = Mage::getSingleton('pap/config'); // we'll need this
        
        // Add a special field to hold the affiliate cookie data
        $form->addField('pap_ab78y5t4a', 'hidden', array('name'=>'pap-cookie-data', 'id'=>'pap_ab78y5t4a', 'value'=>''));
        
        //*******************************************
        // END PAP TRACKING EDITS
        //*******************************************
        
        $html = $form->toHtml();
        
        //*******************************************
        // BEGIN PAP TRACKING EDITS
        //*******************************************
        
        $id = 'pap_x2s6df8d';
        global $have_pap_x2s6df8d;
        if (isset($have_pap_x2s6df8d) && $have_pap_x2s6df8d)
        {
          $id = 'pap_x2s6df8d_salestrack';
        }
        $have_pap_x2s6df8d = true;
        
        // Append the script to make the affiliate tracking work
        $html.= '<script id="'.$id.'" src="'.$config->getRemotePath().'/scripts/salejs.php" type="text/javascript"></script>';
        $html.= '<script type="text/javascript">';

        // Write the tracking data to the form, rather than registering the sale immediately
        $html.= 'PostAffTracker.writeCookieToCustomField(\'pap_ab78y5t4a\');';
        $html.= '</script>';
        
        //*******************************************
        // END PAP TRACKING EDITS
        //*******************************************
        
        $html.= '<script type="text/javascript">document.getElementById("' . $this->getFormId() . '").submit();</script>';
        return $html;
    }
}