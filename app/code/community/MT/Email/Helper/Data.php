<?php

class MT_Email_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getDemoVars($storeId = 0)
    {

        return array(
            'customer' => $this->getDemoCustomer($storeId),
            'store' => Mage::app()->getStore($storeId),
            'order' => $this->getDemoOrder($storeId),
            'creditmemo' => $this->getDemoCreditMemo($storeId),
            'invoice' => $this->getDemoInvoice($storeId),
            'shipment' => $this->getDemoShipment($storeId),
        );
    }

    public function getDemoCustomer($storeId)
    {
        $customer = Mage::getModel("customer/customer")
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setEmail('jd1@ex.com')
            ->setPassword('soMepaSswOrd');

        return $customer;
    }

    /**
     * Returns template store id
     * Use only for demo data
     *
     * @return int
     */
    public function getCurrentStoreId ()
    {
        $storeId = 0;
        $template = Mage::registry('current_email_template');
        if ($template && $template->getId()) {
            $storeId = $template->getStoreId();
        }
        return $storeId;
    }

    /**
     * Returns order model for template rendering
     *
     * @param null $storeId
     * @return Mage_Core_Model_Abstract
     */
    public function getDemoOrder($storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->getCurrentStoreId();
        }
        $orderId = Mage::getStoreConfig('mtemail/preview/order_id', $storeId);
        $orderId = str_replace(array(' '), '', $orderId);
        if (strlen($orderId) > 5) {
            $order = Mage::getModel('sales/order')->getCollection()
                ->addFieldToFilter('increment_id', $orderId)
                ->getFirstItem();
        } else {
            $order = Mage::getModel('sales/order')->load($orderId);
        }

        return $order;
    }

    /**
     * Returns invoice model for template rendering
     *
     * @param null $storeId
     * @return Mage_Core_Model_Abstract
     */
    public function getDemoInvoice($storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->getCurrentStoreId();
        }

        $invoiceId = Mage::getStoreConfig('mtemail/preview/invoice_id', $storeId);
        $invoiceId = str_replace(array(' '), '', $invoiceId);
        if (strlen($invoiceId) > 5) {
            $invoice = Mage::getModel('sales/order_invoice')->getCollection()
                ->addFieldToFilter('increment_id', $invoiceId)
                ->getFirstItem();
        } else {
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
        }

        return $invoice;
    }

    /**
     * Returns shipment model for template rendering
     *
     * @param null $storeId
     * @return Mage_Core_Model_Abstract
     */
    public function getDemoShipment($storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->getCurrentStoreId();
        }

        $shipmentId = Mage::getStoreConfig('mtemail/preview/shipment_id', $storeId);
        $shipmentId = str_replace(array(' '), '', $shipmentId);
        if (strlen($shipmentId) > 5) {
            $shipment = Mage::getModel('sales/order_shipment')->getCollection()
                ->addFieldToFilter('increment_id', $shipmentId)
                ->getFirstItem();
        } else {
            $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
        }

        if (count($shipment->getAllTracks()) == 0) {
            $track = Mage::getModel('sales/order_shipment_track')
                ->setData(array(
                    'title' => 'DHL',
                    'number' => '2040RR89S1'
                ));
            $shipment->addTrack($track);
        }

        return $shipment;
    }

    /**
     * Returns creditmemo model for template rendering
     *
     * @param null $storeId
     * @return Mage_Core_Model_Abstract
     */
    public function getDemoCreditMemo($storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->getCurrentStoreId();
        }

        $creditMemoId = Mage::getStoreConfig('mtemail/preview/creditmemo_id', $storeId);
        $creditMemoId = str_replace(array(' '), '', $creditMemoId);
        if (strlen($creditMemoId) > 5) {
            $creditMemo = Mage::getModel('sales/order_creditmemo')->getCollection()
                ->addFieldToFilter('increment_id', $creditMemoId)
                ->getFirstItem();
        } else {
            $creditMemo = Mage::getModel('sales/order_creditmemo')->load($creditMemoId);
        }

        return $creditMemo;
    }

    public function isActive()
    {
        return Mage::getStoreConfig('mtemail/general/is_active') == 1;
    }

    public function isRTL()
    {
        return $this->getDirection() == 'rtl';
    }

    public function getDirection()
    {
        return Mage::getStoreConfig('mtemail/general/direction', Mage::app()->getStore()->getId());
    }

    public function log($message)
    {
        Mage::log($message, null, 'mt_email.log');
        return true;
    }

    /**
     * Get block list from template content
     *
     * @param Mage_Core_Model_Email_Template $template
     * @return array
     */
    public function parseBlockList($template)
    {
        $content = $template->getTemplateText();
        if (substr_count($content, '{{layout handle="') == 0) {
            return array();
        }

        $result = array();
        $blockList = explode('{{layout handle="', $content);
        foreach ($blockList as $block) {
            $blockTmp = explode('}}', $block);
            if (count($blockTmp) == 2) {
                $result[] = '{{layout handle="'.$blockTmp[0].'}}';
            }
        }

        return $result;
    }

    /**
     * get block data from string format
     *
     * @param $block
     * @return array
     */

    public function parseBlockData($block)
    {
        $blockTmp = str_replace(array('{{', '}}', 'layout', "'", '"'), '',$block);
        $blockTmp = explode(' ', $blockTmp);
        $result = array();
        foreach ($blockTmp as $attribute) {
            if (substr_count($attribute, 'block_name') == 1) {
                $result['block_name'] = str_replace(array('block_name', ' ', '='), '', $attribute);
            }

            if (substr_count($attribute, 'block_id') == 1) {
                $result['block_id'] = str_replace(array('block_id', ' ', '='), '', $attribute);
            }
        }

        return $result;
    }

    /**
     * Get Block Name List
     *
     * @param $blockList
     * @return array
     */
    public function getBlockNameList($blockList)
    {
        if (count($blockList) == 0) {
            return array();
        }

        $result = array();
        foreach ($blockList as $block) {
            $blockData = $this->parseBlockData($block);
            $result[] = $blockData['block_name'];
        }

        return $result;
    }

    /**
     * Returns message and link to wiki
     *
     * @param $type
     * @return string
     */
    public function getWikiLink($type)
    {
        switch($type) {
            case 'preview_settings':
                $msg =  '<br>More about configuration you can find here: <a target="_blank" href="http://wiki.magetrend.com/responsive-emails/configuration#TOC-Preview-settings">Go to Wiki</a>';
                break;
            default:
                $msg = 'Wiki Link is not available';
        }
        return $msg;
    }
}