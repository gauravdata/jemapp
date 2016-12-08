<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 11-4-2016
 * Time: 9:20
 */ 
class Twm_Fix_Model_Core_Config extends Mage_Core_Model_Config
{
    /**
     * Initialization of core configuration
     *
     * @return Mage_Core_Model_Config
     */
    public function init($options=array())
    {
        $this->setCacheChecksum(null);
        $this->_cacheLoadedSections = array();
        $this->setOptions($options);
        $this->loadBase();

        $cacheLoad = $this->loadModulesCache();
        if ($cacheLoad) {
            return $this;
        }
        //100 Router Fix Start
        $this->_useCache = false;
        //100 Router Fix End
        $this->loadModules();
        $this->loadDb();
        $this->saveCache();
        return $this;
    }
}