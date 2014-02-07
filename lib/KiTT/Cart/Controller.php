<?php
/**
 * Cart Controller
 *
 * PHP Version 5.3
 *
 * @category  Payment
 * @package   KiTT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */

/**
 * KiTT_Cart_Controller
 *
 * @category  Payment
 * @package   KiTT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */
class KiTT_Cart_Controller extends KiTT_Installment_Controller_Abstract
{

    /**
     * Create a pclass collection
     *
     * @return KiTT_PClassCollection
     */
    protected function createPClassCollection()
    {
        return new KiTT_PClassCollection(
            $this->api, $this->formatter, $this->sum,
            KlarnaFlags::CHECKOUT_PAGE, KiTT::PART
        );
    }

}
