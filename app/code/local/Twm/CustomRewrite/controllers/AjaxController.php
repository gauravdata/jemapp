<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 11-12-2015
 * Time: 11:34
 */
require_once 'Idev/OneStepCheckout/controllers/AjaxController.php';


class Twm_CustomRewrite_AjaxController extends Idev_OneStepCheckout_AjaxController {

    public function check_emailAction()
    {
        $validator = new Zend_Validate_EmailAddress();
        $validator->setOptions(array('domain' => false));

        $email = $this->getRequest()->getPost('email', false);

        $data = array('result'=>'invalid');

        if($email && $email != '')  {
// sometimes not working
//            if(!$validator->isValid($email))    {
//
//            }
//            else    {

                // Valid email, check for existance
                if($this->_isEmailRegistered($email))   {
                    $data['result'] = 'exists';
                }
                else    {
                    $data['result'] = 'clean';
                }
//            }
        }

        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
}