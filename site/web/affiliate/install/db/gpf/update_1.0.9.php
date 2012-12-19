<?php
/**
 * Update step will delete plugin configuration file in every account 
 *
 */

class CountriesConfigFile {
    
	const FILE_NAME = 'countries.php';
    
    private $path;
    
    function __construct() {
        $this->path = Gpf_Paths::getInstance()->getRealAccountConfigDirectoryPath(). self::FILE_NAME;
    }
    
    public function read() {
        $file = new Gpf_Io_File($this->path);
        if (!$file->isExists()) {
            throw new Gpf_Exception("File '".$this->path."' does not exist");
        }
        $file->open();
        if (!$countries = $file->readLine()) {
            $countries = '';
        }
        $file->close();
        
        return $countries;
    }
}

class gpf_update_1_0_9 {
    public function execute() {
        $this->setCountriesFromOldConfigFile();
    }
    
    public function setCountriesFromOldConfigFile() {
        $countriesConfigFile = new CountriesConfigFile();
        try {
            $countries = $countriesConfigFile->read();	
        } catch (Gpf_Exception $e) {
        	Gpf_Country_Countries::insertCountriesToDB(Gpf_Db_Country::STATUS_ENABLED);
            return;
        }
        
        if ($countries == '') {
        	Gpf_Country_Countries::insertCountriesToDB(Gpf_Db_Country::STATUS_ENABLED);
        	return;
        }
        Gpf_Country_Countries::insertCountriesToDB(Gpf_Db_Country::STATUS_DISABLED);
        $countriesCodesArray = preg_split('/,/', $countries);
        
        foreach ($countriesCodesArray as $countryCode) {
            $country = new Gpf_Db_Country();
            $country->setCountryCode($countryCode);
            $country->setAccountId(Gpf_Session::getAuthUser()->getAccountId());
            try {
                $country->loadFromData(array(Gpf_Db_Table_Countries::COUNTRY_CODE, Gpf_Db_Table_Countries::ACCOUNTID));
                $country->setStatus(Gpf_Db_Country::STATUS_ENABLED);
                $country->save();
            } catch (Gpf_Exception $e) {
            }
            
        }
    }
}
?>
