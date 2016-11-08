<?php

class MT_Email_Block_Adminhtml_System_Email_Template_Grid
    extends Mage_Adminhtml_Block_System_Email_Template_Grid
{


    protected function _prepareColumns()
    {

        $parent = parent::_prepareColumns();
        if (!Mage::helper('mtemail')->isActive()) {
            return $parent;
        }

        $helper = Mage::helper('adminhtml');
        $this->addColumnAfter('is_mtemail',
            array(
                'header'=>$helper->__('MTEmail'),
                'index'=>'is_mtemail',
                'filter' => 'adminhtml/widget_grid_column_filter_select',
                'renderer' => 'mtemail/adminhtml_system_email_template_grid_renderer_mtemail',
                'options' => array(1 => $helper->__('Yes'), 0 => $helper->__('No'))
            ), 'type');

        $this->addColumnAfter('store_id',
            array(
                'header'=>$helper->__('Store'),
                'index'=>'store_id',
                'filter' => 'adminhtml/widget_grid_column_filter_store',
                'renderer' => 'MT_Email_Block_Adminhtml_Widget_Grid_Column_Renderer_Store',
                'width' => '100px'
            ), 'is_mtemail');

        $this->sortColumnsByOrder();
        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::helper('mtemail')->isActive() && $row->getIsMtemail()) {
            return $this->getUrl('adminhtml/email_mteditor/index', array('id'=>$row->getId()));
        }

        return parent::getRowUrl($row);
    }


}