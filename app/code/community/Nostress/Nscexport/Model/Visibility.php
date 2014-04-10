<?php
class Nostress_Nscexport_Model_Visibility extends Mage_Catalog_Model_Product_Visibility {
    
    public static function getAllOptions( $withFirst = true) {
        
        $res = array();
        if( $withFirst) {
            $res[] = array('value'=>'', 'label'=> Mage::helper('catalog')->__('-- Please Select --'));
        }
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
                    'value' => $index,
                    'label' => $value
            );
        }
        return $res;
    }
}