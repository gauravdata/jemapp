<?php
    class Biztech_Translator_Model_Config_Source_Cmspageattributes extends Varien_Data_Collection{    
        public function toOptionArray(){                                 
             $new_array = array(   
                array("label"=>"Page Title",
                    "value"=>"page_title"
                ),
                array("label"=>"Content Heading",
                        "value"=>"page_content_heading"
                    ),                
                 array("label"=>"Content",
                    "value"=>"page_content"
                ),                
                array("label"=>"Meta Data Keywords",
                    "value"=>"page_meta_keywords"
                ) ,

                array("label"=>"Meta Data Description",
                    "value"=>"page_meta_description"
                )
            );
            return $new_array;
        }
    }
