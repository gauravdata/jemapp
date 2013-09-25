<?php
/**
* @copyright Amasty.
*/  
class Amasty_Shopby_Block_Adminhtml_Filter_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        //create form structure
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $hlp = Mage::helper('amshopby');
        
        $yesno = array(
            array(
                'value' => 0,
                'label' => Mage::helper('catalog')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('catalog')->__('Yes')
        ));
        
        $fldSet = $form->addFieldset('amshopby_general', array('legend'=> $hlp->__('Filter Properties')));
        
        $fldSet->addField('display_type', 'select', array(
          'label'     => $hlp->__('Display Type'),
          'name'      => 'display_type',
          'values'    => array(
            array(
                'value' => 0,
                'label' => $hlp->__('Labels Only')
            ),
            array(
                'value' => 1,
                'label' => $hlp->__('Images Only')
            ),
            array(
                'value' => 2,
                'label' => $hlp->__('Images and Labels')
            ),
            array(
                'value' => 3,
                'label' => $hlp->__('Drop-down List')
            ),
            array(
                'value' => 4,
                'label' => $hlp->__('Labels in 2 columns')
            )),
        ));

        $fldSet->addField('max_options', 'text', array(
          'label'     => $hlp->__('Number of unfolded options'),
          'name'      => 'max_options',
          'note'      => $hlp->__('Applicable for `Labels Only`, `Images only` and `Labels and Images` display types. Zero means all options are unfolded')
        ));

        
        $fldSet->addField('hide_counts', 'select', array(
          'label'     => $hlp->__('Hide quantities'),
          'name'      => 'hide_counts',
          'values'    => $yesno
        ));

        $fldSet->addField('sort_by', 'select', array(
          'label'     => $hlp->__('Sort Options By'),
          'name'      => 'sort_by',
          'values'    => array(
            array(
                'value' => 0,
                'label' => $hlp->__('Position')
            ),
            array(
                'value' => 1,
                'label' => $hlp->__('Name')
            ),
            array(
                'value' => 2,
                'label' => $hlp->__('Product Quatities')
            )), 
        ));
        
        $fldSet->addField('show_on_list', 'select', array(
          'label'     => $hlp->__('Show on List'),
          'name'      => 'show_on_list',
          'values'    => $yesno,
          'note'      => $hlp->__('Show option description and image above product listing'),
        ));
        
//        $fldSet->addField('show_icons_on_list', 'select', array(
//          'label'     => $hlp->__('Show Icons on List'),
//          'name'      => 'show_icons_on_list',
//          'values'    => $yesno,
//          'note'      => $hlp->__('Show option`s images for each product on the list view'),
//        ));
        
        $fldSet->addField('show_on_view', 'select', array(
          'label'     => $hlp->__('Show on Product'),
          'name'      => 'show_on_view',
          'values'    => $yesno,
          'note'      => $hlp->__('Show options images block on product view page'),
        ));                
        
        $fldSet->addField('exclude_from', 'text', array(
          'label'     => $hlp->__('Exclude From Categories'),
          'name'      => 'exclude_from',
          'note'      => $hlp->__('Comma separated list of the categories IDs like 17,4,25'),
        ));
        
        $fldSet->addField('collapsed', 'select', array(
          'label'     => $hlp->__('Collapsed'),
          'name'      => 'collapsed',
          'values'    => $yesno,
          'note'      => $hlp->__('Will be collapsed until customer select any filter option'),
        ));
        
        $fldSet->addField('single_choice', 'select', array(
          'label'     => $hlp->__('Single Choice Only'),
          'name'      => 'single_choice',
          'values'    => $yesno,
          'note'      => $hlp->__('Disables multiple selection'),
        ));
        
        
        //set form values
        $data = Mage::getSingleton('adminhtml/session')->getFormData();
        $model = Mage::registry('amshopby_filter');
        if ($data) {
            $form->setValues($data);
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        }
        elseif ($model) {
            $form->setValues($model->getData());
        }
        
        return parent::_prepareForm();
    }
}