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

class ArtsOnIT_Autologin_Block_Autologin extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getAutologin()     
     { 
        if (!$this->hasData('autologin')) {
            $this->setData('autologin', Mage::registry('autologin'));
        }
        return $this->getData('autologin');
        
    }
}