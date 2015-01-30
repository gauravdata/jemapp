<?php

class Twm_ServicepointDHL_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $model = Mage::getModel('servicepointdhl/carrier_shippingMethod');

        $request = $this->getRequest();

        $carriers = $model->getDHLAddresses(
            $request->getParam('servicepointdhl_postcode'),
            $request->getParam('servicepointdhl_city')
        );

        $result = array();
        foreach ($carriers as $carrier) {
            $result[$carrier['spid']] = array(
                'Id' => $carrier['spid'],
                'Name' => $carrier['name'],
                'Street' => $carrier['add'],
                'Postcode' => $carrier['zip'],
                'City' => $carrier['city'],
                'Country' => $carrier['country']
            );
        }

        echo json_encode($result);
    }

}
