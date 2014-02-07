<?php
/**
 * Address Controller
 *
 * PHP Version 5.3
 *
 * @category  Payment
 * @package   Klarna_Module_Magento
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */

/**
 * Controller used to dispatch Klarna ajax actions
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_AddressController
    extends Mage_Core_Controller_Front_Action
{

    /**
     * Dispatch the Klarna action
     *
     * @return void
     */
    public function dispatchAction()
    {
        Mage::helper("klarnaPaymentModule/api")->loadConfig();
        $country = $this->getRequest()->getParam('country');

        $klarna = null;
        try {
            $klarna = KiTT::api(KiTT::locale($country));
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }

        $dispatcher = KiTT::ajaxDispatcher(
            new KiTT_Addresses($klarna),
            null
        );

        $dispatcher->dispatch();
    }

}
