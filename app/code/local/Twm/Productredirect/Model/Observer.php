<?php

class Twm_Productredirect_Model_Observer
{
	public function observe($observer)
    {
        $request = $observer['controller_action']->getRequest();
        $actionName = $request->getActionName();

        if ($actionName == 'noRoute') {
            /* @var $request Mage_Core_Controller_Request_Http */

            $id = $request->getParam('id');
            $categoryId = $request->getParam('category');
            $s = $request->getParam('s');

            if($categoryId){
                //id found in url, use it tor edirect to the category itself
                $destinationUrl = $this->getCategoryUrl($categoryId);

                $response = Mage::app()->getResponse();
                $response->setRedirect(Mage::getBaseUrl() . $destinationUrl, 301);
                $response->sendResponse();
                exit;
            } else {
                // no id found. remove everything after the last slash in the url
                // and try if that's an existing route

                $urlSegments = explode('/', $request->getRequestString());

                foreach($urlSegments as $k => $urlSegment){
                    if(empty($urlSegment)){
                        unset($urlSegments[$k]);
                    }
                }

                /* @var $flashMessages Mage_Core_Model_Message_Collection */
                $flashMessages = Mage::getSingleton('core/session')->getMessages()->clear(); // hack to avoid duplicate flash messages


                if(count($urlSegments) >= 1){
                    $message = "De pagina die u heeft opgevraagd is niet gevonden. In plaats daarvan bent u naar de bovenliggende pagina gestuurd.";

                    Mage::getSingleton('core/session')->addNotice($message);

                    if (count($urlSegments) > 1) {
                        $urlSegments[count($urlSegments)-1] .= '.html';
                    }
                    
                    array_pop($urlSegments);
                    $destinationUrl = implode('/', $urlSegments);
                    //$response = Mage::app()->getResponse();
                    //$response->setRedirect(Mage::getBaseUrl() . $destinationUrl, 301);
                    //$response->sendResponse();
                    //exit;
                }

            }


        }
	}

    public function getCategoryUrl($entityId) {
        $category = new Mage_Catalog_Model_Category();
        $category->load($entityId);

        return $category->getUrlPath();
    }

}
