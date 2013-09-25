<?php
/**
* @copyright Amasty.
*/  
class Amasty_Shopby_Block_Featured extends Mage_Core_Block_Template
{
    private $items = array();
    
    protected function _prepareLayout()
    {
        // get filter ID by attribute code 
        $id = Mage::getResourceModel('amshopby/filter')
            ->getIdByCode($this->getAttributeCode());
            
        if ($id){
            $this->items = Mage::getResourceModel('amshopby/value_collection')
                ->addFieldToFilter('is_featured', 1)
                ->addFieldToFilter('filter_id', $id);
                
            if ($this->getRandom()){
                $this->items->setOrder('rand()');
            } 
            else {
                $this->items->setOrder('title', 'asc');    
            }  
             
            if ($this->getLimit()){
                $this->items->setPageSize(intVal($this->getLimit()));
            }   
                
            $hlp = Mage::helper('amshopby/url');
            $base = Mage::getBaseUrl('media') . 'amshopby/';
            foreach ($this->items as $item){
                $item->setImgBig($base . $item->getImgBig());   
                
                $attrCode = $this->getAttributeCode();
                $optLabel = $item->getTitle();
                $optId    = $item->getOptionId();
                $item->setUrl($hlp->getOptionUrl($attrCode, $optLabel, $optId));   
            }
        }
        
        return parent::_prepareLayout();
    }
    
    public function getItems()
    {
        return $this->items;
    }

}