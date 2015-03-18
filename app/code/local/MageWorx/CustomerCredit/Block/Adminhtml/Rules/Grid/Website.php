<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2010 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */
 
/**
 * Customer Credit extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */
 
class MageWorx_CustomerCredit_Block_Adminhtml_Rules_Grid_Website extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $websiteHash = Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash();
        $data = $row->getData($this->getColumn()->getIndex());
        $rowWebsites = explode(',', $data);
        $html = array();
        foreach ($rowWebsites as $websiteId) {
            if(isset($websiteHash[$websiteId])) {
                $html[] = $websiteHash[$websiteId];
            }
        }
        if(count($html)) {
            return join(", ",$html);
        }
        
        return parent::render($row);
    }
   
}