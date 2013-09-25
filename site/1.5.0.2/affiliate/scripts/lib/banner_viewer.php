<?php
require_once 'lib/fast_init.php';

class Lib_BannerViewer {
    
    /**
     * @var Lib_SettingFile
     */
    private $settings;
    
    private $bannerParams;
    
    public function __construct(Lib_SettingFile $settings) {
        $this->settings = $settings;
        $this->initParams();
    }
    
    private function initParams() {
        $this->bannerParams = array(
            'userid' => @$_GET[$this->getSettingFromFile(Lib_SettingFile::PARAM_NAME_USER_ID)],
            'bannerid' => @$_GET[$this->getSettingFromFile(Lib_SettingFile::PARAM_NAME_BANNER_ID)],
            'parentbannerid' => @$_GET[$this->getSettingFromFile(Lib_SettingFile::PARAM_NAME_ROTATOR_ID)],
            'channel' => @$_GET['chan'],
            'wrapper' => @$_GET['w'],
            'dynamiclink' => @$_GET['dynamiclink']);
    }
    
    private function getSettingFromFile($name) {
        $value = $this->settings->get($name);
        if ($value == '') {
            throw new Exception('Missing option ' . $name . ' in settings.php file. File is probably corrupt.');
        }
        return $value;
    }
    
    /**
     * @return Lib_CachedBanners
     */
    protected function getBannersFromCache() {
        return new Lib_CachedBanners($this->settings->getDb(), $this->bannerParams);
    }
    
    public function displayBanner() {
        $cachedBanners = $this->getBannersFromCache();
        
        if ($cachedBanners->getCount() == 0) {
            return false;
        }
        
        if ($cachedBanners->getCount() == 1) {
            $banner = $cachedBanners->getNext();
            $this->showBanner($banner);
            return true;
        }
        
        $sum = 0;
        $lastBanner = array();
        
        while ($banner = $cachedBanners->getNext()) {
            $sum += $banner['rank'];
            $banners[] = $banner;
        }
        $rnd = $this->getRand($sum);
        $sum = 0;
        foreach($banners as $banner){
            $sum += $banner['rank'];
            if ($rnd <= $sum) {
                $this->showBanner($banner);
                return true;
            }
            $lastBanner = $banner;
        }
        $this->showBanner($lastBanner);
        return true;
    }
    
    protected function getRand($max) {
        list($usec, $sec) = explode(' ', microtime());
        srand((float) $sec + ((float) $usec * 100000));
        return rand(0, $max);
    }
    
    /**
     * @param array $bannerArray <headers, code, rank>
     * @return unknown_type
     */
    protected function showBanner(array $bannerArray) {
        if ($bannerArray['headers'] != '') {
            header($bannerArray['headers'], true);
        }
        echo $bannerArray['code'];
    }
    
    public function getBannerParams() {
        return $this->bannerParams;
    }
}

class Lib_CachedBanners {
    const DATETIME_FORMAT = 'Y-m-d H:i:s';
    private $res;
    private $count;
    
    public function __construct(Lib_Db $db, array $bannerParams) {
        $where = '';
        foreach ($bannerParams as $key => $value) {
            $where .= $key .'=\'' . $db->escape($value) . '\' AND ';
        }
        $select = 'SELECT headers, code, rank, valid_from, valid_until FROM qu_pap_cachedbanners WHERE '.rtrim($where, 'AND ').' AND (valid_until > \''.date(self::DATETIME_FORMAT).'\' OR valid_until IS NULL) AND (valid_from < \''.date(self::DATETIME_FORMAT).'\' or valid_from IS NULL)';
        $this->res = $db->query($select);
        if($this->res){
            $this->count = mysql_num_rows($this->res);
        }
    }
    
    public function getCount() {
        return $this->count;
    }
    
    public function getNext() {
        return mysql_fetch_assoc($this->res);
    }
}
?>
