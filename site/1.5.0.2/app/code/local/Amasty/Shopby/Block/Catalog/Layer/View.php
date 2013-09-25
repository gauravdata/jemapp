<?php
class Amasty_Shopby_Block_Catalog_Layer_View extends Mage_Catalog_Block_Layer_View
{
    protected $_filterBlocks = null;
    
    public function getFilters()
    {
        if (!is_null($this->_filterBlocks)){
            return $this->_filterBlocks;
        }
        
        //attributes ids
        $attrIds = array();

        // option ids
        $ids = array();
        
        $filters = parent::getFilters();
        
        // remove some filtes for the home page
        $exclude = Mage::getStoreConfig('amshopby/general/exclude');
        if ('/' == Mage::app()->getRequest()->getRequestString() && $exclude){
            $exclude = explode(',', preg_replace('/[^a-zA-Z0-9_\-,]+/','', $exclude));
            $filters = $this->excludeFilters($filters, $exclude);
        }
        
        foreach ($filters as $f){
            if ($f->getItemsCount() && ($f instanceof Mage_Catalog_Block_Layer_Filter_Attribute)){
                $items = $f->getItems();
                foreach ($items as $item){  
                    $vals =  explode(',', $item->getValue() ? $item->getValue() : $item->getOptionId());
                    foreach ($vals as $v) 
                        $ids[] = $v;
                }
                $attrIds[] = $items[0]->getFilter()->getAttributeModel()->getId();
            }
        }
        
        //additional filter properties
        $attrCollection = Mage::getResourceModel('amshopby/filter_collection')
            ->addFieldToFilter('attribute_id', array('in' => $attrIds)) 
            ->load();
                
        $attributes = array();
        foreach ($attrCollection as $row){
            $attributes[$row->getAttributeId()] = $row;
        }
        
        // images of filter values 
        $optionsCollection = Mage::getResourceModel('amshopby/value_collection')
            ->addFieldToFilter('option_id', array('in' => $ids)) 
            ->load();
                
        $options = array();        
        foreach ($optionsCollection as $row){
            $options[$row->getOptionId()] = array('img'=>$row->getImgSmall(), 'descr'=>$row->getDescr());
        }

        $catId = $this->getLayer()->getCurrentCategory()->getId();
        $exclude = array();
        
        // update filters with new properties
        foreach ($filters as $f){
            if ($f->getItemsCount() && $f instanceof Mage_Catalog_Block_Layer_Filter_Attribute){
                $items = $f->getItems();
               
                //add selected and image properties for all items                
                $attributeCode = $items[0]->getFilter()->getAttributeModel()->getAttributeCode();
                $selectedValues = Mage::app()->getRequest()->getParam($attributeCode);
                if ($selectedValues){
                    $selectedValues = explode(',', $selectedValues);
                    $f->setHasSelection(true);
                }
                else
                    $selectedValues = array();
                    
                foreach ($items as $item){ 
                    $optId = $item->getOptionId();
                    if (!empty($options[$optId]['img'])){
                        $item->setImage($options[$optId]['img']);
                    }
                    if (!empty($options[$optId]['descr'])){
                        $item->setDescr($options[$optId]['descr']);
                    }
                    
                    
                    $item->setIsSelected(in_array($optId, $selectedValues));
                }                    
                
                $attributeId  = $items[0]->getFilter()->getAttributeModel()->getId();
                if (isset($attributes[$attributeId])){
                    $a = $attributes[$attributeId];
                    $f->setMaxOptions($a->getMaxOptions());
                    $f->setHideCounts($a->getHideCounts());
                    $f->setSortBy($a->getSortBy());
                    $f->setDisplayType($a->getDisplayType());
                    $f->setSingleChoice($a->getSingleChoice());
                    if (Mage::getStoreConfig('amshopby/general/enable_collapsing'))
                        $f->setCollapsed($a->getCollapsed());
                    
                    $cats = trim($a->getExcludeFrom());
                    if ($cats){
                        if (in_array($catId, explode(',', $cats))){
                            $exclude[] = $attributeCode;
                        }
                    }
                } //if attibute
                
            }// if count items and attribute
            elseif ($f instanceof Mage_Catalog_Block_Layer_Filter_Category){
                $f->setDisplayType(Mage::getStoreConfig('amshopby/general/categories_type'));
                $f->setTemplate('amshopby/category.phtml'); 
                $f->setHasSelection($catId != $this->getLayer()->getCurrentStore()->getRootCategoryId());
                if (Mage::getStoreConfig('amshopby/general/enable_collapsing'))
                    $f->setCollapsed(Mage::getStoreConfig('amshopby/general/categories_collapsed'));
                
            }
            elseif ($f instanceof Mage_Catalog_Block_Layer_Filter_Price){
                $f->setDisplayType(Mage::getStoreConfig('amshopby/general/price_type'));
                $f->setTemplate('amshopby/price.phtml');
                $f->setHasSelection(isset($_GET['price']));
                //$f->setHasSelection(Mage::app()->getRequest()->getParam('price'));
                if (Mage::getStoreConfig('amshopby/general/enable_collapsing'))
                    $f->setCollapsed(Mage::getStoreConfig('amshopby/general/price_collapsed'));
            }
        }
        
        // 1.2.7 exclude some filters from the selected categories
        $filters = $this->excludeFilters($filters, $exclude);
        

        $this->_filterBlocks = $filters;
        return $filters;       
    }
    
    public function getStateHtml()
    {
        $this->getChild('layer_state')->setTemplate('amshopby/state.phtml');
        return $this->getChildHtml('layer_state');
    }   
    
    protected function excludeFilters($filters, $exclude)
    {
        $new = array();
        foreach ($filters as $f){
            $code = substr($f->getData('type'), 1+strrpos($f->getData('type'), '_'));
            if ($f->getAttributeModel()){
                $code = $f->getAttributeModel()->getAttributeCode();
            }
            
            if (in_array($code, $exclude)){
                 continue;
            } 
             
            $new[] = $f;          
        }
        return $new;
    }
    
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        
        //to make js and css work for 1.3 also
        $html = str_replace('narrow-by', 'narrow-by block-layered-nav', $html);
        
        $collapsing = intval(Mage::getStoreConfig('amshopby/general/enable_collapsing'));
        $html .= '<script>amshopby_start('.$collapsing.')</script>';
        
        // we don't want to move this into the template as there are different 
        // teplates for different themes
        foreach ($this->getFilters() as $f){
            if ($f->getCollapsed() && !$f->getHasSelection()){
                $name = $this->__($f->getName());
                $html = str_replace('<dt>'.$name, '<dt class="amshopby-collapsed">'.$name, $html);
            }
        }
        return $html;
    } 
    
    protected function _prepareLayout()
     {
        $head = $this->getLayout()->getBlock('head');
        if ($head){
            $head->addItem('skin_js', 'js/amshopby.js');     
        }
        
        return parent::_prepareLayout();
     }   
}