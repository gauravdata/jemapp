<?php
/**
 * Created by PhpStorm.
 * User: mathijs
 * Date: 11-3-16
 * Time: 14:39
 */ 
//class Twm_Email_Model_Core_Email_Template extends Mage_Core_Model_Email_Template
class Twm_Email_Model_Core_Email_Template extends Ebizmarts_Mandrill_Model_Email_Template
{
    public function sendTransactional($templateId, $sender, $email, $name, $vars=array(), $storeId=null)
    {
        if (isset($vars['pointstotal'])) {
            try
            {
                $points = $vars['pointstotal'];
                $websiteId = Mage::app()->getStore()->getWebsiteId();
                $money = Mage::getModel('points/rate')->load(2)
                    ->setCurrentWebsite(Mage::app()->getWebsite($websiteId))
                    ->loadByDirection(AW_Points_Model_Rate::POINTS_TO_CURRENCY)
                    ->exchange($points);

                $vars['pointsmoney'] = number_format($money, 2, ',', '.');
            }
            catch (\Exception $e)
            {
                $vars['pointsmoney'] = '0,00';
                Mage::logException($e);
            }

            $vars['pointsexpirationlink'] = Mage::getBaseUrl().'/points-countdown-timer?date='.$vars['pointsexpirationdate'];
            $vars['pointsexpirationimage'] = '<img src="'.Mage::getBaseUrl().'/points-countdown-timer?date='.$vars['pointsexpirationdate'].'">';
        }

        $this->setSentSuccess(false);
        if (($storeId === null) && $this->getDesignConfig()->getStore()) {
            $storeId = $this->getDesignConfig()->getStore();
        }

        if (strpos($templateId, ',') !==false){           // we have more templates, choose a random
            $templateId = $this->getRandomId($templateId);
        }

        if (is_numeric($templateId)) {
            $queue = $this->getQueue();
            $this->load($templateId);
            $this->setQueue($queue);
        } else {
            $localeCode = Mage::getStoreConfig('general/locale/code', $storeId);
            $this->loadDefault($templateId, $localeCode);
        }

        if (!$this->getId()) {
            throw Mage::exception('Mage_Core', Mage::helper('core')->__('Invalid transactional email code: %s', $templateId));
        }

        if (!is_array($sender)) {
            $this->setSenderName(Mage::getStoreConfig('trans_email/ident_' . $sender . '/name', $storeId));
            $this->setSenderEmail(Mage::getStoreConfig('trans_email/ident_' . $sender . '/email', $storeId));
        } else {
            $this->setSenderName($sender['name']);
            $this->setSenderEmail($sender['email']);
        }

        if (!isset($vars['store'])) {
            $vars['store'] = Mage::app()->getStore($storeId);
        }
        $this->setSentSuccess($this->send($email, $name, $vars));
        return $this;
    }

    private function getRandomId($multiId){

        $ids = explode(',',$multiId);
        $idx = rand(0, count($ids)-1);
        return $ids[$idx];
    }
}
