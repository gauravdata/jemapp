<?php
/** 
* Magento Module developed by NoStress Commerce 
* 
* NOTICE OF LICENSE 
* 
* This source file is subject to the Open Software License (OSL 3.0) 
* that is bundled with this package in the file LICENSE.txt. 
* It is also available through the world-wide-web at this URL: 
* http://opensource.org/licenses/osl-3.0.php 
* If you did of the license and are unable to 
* obtain it through the world-wide-web, please send an email 
* to info@nostresscommerce.cz so we can send you a copy immediately. 
* 
* @copyright Copyright (c) 2009 NoStress Commerce (http://www.nostresscommerce.cz) 
* 
*/ 

/** 
* @category Nostress 
* @package Nostress_Nscexport
*/

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'nscexport';
        $this->_controller = 'adminhtml_nscexport';
        
        $this->_updateButton('save', 'label', Mage::helper('nscexport')->__('Save'));
        $this->_updateButton('save', 'onclick','updateOutputAndSave()');
        $this->_updateButton('delete', 'label', Mage::helper('nscexport')->__('Delete'));        
        $this->removeButton('reset');
        /*$this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('nscexport')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "            
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }                                    
            
        ";*/
        
        //update for case of creaarting new profile
    	if( !Mage::registry('nscexport_data') || !Mage::registry('nscexport_data')->getId() )
        {
        	$this->_updateButton('back',null, array(
           		'label'     => Mage::helper('adminhtml')->__('Back'),
        		'onclick'   => "setLocation('".$this->getUrl("/*/new")."');",
            	'class'     => 'back',
        	), -1);
        }
    }

    public function getHeaderText()
    {
        if( Mage::registry('nscexport_data') && Mage::registry('nscexport_data')->getId() ) {
            return Mage::helper('nscexport')->__('XML Feed Export')." - ".Mage::helper('nscexport')->__('Edit Profile')." '".$this->htmlEscape(Mage::registry('nscexport_data')->getName())."'";
        } else {
            return Mage::helper('nscexport')->__('XML Feed Export')." - ".Mage::helper('nscexport')->__('New Profile').' ('.Mage::helper('nscexport')->__('Step').' 2)';
        }
    }
}
