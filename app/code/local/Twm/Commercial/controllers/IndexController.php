<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 20-3-15
 * Time: 8:43
 */

class Twm_Commercial_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}