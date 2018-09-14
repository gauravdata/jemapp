<?php
class AW_Colorswatches_Model_Resource_Swatchattribute extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('awcolorswatches/swatchattribute', 'entity_id');
    }
}