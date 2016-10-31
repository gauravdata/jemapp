<?php
class Dealer4dealer_Exactonline_Model_Observer
{
    public function runUpdate($observer)
    {
        try {
            $exactOnlineKoppeling = Mage::getModel('exactonline/main');
            $exactOnlineKoppeling->runUpdate();

        } catch (Exception $e) {
            $message = $e->getMessage();
            Mage::log('Error while starting D4D connector: '.$message);
        }

        return $this;
    }
}