<?php

require_once "Mage/Checkout/controllers/OnepageController.php";

class Uni_Autoregister_Checkout_OnepageController extends Mage_Checkout_OnepageController {

    public function saveMethodAction() {
//        if ($this->_expireAjax()) {
//            return;
//        }
//        $_autoRegHelper = Mage::helper('autoregister');
//        if ($this->getRequest()->isPost()) {
//            if ($_autoRegHelper->isAutoRegistrationEnabled()):
//                if ($this->getRequest()->getPost('method') === 'guest'):
//                    Mage::getSingleton('checkout/session')->setIsGuest('TRUE');
////                    $method = 'register';
//                endif;
//            endif;
//                $method = $this->getRequest()->getPost('method');
//            
//            $result = $this->getOnepage()->saveCheckoutMethod($method);
//            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
//        }
    }

}
