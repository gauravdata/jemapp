<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 15-12-2015
 * Time: 15:43
 */
require_once 'PostcodeNl/Api/controllers/JsonController.php';


class Twm_CustomRewrite_JsonController extends PostcodeNl_Api_JsonController {

    public function lookupAction()
    {
        parent::lookupAction();
        $postcode = $this->getRequest()->getParam('postcode');
        $houseNumber = $this->getRequest()->getParam('houseNumber');

        Mage::getSingleton('checkout/session')->setTmpPostcode($postcode);
        Mage::getSingleton('checkout/session')->setTmpHouseNumber($houseNumber);
    }

}