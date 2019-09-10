<?php
/**
 * ArtsOnIT
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.pdf
 * It is also available through the world-wide-web at this URL:
 * http://www.mageext.com/respository/docs/License-SourceCode.pdf
 *
 * @category   ArtsOnIT
 * @package    ArtsOnIT_Autologin
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 ArtsonIT di Calore (http://www.mageext.com)
 * @license    http://www.mageext.com/respository/docs/License-SourceCode.pdf
 */
class ArtsOnIT_Autologin_Block_Adminhtml_Autologin_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
        parent::__construct();
        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
        $this->setIsWeb(true);
  }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
  protected function _prepareCollection()
  {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('autologin_hash')
            ->addAttributeToSelect('autologin_is_active')
            ->addAttributeToSelect('email')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left');
 
		      if (!$this->getIsWeb())
		      {
		      	 $collection->addAttributeToFilter('autologin_is_active', true);
		      }
	      
        $this->setCollection($collection);

        return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      if ($this->getIsWeb())
      {
	      $this->addColumn('entity_id', array(
	            'header'    => Mage::helper('customer')->__('ID'),
	            'width'     => '50px',
	            'index'     => 'entity_id',
	            'type'  => 'number',
	        ));
      }
      $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name'
        ));
      $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => '150',
            'index'     => 'email'
        ));
     
      if ($this->getIsWeb())
      {
 	     	   $this->addColumn('autologin_is_active', array(
	          'header'    => Mage::helper('autologin')->__('Enabled'),
	          'align'     => 'left',
	          'width'     => '80px',
	          'index'     => 'autologin_is_active',
	          'type'      => 'options',
	          'options'   => array(
	              0 => Mage::helper('autologin')->__('No'),
	              1 => Mage::helper('autologin')->__('Yes'),
	          ),
	      ));
      	 $this->addColumn('telephone', array(
            'header'    => Mage::helper('customer')->__('Telephone'),
            'width'     => '100',
            'index'     => 'billing_telephone'
         ));
      }
      else 
      {
  	     $this->addColumn('login', array(
            'header'    => Mage::helper('customer')->__('Login'),
            'width'     => '150',
            'index'     => 'autologin_hash'
        ));
      	
      }
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('autologin')->__('Action'),
                'width'     => '100',
                 'getter'    => 'getId',
            	'renderer'    => 'autologin/adminhtml_autologin_actions',
                 'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('autologin')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('autologin')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('customer');
 
       $this->getMassactionBlock()->addItem('generatehash', array(
             'label'    => Mage::helper('autologin')->__('Change Login Code'),
             'url'      => $this->getUrl('*/*/massChangehash'),
             'confirm'  => Mage::helper('autologin')->__('Are you sure?')
        ));
        $statues = array(
        	array('label'=> Mage::helper('autologin')->__('Enabled'),'value'=> 1),
        	array('label'=> Mage::helper('autologin')->__('Not Enabled'),'value'=> 0),
        );
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('autologin')->__('Change Status'),
             'url'  => $this->getUrl('*/*/massEnabled', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('autologin')->__('Status'),
                         'values'   => $statues
                     )
             )
        )); 
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('adminhtml/customer/edit', array('id' => $row->getId()));
  }

}