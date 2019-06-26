<?php

class Biztech_Translator_Block_Adminhtml_Translator_Cron_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('translator_cron_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(false);
    }

    public function getGridUrl()
    {

    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('translator/cron')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => Mage::helper('translator')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
        ));

        /*$this->addColumn('cron_date', array(
            'header' => Mage::helper('translator')->__('Date Of Cron'),
            'type' => 'date',
            'format' => 'd-M-Y',
            'align' => 'left',
            'width' => '50px',
            'index' => 'cron_date',
        ));*/

        /*$this->addColumn('cron_name', array(
            'header' => Mage::helper('translator')->__('Cron Name'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'cron_name',
        ));*/

        /*$this->addColumn('product_ids', array(
            'header' => Mage::helper('translator')->__('Product Ids'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'product_ids',
        ));*/

        $this->addColumn('lang_from', array(
            'header' => Mage::helper('translator')->__('Language From'),
            'align' => 'left',
            'width' => '50px',
            'index' => 'lang_from',
            'type' => 'text',
            'renderer' => 'Biztech_Translator_Block_Adminhtml_Translator_Cron_Renderer_Langfrom',
        ));

        $this->addColumn('lang_to', array(
            'header' => Mage::helper('translator')->__('Language To'),
            'align' => 'left',
            'width' => '50px',
            'index' => 'lang_to',
            'renderer' => 'Biztech_Translator_Block_Adminhtml_Translator_Cron_Renderer_Langto',
        ));

        $this->addColumn('store_id', array(
            'header' => Mage::helper('translator')->__('Store'),
            'align' => 'left',
            'width' => '50px',
            'index' => 'store_id',
            'renderer' => 'Biztech_Translator_Block_Adminhtml_Translator_Cron_Renderer_Storename',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('translator')->__('Status'),
            'align' => 'left',
            'width' => '50px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getModel('translator/config_source_status')->toOptionArray(),
        ));

        parent::_prepareColumns();
    }

    // protected function _prepareMassaction()
    // {
    //     $this->setMassactionIdField('id');
    //     $this->getMassactionBlock()->setFormFieldName('translator');

    //     $this->getMassactionBlock()->addItem('delete', array(
    //         'label' => Mage::helper('translator')->__('Delete'),
    //         'url' => $this->getUrl('*/massCronDelete', array('store' => $this->getRequest()->getParam('store', 0))),
    //         'confirm' => Mage::helper('translator')->__('Are you sure?')
    //     ));

    //     return $this;
    // }
}