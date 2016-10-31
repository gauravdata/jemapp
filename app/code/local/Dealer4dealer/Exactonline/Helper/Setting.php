<?php
class Dealer4dealer_Exactonline_Helper_Setting extends Mage_Core_Helper_Abstract
{
    protected $_collection = null;

    public function generateSettingsCsv()
    {
        $io = new Varien_Io_File();

        $path = Mage::getBaseDir('var') . DS . 'export';
        $name = md5(uniqid());
        $filePath = $path . DS . $name;

        $io->setAllowCreateFolders(true);
        $io->open(array('path'=>$path));
        $io->streamOpen($filePath,'w+');
        $io->streamLock(true);

        $io->streamWriteCsv($this->getHeader());

        foreach($this->getSettingsCollection() as $setting) {
            $io->streamWriteCsv(array(
                $setting->getName(),
                $setting->getLabel(),
                $setting->getValue(),
                $setting->getCategoryId()
            ));
        }

        return array(
            'type'  => 'filename',
            'value' => $filePath,
            'rm'    => true
        );
    }

    public function getSettingsCollection()
    {
        if(is_null($this->_collection)) {
            $this->_collection = Mage::getModel('exactonline/setting')->getCollection();
        }

        return $this->_collection;
    }

    public function getHeader()
    {
        return array('name','label','value','category_id');
    }

}