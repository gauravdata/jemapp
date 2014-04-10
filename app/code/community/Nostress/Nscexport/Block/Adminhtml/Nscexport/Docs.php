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
*
*/

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Docs extends Mage_Adminhtml_Block_Widget_Container
{
    
  public function __construct()
  {
    $this->_headerText = Mage::helper('nscexport')->__('Koongo university - Watch Koongo explaning videos!');
    
    $this->_addButton('documentation', array(
        'label'     => Mage::helper('nscexport')->__('Are you experiencing some issues? Check out Koongo Documentation!'),
        'onclick'   => "setLocation('https://docs.koongo.com/display/KoongoConnector/Koongo+Connector');",
        'class'     => 'reload',
    ), -100);
    
    $this->_addButton('support', array(
        'label'     => Mage::helper('nscexport')->__('Support Desk'),
        'onclick'   => "setLocation('https://store.koongo.com/support-and-contact.html');",
        'class'     => 'reload',
    ), -100);
    
    $this->_addButton('livechat', $this->helper('nscexport')->getLivechatButtonOptions(), 100);
  }
  
  public function getVideos() {
      
      $videos = Mage::helper( 'nscexport/data_client')->getUniversityInfo();
      if( isset( $videos['videos'])) {
          return $videos['videos'];
      } else {
          return false;
      }
  }
}