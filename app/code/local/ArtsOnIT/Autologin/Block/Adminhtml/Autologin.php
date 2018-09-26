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
class ArtsOnIT_Autologin_Block_Adminhtml_Autologin extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_autologin';
    $this->_blockGroup = 'autologin';
    $this->_headerText = Mage::helper('autologin')->__('Customers Autologin');
     $this->_addButton('settings', array(
            'label'     => Mage::helper('autologin')->__('Settings'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('adminhtml/system_config/edit', array('section' => 'customer')) .'\')',
            'class'     => '',
        ));
    parent::__construct();
    $this->removeButton('add');
  }
}