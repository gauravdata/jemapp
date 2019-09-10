<?php

class MT_Email_Model_Var extends Mage_Core_Model_Abstract
{
    private $__varArray = array();

    private $__varModelArray = array();

    private $__editFlag = false;

    private $__template = null;

    private $__storeGlobalVars = null;

    private $__ignoreVars = array();

    protected function _construct()
    {
        $this->_init('mtemail/var');
        $isEditor = Mage::registry('mt_editor_edit_mode');
        if ($isEditor && $isEditor == 1) {
            $this->__editFlag = true;
        }
    }

    public function loadByKey($key, $templateId, $blockName, $blockId)
    {
        if (empty($key) || empty($templateId))
            return $this;

        $collection = $this->getCollection()
            ->addFieldToFilter('var_key', $key)
            ->addFieldToFilter('template_id', $templateId)
            ->addFieldToFilter('block_name', $blockName)
            ->addFieldToFilter('block_id', $blockId)
            ->setPageSize(1);

        if ($collection->count() == 0)
            return $this;

        return parent::load($collection->getFirstItem()->getId());
    }

    public function getTextEditAttribute($key)
    {
        if (!$this->__editFlag) {
            return '';
        }

        $result = ' contenteditable="true" data-var-text="'.$key.'"';
        return $result;
    }

    public function getHtmlAttribute($attribute, $key, $default, $global = true)
    {
        $value = $this->getValue($key, $default, $global);
        $result = ' '.$attribute.'="'.$value.'" ';;
        if ($this->__editFlag) {
            $result .= ' data-var-'.$attribute.'="'.$key.'"';
        }

        return $result;
    }

    public function getVar($key, $default = '', $global = true)
    {


        $templateId = $this->getTemplateId();
        $template = $this->getTemplate($templateId);
        $storeId = $this->getStoreId();
        $blockName = $this->getBlockName();
        $blockId = $this->getBlockId();

        if (!isset($this->__varArray[$templateId])) {
            $varArray = array();
            $varModel = Mage::getModel('mtemail/var')->getCollection()
                ->addFieldToFilter('template_id', $templateId)
                ->addFieldToFilter('store_id', $storeId);
            if ($varModel->count() > 0) {
                foreach ($varModel as $var) {
                    $varArray[$var->getBlockName()][$var->getBlockId()][$var->getVarKey()] = $var;
                }
                $this->__varArray[$templateId] = $varArray;
            }
        }

        if (!isset($this->__varArray[$templateId][$blockName][$blockId][$key])) {
            $newVar =  $this->createVar($template, $blockId, $blockName, $key, $default, $global);
            $this->__varArray[$templateId][$blockName][$blockId][$key] = $newVar;
        }

        $var = $this->__varArray[$templateId][$blockName][$blockId][$key];
        $this->checkVisibility($var, $global);

        return $var;
    }

    /**
     * update visibility if it was changed
     *
     * @param $var
     * @param $global
     */
    public function checkVisibility($var, $global)
    {
        //update var if there are changes
        $varIsGlobal = $var->getGlobal()?true:false;
        if ($varIsGlobal != $global) {
            $var->setGlobal($global?1:0);
            $var->save();
        }
    }

    /**
     * Returns var value
     *
     * @param string $key
     * @param string $default
     * @param bool $global
     * @return mixed
     */
    public function getValue($key, $default = '', $global = true)
    {
        return $this->getVar($key, $default, $global)->getVarValue();
    }

    /**
     * Returns var value from store config
     *
     * @param string $key
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getDefaultValue($key)
    {
        $storeId = $this->getStore()->getId();
        switch($key) {
            case 'logo_src':
                return $this->getLogoUrl($storeId);
                break;
            case 'logo_href':
                return $this->getStore($storeId)->getBaseUrl();
                break;
            case 'logo_alt':
                return $this->getLogoAlt($storeId);
            case 'store':
                return $this->getStore();
        }

        return '';
    }

    public function getVarValue()
    {
        $varValue = parent::getVarValue();
        if ($this->getIsSystemConfig()) {
            $varValue = Mage::getStoreConfig($this->getVarValue(), $this->getStoreId());
        }

        return $varValue;
    }

    protected function getLogoUrl($storeId = 0)
    {
        $version = Mage::getVersionInfo();
        if ( $version['major'] == 1 && $version['minor'] > 6 ) {
            $fileName = Mage::getStoreConfig(Mage_Core_Model_Email_Template::XML_PATH_DESIGN_EMAIL_LOGO, $storeId);
            if ($fileName) {
                $uploadDir = Mage_Adminhtml_Model_System_Config_Backend_Email_Logo::UPLOAD_DIR;
                $fullFileName = Mage::getBaseDir('media') . DS . $uploadDir . DS . $fileName;
                if (file_exists($fullFileName)) {
                    return Mage::getBaseUrl('media') . $uploadDir . '/' . $fileName;
                }
            }
        }

        return Mage::getDesign()->getSkinUrl('images/logo_email.gif');
    }

    protected function getLogoAlt($storeId = 0)
    {
        $version = Mage::getVersionInfo();
        if ( $version['major'] == 1 && $version['minor'] > 6 ) {
            $alt =  Mage::getStoreConfig(Mage_Core_Model_Email_Template::XML_PATH_DESIGN_EMAIL_LOGO_ALT, $storeId);
            if ($alt) {
                return $alt;
            }
        }

        return Mage::app()->getStore($storeId)->getFrontendName();
    }

    public function getStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * return private variables from xml language file
     *
     * @param Mage_Core_Model_Email_Template $template
     * @param int $global
     * @return Varien_Data_Collection
     * @throws Exception
     */
    public function getDefaultVarCollection($template, $global = 0)
    {
        $templateCode = $template->getOrigTemplateCode();
        $locale = $template->getLocale();

        $locale = strtolower($locale);
        $collection = new Varien_Data_Collection();

        $path =  Mage::getBaseDir(). DS .'app' .DS. 'code' .DS. 'community' .DS. 'MT'. DS . 'Email'. DS. 'etc'. DS. 'locale' . DS;
            $fileName = $path.$locale.'.xml';
        if (!file_exists($fileName)) {
            $fileName = $path.'en_us.xml';
        }

        $xmlObj = new Varien_Simplexml_Config($fileName);
        $xmlData = $xmlObj->getNode();
        if (count($xmlData->row) > 0) {
            foreach ($xmlData->row as $item) {

                if (
                    ($global == 0 && $item->global == 0 && $item->template_code == $templateCode)
                    || ($global == 1 && $item->global == 1)
                ) {
                    $newVar = Mage::getModel('mtemail/var');
                    $data = array(
                        'block_id' => (int)$item->block_id,
                        'block_name' => (string)$item->block_name,
                        'var_key' => (string)$item->var_key,
                        'var_value' => (string)$item->var_value,
                        'global' => (int)$item->global,

                        'template_code' => (string)$item->template_code,
                    );
                    if ($item->is_default) {
                        $data['is_default'] = (int)$item->is_default;
                    }

                    if ($item->block_id) {
                        $data['block_id'] = (int)$item->block_id;
                    }

                    $newVar->setData($data);
                    $collection->addItem($newVar);
                }
            }
        }

        return $collection;
    }

    public function getTemplateVarCollection($templateId)
    {
        $collection = Mage::getModel('mtemail/var')->getCollection()
            ->addFieldToFilter('template_id', $templateId);
        return $collection;
    }

    /**
     * Get template variable model
     *
     * @param $template
     * @param $blockName
     * @param $blockId
     * @param $varKey
     * @return null
     */
    public function getVarObject($template, $blockName, $blockId, $varKey)
    {
        $templateId = $template->getId();
        $storeId = $template->getStoreId();
        if (!isset($this->__varModelArray[$templateId])) {
            $varArray = array();
            $varModel = Mage::getModel('mtemail/var')->getCollection()
                ->addFieldToFilter('template_id', $templateId)
                ->addFieldToFilter('store_id', $storeId);
            if ($varModel->count() > 0) {
                foreach ($varModel as $var) {
                    $varArray[$var->getBlockName()][$var->getBlockId()][$var->getVarKey()] = $var;
                }
                $this->__varModelArray[$templateId] = $varArray;
            }
        }

        if (!isset($this->__varModelArray[$templateId][$blockName][$blockId][$varKey])) {
            return null;
        }

        return $this->__varModelArray[$templateId][$blockName][$blockId][$varKey];
    }

    /**
     * Get Template By ID
     *
     * @param int $templateId
     * @return false|Mage_Core_Model_Abstract|mixed
     */
    public function getTemplate($templateId)
    {
        if ($this->__template == null) {
            $this->__template = Mage::getModel('adminhtml/email_template');
            //preview mode
            if ($templateId === 0) {
                $data = Mage::getSingleton('core/session')->getPreviewTemplateData();
                $templateId = $data['id'];
                $this->__template->load($templateId);
                $this->__template->setTemplateId(0);
            } else {
                if ($templateId) {
                    $this->__template->load($templateId);
                }
            }
        }
        return $this->__template;
    }

    /**
     * Get global var value by using only store id filter
     *
     * @param Mage_Core_Model_Email_Template $template
     * @param string $key
     * @return string
     */
    public function getGlobalVar($template, $key)
    {
        $storeId = $template->getStoreId();

        if ($this->__storeGlobalVars == null) {
            $globalVarCollection = Mage::getModel('mtemail/var')->getCollection()
                ->addFieldToFilter('global', 1)
                ->addFieldToFilter('store_id', $storeId);
            $globalVarCollection->getSelect()->group('main_table.var_key');

            if ($globalVarCollection->count() == 0) {
                $this->__storeGlobalVars = array();
            } else {
                foreach ($globalVarCollection as $var) {
                    $this->__storeGlobalVars[$var->getVarKey()] = $var;
                }
            }
        }

        if (!isset($this->__storeGlobalVars[$key])) {
            return '';
        }

        return $this->__storeGlobalVars[$key];
    }

    /**
     * Create new variable
     *
     * @param Mage_Core_Model_Email_Template $template
     * @param int $blockId
     * @param string $blockName
     * @param string $key
     * @param string $default
     * @param int $global
     * @return MT_Email_Model_Var
     */
    public function createVar($template, $blockId, $blockName, $key, $default, $global)
    {
        $templateId = $template->getId();
        $storeId = $template->getStoreId();
        $templateCode = $template->getOrigTemplateCode();

        if ($default == '') {
            $default  = $this->getDefaultValue($key);
        }

        if ($global) {
            $globalVar = $this->getGlobalVar($template, $key);
            if ($globalVar) {
                $default = $globalVar->getVarValue();
            }
        }

        $isTmp = 0;
        if (Mage::registry('mt_email_tmp_var')) {
            $isTmp = 1;
        }
        $newVar = Mage::getModel('mtemail/var')
            ->setTemplateId($templateId)
            ->setBlockId($blockId)
            ->setBlockName($blockName)
            ->setGlobal($global?1:0)
            ->setVarKey($key)
            ->setVarValue($default)
            ->setStoreId($storeId)
            ->setTemplateCode($templateCode)
            ->setIsTmp($isTmp)
            ->save();

        return $newVar;
    }

    /**
     * Returns store id
     * @return mixed
     */
    public function getStoreId()
    {
        $templateId = $this->getTemplateId();
        $template = $this->getTemplate($templateId);
        $storeId = $template->getStoreId();
        return $storeId;
    }

    /**
     * Update variable
     *
     * @param $blockName
     * @param $blockId
     * @param $templateId
     * @param $key
     * @param $value
     * @param $applyToAll
     */
    public function saveVar($blockName, $blockId, $templateId, $key, $value, $applyToAll)
    {
        $template = $this->getTemplate($templateId);
        $storeId = $template->getStoreId();
        $var = $this->getVarObject($template, $blockName, $blockId, $key);
        if (!$var || !$var->getId()) {
            $var = $this->createVar($template, $blockId, $blockName, $key, $value, 0);
        } else {
            $var->setIsTmp(0);
            $var->setVarValue($value);
            $var->save();
        }

        //update vars in store level
        if ($applyToAll == 1) {
            if (isset($this->__ignoreVars[$blockName][$key])) {
                return;
            }

            $varCollection = Mage::getModel('mtemail/var')->getCollection()
                ->addFieldToFilter('var_key', $key)
                 ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('global', 1);

            if ($varCollection->count() > 0) {
                foreach ($varCollection as $var) {
                    $var->setVarValue($value);
                    $var->save();
                }
            }

            $this->__ignoreVars[$blockName][$key] = 1;
        }
    }
}
