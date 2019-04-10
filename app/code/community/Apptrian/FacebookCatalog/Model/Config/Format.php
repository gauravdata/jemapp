<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookCatalog
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
class Apptrian_FacebookCatalog_Model_Config_Format
{
    const FORMAT_CSV     = 'csv';
    const FORMAT_TSV     = 'tsv';
    const FORMAT_XML_RSS = 'xml-rss';
    
    protected $_options;
    
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options[] = array(
                    'value' => self::FORMAT_CSV,
                    'label' => Mage::helper('apptrian_facebookcatalog')
                    ->__('CSV')
            );
            $this->_options[] = array(
                'value' => self::FORMAT_TSV,
                'label' => Mage::helper('apptrian_facebookcatalog')
                    ->__('TSV')
            );
            $this->_options[] = array(
                'value' => self::FORMAT_XML_RSS,
                'label' => Mage::helper('apptrian_facebookcatalog')
                    ->__('XML RSS')
            );
        }
        
        return $this->_options;
    }
}
