<?php
 
class Icreators_Emalo_Model_Emalo extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('Emalo/Emalo');
    }
}