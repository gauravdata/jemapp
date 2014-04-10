<?php
class Nostress_Nscexport_Model_Data_Form_Element_Checkboxes extends Varien_Data_Form_Element_Checkboxes {
    
    /**
     * Retrieve HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $values = $this->_prepareValues();
    
        if (!$values) {
            return '';
        }
        
        $this->setOnclick( 'selectCheckbox( this)');
    
        $html  = '<ul id="'.$this->getId().'" class="checkboxes layouted '.$this->getClass().'">';
        foreach ($values as $value) {
            $html.= $this->_optionToHtml($value);
        }
        $html .= '</ul>';
        $html .= "<script type='text/javascript'>
                    
                    function selectCheckbox( node) {
                        if(node.checked) {
                            node.parentNode.setAttribute('class', 'selected');
                        } else {
                            node.parentNode.setAttribute('class', '');
                        }
                    }
                
                 </script>"
                . $this->getAfterElementHtml();
    
        return $html;
    }
    
    protected function _optionToHtml($option)
    {
        $id = $this->getHtmlId().'_'.$this->_escape($option['value']);
        $valuesSelected = $this->getValue();
        if(!is_array($valuesSelected))
            $valuesSelected = array($valuesSelected);
        $class = in_array( $option['value'], $valuesSelected) ? "class='selected'" : "";
    
        $html = '<li '.$class.'><input id="'.$id.'"';
        foreach ($this->getHtmlAttributes() as $attribute) {
            if ($value = $this->getDataUsingMethod($attribute, $option['value'])) {
                $html .= ' '.$attribute.'="'.$value.'"';
            }
        }
        $html .= ' value="'.$option['value'].'" />'
                . ' <label for="'.$id.'">' . $option['label'] . '</label></li>'
                        . "\n";
        return $html;
    }
}