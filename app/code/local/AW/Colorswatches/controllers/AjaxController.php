<?php

class AW_Colorswatches_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function productInfoAction()
    {
        $response = array(
            'success' => true,
            'data' => array()
        );
        $productId = $this->getRequest()->getParam('product_id', null);
        $productModel = Mage::getModel('catalog/product')->load($productId);
        if (null === $productModel->getId()) {
            $response['success'] = false;
            return $this->getResponse()->setBody(
                Zend_Json::encode($response)
            );
        }
        $partList = $this->getRequest()->getParam('parts', array());
        foreach ($partList as $partKey) {
            $response['data'][$partKey] = $this->_getProductPartHTML($partKey, $productModel);
        }
        return $this->getResponse()->setBody(
            Zend_Json::encode($response)
        );
    }

    /**
     * @param string $key
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    protected function _getProductPartHTML($key, $product)
    {
        if (!method_exists("AW_Colorswatches_Helper_ProductInfoGetter", $key)) {
            return "";
        }
        return call_user_func(array("AW_Colorswatches_Helper_ProductInfoGetter", $key), $product);
    }
}