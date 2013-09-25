<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Magavenue
 * @package     Magavenue_Shopializable
 * @copyright   Copyright (c) 2010 Magavenue (http://www.magavenue.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Magavenue_Shopializable_TemplateController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
	
		$template = $this->_request->getParam('template');
		$file = $this->_request->getParam('file');

                $filename = dirname(__FILE__).'/../../../../../../skin/shopializable/template/'.$template.'/css.tpl';

		$handle = fopen($filename, "r");
		echo $contents = fread($handle, filesize($filename));
		fclose($handle);
                
                echo '####CSS-FILE####';
                
                $filename = dirname(__FILE__).'/../../../../../../skin/shopializable/template/'.$template.'/header.tpl';
		
		$handle = fopen($filename, "r");
		echo $contents = fread($handle, filesize($filename));
		fclose($handle);

                echo '####HEADER-FILE####';
                
                $filename = dirname(__FILE__).'/../../../../../../skin/shopializable/template/'.$template.'/footer.tpl';
		
		$handle = fopen($filename, "r");
		echo $contents = fread($handle, filesize($filename));
		fclose($handle);

                echo '####FOOTER-FILE####';
                
                $filename = dirname(__FILE__).'/../../../../../../skin/shopializable/template/'.$template.'/'.$file;
		
		$handle = fopen($filename, "r");
		echo $contents = fread($handle, filesize($filename));
		fclose($handle);                
        }
	
}