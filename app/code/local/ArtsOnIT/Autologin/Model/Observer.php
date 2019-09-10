<?php
/**
 * ArtsOnIT
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.pdf
 * It is also available through the world-wide-web at this URL:
 * http://www.mageext.com/respository/docs/License-SourceCode.pdf
 *
 * @category   ArtsOnIT
 * @package    ArtsOnIT_Autologin
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 ArtsonIT di Calore (http://www.mageext.com)
 * @license    http://www.mageext.com/respository/docs/License-SourceCode.pdf
 */
class ArtsOnIT_Autologin_Model_Observer extends Varien_Object {

    public function updateMailChip($observer)
    {
        try
        {
            $customer = $observer->getCustomer();
            if ($customer->getId() > 0)
            {
                if  (!$customer->hasData ( 'autologin_hash' ))
                {
                	if (is_bool($customer->getId())) {
		                $customer = Mage::getModel( 'cutomer/customer' )->load( $customer->getId() );
	                }
                }
                $al = $customer->getData ( 'autologin_hash' );
                
                if ($al)
                {
                    
                    $observer->getNewvars()->setData(Mage::getStoreConfig('customer/autologin/mailchimp_var'), $al);
                }
            }   
    
        }
        catch(Excaption $e)
        {
            
            Mage::Log( $e);
        }
        return $this;
       
        
    }
	public function saveCustomer( $observer)
	{
		try 
		{
			$customer = $observer->getCustomer();
			if (($customer instanceof Mage_Customer_Model_Customer)) {
				Mage::helper('autologin')->generateAutologin($customer);
			}
		}
		catch(Excaption $e)
		{
			Mage::Log($e);
		}
        return $this;
	}
	public function doAutologin(Varien_Event_Observer $observer) {
		if (! Mage::getStoreConfig ( 'customer/autologin/enabled' )) {
 
			return;
		}
		
		if (! Mage::helper ( 'customer' )->isLoggedIn ()) {
			
			$request = Mage::app ()->getFrontController ()->getRequest ();
			$hashes = array ();
			$paramscount = count ( $request->getParams () );
			if ($paramscount == 0) {
				$path = trim ( $request->getPathInfo (), '/' );
				$p = explode ( '/', $path );
				if (count ($p) < 3) {
					$hashes = $p;
					$withParam = false;
				} else {
					return $this;
				}
			}
  
			if ($paramscount > 0) {
				$al = Mage::getStoreConfig ( 'customer/autologin/urlparam' );
				$withParam = false;
				
				if ($hash = $request->getParam ( $al )) {
					if ($hash == '') {
						return $this;
					}
					$hashes = $hash;
					$withParam = true;
				} else {
					foreach ( $request->getParams () as $k => $y ) {
						if ($y == '') {
							$hashes [] = $k;
						}
					}
				}
			}
			if (count ( $hashes ) == 0) {
				return $this;
			}
			if (Mage::helper ( 'autologin' )->tryLogin ( $hashes )) {
				
				$action = $observer->getControllerAction ();
				$pathInfo = $request->getPathInfo ();
				$hash = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getData ( 'autologin_hash' );
				$rpl = array ();
				$rpl [] = $hash;
				
				if ($withParam) {
					
					$rpl [] = $al . '='; 
				}
				$pathInfo = str_replace ( '//', '/', str_replace ( $rpl, "", $pathInfo ) );
				$response = Mage::app ()->getFrontController ()->getResponse ();
				$response->setRedirect ( Mage::getBaseUrl () . $pathInfo, 301 );
				$response->sendHeaders();
				exit;
			} 
		}
	
	}

}
