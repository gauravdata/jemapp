<?php

class AW_Colorswatches_Block_Adminhtml_Catalog_Product_Attribute_Edit_Tab_Colorswatch
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_swatchAttributeModel = null;

    /**
     * @return AW_Colorswatches_Model_Swatchattribute
     */
    public function getCurrentSwatchAttribute()
    {
        if (null === $this->_swatchAttributeModel) {
            $attribute = Mage::registry('entity_attribute');
            $this->_swatchAttributeModel = Mage::getModel('awcolorswatches/swatchattribute')->loadByAttributeId(
                $attribute->getId()
            );
        }
        return $this->_swatchAttributeModel;
    }

    protected function _prepareForm()
    {
        /* @var $form Varien_Data_Form */
        $form = new Varien_Data_Form();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->addFieldset(
            'base_fieldset', array('legend' => Mage::helper('awcolorswatches')->__('Swatch Settings'))
        );
        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
        $fieldset->addField(
            'aw_csw_is_enabled', 'select',
            array(
                'name'   => 'aw_csw[is_enabled]',
                'label'  => Mage::helper('awcolorswatches')->__('Enable swatcher for current attribute'),
                'title'  => Mage::helper('awcolorswatches')->__('Enable swatcher for current attribute'),
                'values' => $yesnoSource,
            )
        );
        $fieldset->addField(
            'aw_csw_is_display_popup', 'select',
            array(
                'name'   => 'aw_csw[is_display_popup]',
                'label'  => Mage::helper('awcolorswatches')->__('Display images in a pop-up on mouse hovering'),
                'title'  => Mage::helper('awcolorswatches')->__('Display images in a pop-up on mouse hovering'),
                'values' => $yesnoSource,
            )
        );
        $fieldset->addField(
            'aw_csw_is_override_with_child', 'select',
            array(
                'name'   => 'aw_csw[is_override_with_child]',
                'label'  => Mage::helper('awcolorswatches')->__('Override swatch with child product images'),
                'title'  => Mage::helper('awcolorswatches')->__('Override swatch with child product images'),
                'note'   => Mage::helper('awcolorswatches')->__('Works only when product has single attribute'),
                'values' => $yesnoSource,
                'value'  => 1, //YES by default
            )
        );

        $contentBlock = $this->getLayout()
            ->createBlock('awcolorswatches/adminhtml_catalog_product_attribute_edit_tab_colorswatch_images')
        ;
        $form->addFieldset(
            'images_fieldset', array(
                'legend' => Mage::helper('awcolorswatches')->__('Images'),
                'html_content' => $contentBlock->toHtml(),
            )
        );
        $data = array();
        foreach ($this->getCurrentSwatchAttribute()->getData() as $key => $value) {
            $data['aw_csw_' . $key] = $value;
        }
        $form->addValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('awcolorswatches')->__('Images for Attribute');
    }

    public function getTabTitle()
    {
        return Mage::helper('awcolorswatches')->__('Images for Attribute');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getAfter()
    {
        return 'labels';
    }
}