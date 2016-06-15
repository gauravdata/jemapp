<?php
class Dealer4dealer_Syncinfo_Block_Catalog_Product_Grid extends Dealer4dealer_Syncinfo_Block_Grid_Abstract
{
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }

    protected function _getValue(Varien_Object $row)
    {
        $syncState = $row->getExactSyncState();

        $date  = Mage::helper('core')->formatTime($row->getLastSync(), Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, true);

        if(is_null($syncState)) {
            $statusMessage = Mage::helper('core')->__('Not yet synchronized');
            return '<img src="'.$this->getMediaUrl('not-synced.png').'" title="'.$statusMessage.'" alt="'.$statusMessage.'" />';
        }elseif($syncState == 1) {
            return '<img src="'.$this->getMediaUrl('synced.png').'" title="'.$date.' - '.$row->getStatusMessage().'" alt="'.$row->getStatusMessage().'"  />';
        }else {
            return '<img src="'.$this->getMediaUrl('error.png').'" title="'.$date.' - '.$row->getStatusMessage().'" alt="'.$row->getStatusMessage().'"  />';
        }
    }
}