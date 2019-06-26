<?php
class Biztech_Translator_Model_Search extends Mage_Core_Model_Translate  
{
        protected $locale;
        protected $storeId;
        protected $interface;
        protected $modules;

        public function _construct() {
            parent::_construct();
            $this->_init('translator/search');
        }

        public function searchString($string, $locale, $modules, $interface) {

            $results = array();
            $stringPattern = "^(?i)" . $string . "^";
            if (!empty($string)) {
                $this->modules = $modules;
                $this->interface = $interface;

                $this->_data = array();
                $resultArray = array();
                $temp = array();

                //search in Magento Array
                $this->_loadTranslations($locale);

                $results = $this->_matchTranslationInArray($stringPattern);
                if (count($results) >= 1000) {
                    $result['warning'] = 'True';
                    return $result;
                } else {
                    foreach ($results as $key => $_result) {
                        foreach ($_result as $locale => $translation) {
                            $translation['key'] = $key;
                            $temp['locale'] = $locale;
                            $temp['translation'] = $translation;
                            $resultArray[] = $temp;
                        }
                    }
                    $results = $resultArray;
                }
                return $results;
            } else {
                return $results;
            }
        }

        private function _loadTranslations($locale) {
            $config = Mage::getConfig()->getNode($this->interface . '/translate/modules')->children();
            if ($locale == 'all') {
                foreach (Mage::getModel('translator/system_config_locales')->toOptionArray() as $key => $value) {
                    $this->locale = $key;
                    foreach ($config as $moduleName => $info) {
                        if ($moduleName == $this->modules || $this->modules == "all") {
                            $info = $info->asArray();
                            $this->_loadModuleTranslation($moduleName, $info['files'], $forceReload = false);
                        }
                    }

                    $this->_loadThemeTranslation();
                    $this->_loadDbTranslation();
                }
            } else {
                $this->locale = $locale;
                foreach ($config as $moduleName => $info) {
                    if ($moduleName == $this->modules || $this->modules == "all") {
                        $info = $info->asArray();
                        $this->_loadModuleTranslation($moduleName, $info['files'], $forceReload = false);
                    }
                }
                $this->_loadThemeTranslation();
                $this->_loadDbTranslation();
            }
        }

        protected function _matchTranslationInArray($stringPattern) {
            $results = array();

            $locales = Mage::getModel('translator/system_config_locales')->toOptionArray();
            if (isset($locales['all']) || isset($localesArray['en_US'])) {
                unset($locales['all']);
                unset($locales['en_US']);
            }


            // flatten array to be able to search it
            foreach ($this->_data as $string => $locale) {
                foreach ($locale as $key => $data) {
                    $searchArray[$string . "::" . $key] = $data["translate"];
                }
            }

            // search flattened array
            $searchResults = preg_grep($stringPattern, $searchArray);

            // re-inflate array
            foreach ($searchResults as $key => $value) {
                $key = substr($key, 0, -7);
                $results[$key] = $this->_data[$key];

                foreach ($results[$key] as $locale => $array) {
                    if ($array['translate'] != $value) {
                        unset($results[$key][$locale]);
                    }
                }
            }
            return $results;
        }

        protected function _loadModuleTranslation($moduleName, $files, $forceReload = false)
        {
            foreach ($files as $file) {
                $file = Mage::getBaseDir('locale'). DS . $this->locale . DS . $file;
                $this->_addDataToTranslate($this->_getFileData($file), $moduleName, $forceReload, 'Module');
            }
            return $this;
        }

        protected function _loadThemeTranslation($forceReload = false) {
            $original = Mage::app()->getLocale()->getLocaleCode();
            Mage::app()->getLocale()->setLocaleCode($this->locale);
            $file = Mage::getDesign()->getLocaleFileName('translate.csv');
            Mage::app()->getLocale()->setLocaleCode($original);
            $this->_addDataToTranslate($this->_getFileData($file), false, $forceReload, 'Theme');
            return $this;
        }

        protected function _loadDbTranslation($forceReload = false) {
            $arr = $this->getResource()->getTranslationArray($this->getSearchStoreId(), $this->locale);
            $this->_addDataToTranslate($arr, $this->getConfig(self::CONFIG_KEY_STORE), $forceReload, 'Database');
            return $this;
        }

        protected function _addDataToTranslate($data, $scope, $forceReload, $translationSource) {
            foreach ($data as $key => $value) {
                $key = $this->_prepareDataString($key);
                $value = $this->_prepareDataString($value);
                $locale = $this->locale;

                if ($scope && isset($this->_dataScope[$key]) && !$forceReload) {
                    /**
                    * Checking previos value
                    */
                    $scopeKey = $this->_dataScope[$key] . self::SCOPE_SEPARATOR . $key;
                    if (!isset($this->_data[$scopeKey])) {
                        if (isset($this->_data[$key])) {
                            /**
                            * Not allow use translation not related to module
                            */
                            if (Mage::getIsDeveloperMode()) {
                                unset($this->_data[$key]);
                            }
                        }
                    }
                    $scopeKey = $scope . self::SCOPE_SEPARATOR . $key;

                    $this->_data[$scopeKey][$locale] = array(
                        "translate" => $value,
                        "source" => $translationSource . " (" . $scope . ")"
                    );
                } else {
                    $this->_data[$key][$locale] = array(
                        "translate" => $value,
                        "source" => $translationSource . " (" . $scope . ")"
                    );
                    $this->_dataScope[$key] = $scope;
                }
            }
            return $this;
        }
        protected function getSearchStoreId() {
            return $this->storeId;
        }

        protected function setSearchStoreId($storeId) {
            $this->storeId = $storeId;
        }
    }
