<?php
class Dealer4dealer_Syncinfo_Block_Grid_Abstract extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_mediaUrl = null;

    protected $_folder = 'exact';



    public function getMediaUrl($file)
    {

        if(is_null($this->_mediaUrl)) {
            $this->_mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        }

        return $this->_mediaUrl.$this->_folder.'/'.$file;
    }
}