<?php
    class Biztech_Translator_Model_Config_Source_Categoryattributes extends Varien_Data_Collection{    
        public function toOptionArray(){                                 
            $new_array = array(
                 array("label"=>"Name",
                    "value"=>"group_4name"),
                array("label"=>"Description",
                    "value"=>"group_4description"),
                array("label"=>"Meta Keywords",
                    "value"=>"group_4meta_keywords"
                ) ,
                array("label"=>"Meta Description",
                    "value"=>"group_4meta_description"
                )            
            );
            return $new_array;
        }
    }
