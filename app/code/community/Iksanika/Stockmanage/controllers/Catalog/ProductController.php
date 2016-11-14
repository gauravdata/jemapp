<?php

/**
 * Iksanika llc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.iksanika.com/products/IKS-LICENSE.txt
 *
 * @category   Iksanika
 * @package    Iksanika_Stockmanage
 * @copyright  Copyright (c) 2013 Iksanika llc. (http://www.iksanika.com)
 * @license    http://www.iksanika.com/products/IKS-LICENSE.txt
 */

include_once "Mage/Adminhtml/controllers/Catalog/ProductController.php";

class Iksanika_Stockmanage_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController 
{ 
    protected function _construct() 
    { 
        $this->setUsedModuleName('Iksanika_Stockmanage'); 
    } 
    
    public function indexAction() 
    { 
        $this->loadLayout(); 
        $this->_setActiveMenu('catalog/stockmanage'); 
        $this->_addContent($this->getLayout()->createBlock('stockmanage/catalog_product')); 
        $this->renderLayout(); 
    } 
    
    public function gridAction() 
    { 
        $this->loadLayout(); 
        $this->getResponse()->setBody($this->getLayout()->createBlock('stockmanage/catalog_product_grid')->toHtml()); 
    } 
    
    protected function _isAllowed() 
    { 
        return Mage::getSingleton('admin/session')->isAllowed('catalog/products'); 
    } 
    
    public function massUpdateProductsAction() 
    { 
        $productIds = $this->getRequest()->getParam('product'); 
        if (is_array($productIds)) 
        { 
            try { 
                foreach ($productIds as $itemId => $productId) 
                { 
                    $product = Mage::getModel('catalog/product')->load($productId); 
                    $stockData = $product->getStockItem(); 
                    $columnValuesForUpdate = $this->getRequest()->getParam('is_in_stock'); 
                    $stockData->setData('is_in_stock', $columnValuesForUpdate[$itemId]); 
                    $columnValuesForUpdate = $this->getRequest()->getParam('qty'); 
                    $stockData->setData('qty', $columnValuesForUpdate[$itemId]); 
                    $stockData->save(); 
                } 
                $this->_getSession()->addSuccess( $this->__('Total of %d record(s) were successfully refreshed.', count($productIds)) ); 
            } 
            catch (Exception $e) 
            { 
                $this->_getSession()->addError($e->getMessage()); 
            } 
        }else 
        { 
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.')); 
        } 
        $this->_redirect('*/*/index'); 
    } 
}