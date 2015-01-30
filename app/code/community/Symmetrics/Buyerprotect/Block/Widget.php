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
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2010-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_buyerprotection/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */

/**
 * Block class for TS widget/badge rendering.
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2010-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_buyerprotection/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */
class Symmetrics_Buyerprotect_Block_Widget extends Mage_Core_Block_Template
{
    /**
     * Some scheme-less URIs:
     * 
     *  - Resource to badge image
     *  - URI path to external JS which creates the widget
     */
    const TRUSTEDSHOPS_URI = 'www.trustedshops.com',
          WIDGET_URI_NOSCRIPT_BADGE = 'widgets.trustedshops.com/images/badge.png',
          WIDGET_URI_PATH_JS = 'widgets.trustedshops.com/js/';
    
    /**
     * Shop config for showing widget or not.
     */
    const XML_PATH_SHOW_WIDGET = 'buyerprotection/data/trustedshops_certificate_logo_active';
    
    /**
     * Helper instance.
     *
     * @var Symmetrics_Buyerprotect_Helper_Data
     */
    protected $_helper;
    
    /**
     * Getter for default module helper.
     * 
     * @return Symmetrics_Buyerprotect_Helper_Data
     */
    protected function _getHelper()
    {
        if (is_null($this->_helper)) {
            $this->_helper = Mage::helper('buyerprotect');
        }
        
        return $this->_helper;
    }
    
    /**
     * Link to TS certificate page.
     * 
     * @return string
     */
    public function getCertLink()
    {
        return self::TRUSTEDSHOPS_URI . '/shop/certificate.php?shop_id=' . $this->getTsId();
    }
    
    /**
     * TS shop ID.
     * 
     * @return string
     */
    public function getTsId()
    {
        return $this->_getHelper()->getTsUserId();
    }
    
    /**
     * URI to external TS ID dependent JS which renders the widget.
     * 
     * @return string
     */
    public function getWidgetJsUri()
    {
        return self::WIDGET_URI_PATH_JS . $this->getTsId() . '.js';
    }
    
    /**
     * URI to noscript image/badge.
     * 
     * @return string
     */
    public function getWidgetNoscriptBadgeUri()
    {
        return self::WIDGET_URI_NOSCRIPT_BADGE;
    }
    
    /**
     * Render widget or not.
     * 
     * @return bool
     */
    public function isActive()
    {
        return $this->_getHelper()->isBuyerprotectActive() &&
            Mage::getStoreConfigFlag(self::XML_PATH_SHOW_WIDGET);
    }
}
