<?php
/** 
 * Magento Module developed by NoStress Commerce 
 * 
 * NOTICE OF LICENSE 
 * 
 * This source file is subject to the Open Software License (OSL 3.0) 
 * that is bundled with this package in the file LICENSE.txt. 
 * It is also available through the world-wide-web at this URL: 
 * http://opensource.org/licenses/osl-3.0.php 
 * If you did of the license and are unable to 
 * obtain it through the world-wide-web, please send an email 
 * to info@nostresscommerce.cz so we can send you a copy immediately. 
 * 
 * @copyright Copyright (c) 2009 NoStress Commerce (http://www.nostresscommerce.cz) 
 * 
 */

/** 
 * Observer for Export
 * 
 * @category Nostress 
 * @package Nostress_Nscexport
 * 
 */

class Nostress_Nscexport_Model_Observer 
{
	protected $_rulePrices = array();

	/**
     * Handles a custom AfterSave event for Catalog Products
     * Determines if the product is new.
     * Calls function for adding product to proper profile.
     *
     * @param array $eventArgs
     */
    public function processCatalogProductAfterSaveEvent($eventArgs)
    {
		//Pull the product out of the EventArgs parameter
        $product = $eventArgs['data_object'];
        if (Mage::helper('nscexport')->getNscGeneralStoreConfig(Nostress_Nscexport_Helper_Data::XML_PATH_NSC_GENERAL_ADD_PRODUCTS) == 1 && $product->isObjectNew()) 
        {
         	Mage::helper('nscexport')->addProductToExportProfiles($product);
        }
   	}
    
	/**!!!!!!!!!!FUNCTION IS NO MORE USED!!!!!!!!!!!!!
     * Apply catalog price rules to product in admin
     *
     * @return  Mage_CatalogRule_Model_Observer
     */
    public function processNscexportFinalPrice($observer)
    {
        $product = $observer->getEvent()->getProduct();
		$model = $observer->getEvent()->getModel();
		$store = $model->getStore();
        $custmerGroup = Mage::getModel('customer/group')->load((string)Mage::getConfig()->getNode('default/nscexport/customer_group_code'),"customer_group_code");
		$date = Mage::app()->getLocale()->storeDate($store->getId());
        
        $wId = $store->getWebsiteId();
        $gId = $custmerGroup->getId();
        $pId = $product->getId();

        $key = "$date|$wId|$gId|$pId";

        if ($key) 
        {
            if (!isset($this->_rulePrices[$key])) 
            {
            	$rulePrice = Mage::getResourceModel('catalogrule/rule')
                    ->getRulePrice($date, $wId, $gId, $pId);
                $this->_rulePrices[$key] = $rulePrice;
            }
            if ($this->_rulePrices[$key]!==false) 
            {            	
                $finalPrice = min($product->getFinalPrice() , $this->_rulePrices[$key]);
                $product->setFinalPrice($finalPrice);
            }
        }
        return $this;
    }
    
	/**
     * Generate XML nscexport for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    private function scheduledNscexportXML($schedule,$searchEngine)
    {   
    	//Mage::log("Cron Nostress export started by ENGINE: ". $searchEngine);
    	//Get nearest nscexport id
    	$now = getdate(time()); //get actual time
        $nextNscexportId = Nostress_Nscexport_Helper_Data::getNearestTime($searchEngine,$now); 
        
        //pernamently removed
		//Nostress_Nscexport_Helper_Data::setCronSchedule($nextNscexportId,'nscexport_nscexport'.$searchEngine,'pending');
        
        $nscexportId = Mage::getModel('core/config_data')->load('nscexport/nscexport'.$searchEngine.'/id', 'path')->getValue();
        if($nscexportId == NULL || $nscexportId == "") 
        	return;  //nscexport not set
        if($nscexportId != $nextNscexportId)
        	Nostress_Nscexport_Helper_Data::setConfigData($nextNscexportId);
    	    			
    	$nscexport = Mage::getModel(Nostress_Nscexport_Helper_Data::getEngineModelName($searchEngine)); 		
    	
    	//find all nscexports between now and first nscexport
    	$idsToNscexport = Nostress_Nscexport_Helper_Data::findNscexports($now,$nscexportId);
    	
    	//generate actual xml nscexports
    	foreach($idsToNscexport as $id)
    	{
    		if(Nostress_Nscexport_Helper_Data::allowGenerateXml($id))  //generate XML file if allowed
    		{    
    			//Mage::log('$nscexport->generateXml('.$id.'); START');		     
       		 	$nscexport->generateXml($id);
       		 	//Mage::log('$nscexport->generateXml('.$id.'); FINISH');	
    		}
    	}                                              
    }
    
	/**
     * Generate XML export for search engine acheterfacile
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportAcheterfacileXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'acheterfacile');                                            
    }
    
	/**
     * Generate XML export for search engine Amazon
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportAmazonXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'amazon');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportBeslistXML($schedule)
    {
    	$this->scheduledNscexportXML($schedule,'beslist');                                            
    }
    
	/**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportBestpriceXML($schedule)
    {
    	$this->scheduledNscexportXML($schedule,'bestprice');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportBleskXML($schedule)
    {
    	$this->scheduledNscexportXML($schedule,'blesk');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportBolhaXML($schedule)
    {
    	$this->scheduledNscexportXML($schedule,'bolha');                                            
    }
    
    /**
     * Generate XML nscexport for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportCenejeXML($schedule)
    { 
    	$this->scheduledNscexportXML($schedule,'ceneje');                                            
    }
    
    /**
     * Generate XML nscexport for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportCentrumXML($schedule)
    { 
    	$this->scheduledNscexportXML($schedule,'centrum');                                            
    }
    
    /**
     * Generate XML nscexport for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportCiaoXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'ciao');                                            
    }
    
	/**
     * Generate XML nscexport for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportCiaoesXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'ciaoes');                                            
    }
    
	/**
     * Generate XML nscexport for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportCiaocoukXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'ciaocouk');                                            
    }
    
	/**
     * Generate XML nscexport for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportCleafsXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'cleafs');                                            
    }

	/**
     * Generate XML nscexport for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportComparegroupeuXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'comparegroupeu');                                            
    }    
    
    /**
     * Generate XML nscexport for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportDaisyconXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'daisycon');                                            
    }  
    
    /**
     * Generate XML nscexport for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportEbuyclubXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'ebuyclub');                                            
    }    

     /**
     * Generate XML nscexport for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportErosiaXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'erosia');                                            
    }  

    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportFfshoppenXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'ffshoppen');                                            
    }
    
	/**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportFruugoXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'fruugo');                                            
    }
    
     /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportGoogleXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'google');                                            
    }
    
	/**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportGreeneXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'greene');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportHeurekaXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'heureka');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportHledejcenyXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'hledejceny');                                            
    }
    
	/**
     * Generate XML nscexport for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportCherchonsXML($schedule)
    { 
    	$this->scheduledNscexportXML($schedule,'cherchons');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportIcomparateurXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'icomparateur');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportIdealoXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'idealo');                                            
    }
    
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportJyxoXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'jyxo');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportKeldeliceXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'keldelice');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportKelkooXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'kelkoo');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportKelkoocoukXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'kelkoocouk');                                            
    }
    
	/**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportKoopjespakkerXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'koopjespakker');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportLeguideXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'leguide');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportLovecnaceneXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'lovecnacene');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportMercamaniaXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'mercamania');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportMonitorcienXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'monitorcien');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportM4nXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'m4n');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportNajnakupXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'najnakup');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportNejlepsicenyXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'nejlepsiceny');                                            
    }
     
     /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportNextagXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'nextag');                                            
    }
    
     /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportPricegrabberXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'pricegrabber');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportPreisroboterXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'preisroboter');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportPricemaniaXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'pricemania');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportSeznamXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'seznam');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportShopbotXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'shopbot');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportShopmaniaXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'shopmania');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportShoppingXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'shopping');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportShopzillaXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'shopzilla');                                            
    }
    
	/**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportSkroutzXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'skroutz');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportSuperdealXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'superdeal');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportTouslesprixXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'touslesprix');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportTovarXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'tovar');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportTrovaprezziXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'trovaprezzi');                                            
    }
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportTwengaXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'twenga');                                            
    } 
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportTwengacoukXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'twengacouk');                                            
    } 
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportTwengafrXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'twengafr');                                            
    } 
    
        /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportTwengafrxmlXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'twengafrxml');                                            
    } 
    
    /**
     * Generate XML export for search engines
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledNscexportYahooXML($schedule)
    {   
    	$this->scheduledNscexportXML($schedule,'yahoo');                                            
    } 
}