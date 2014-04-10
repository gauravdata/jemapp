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
* Rewrite of Adminhtml Catalog Category Attributes per Group Tab block
* @category Nostress
* @package Nostress_Nscexport
*
*/

class Nostress_Nscexport_Block_Adminhtml_Catalog_Category_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Category_Tab_Attributes
{
    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Catalog_Category_Tab_Attributes
     */
    protected function _prepareForm()
    {
    	$group = $this->getGroup();
        $groupName = $group->getAttributeGroupName();
        $renderTaxonomies = Mage::helper('nscexport')->getGeneralConfig(Nostress_Nscexport_Helper_Data::PARAM_RENDER_TAXONOMIES,false,false);
        if(!$renderTaxonomies && $groupName == Nostress_Nscexport_Model_Entity_Attribute_Taxonomy::GROUP_NAME)
        {
        	$form = new Varien_Data_Form();
        	$form->setHtmlIdPrefix('group_' . $group->getId());
        	$fieldset = $form->addFieldset('fieldset_group_' . $group->getId(), array(
        			'legend'    => Mage::helper('catalog')->__($group->getAttributeGroupName()),
        			'class'     => 'fieldset-wide',
        	));

         	$fieldset->addField('info', 'label', array(
         			'name'  => 'info',
         			'value' => Mage::helper('nscexport')->__('Koongo Connector engine taxonomies rendering is disabled.')
         	));

         	$messageLink = Mage::helper('nscexport')->__('Enable the rendering in the module configuration.');

         	$configUrl = Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit/section/koongo_config');
        	$fieldset->addField('link_config', 'link', array(
        			'href' => $configUrl,        			
        			'title' => Mage::helper('nscexport')->__('Enable taxonomies rendering'),
        			'value' => $messageLink
        	));

        	$this->setForm($form);
        	return $this;
        }

        return parent::_prepareForm();
    }
}
