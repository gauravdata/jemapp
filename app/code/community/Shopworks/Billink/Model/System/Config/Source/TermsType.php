<?php

/**
 * Class Shopworks_Billink_Model_System_Config_Source_TermsType
 * Used to show a list of agreements that can be used for the Billink module
 */
class Shopworks_Billink_Model_System_Config_Source_TermsType
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array(
                'value'=>0,
                'label'=>'- Geen aparte voorwaarden voor Billink -'
            )
        );

        /** @var Mage_Checkout_Model_Agreement $agreementModel */
        $agreementModel = Mage::getModel('checkout/agreement');
        $agreements = $agreementModel->getCollection();

        foreach($agreements as $agreement)
        {
            /** @var Mage_Checkout_Model_Agreement $agreement */
            $options[] = array(
                'value' => $agreement->getId(),
                'label' => $agreement->getData('name')
            );
        }

        return $options;
    }
}