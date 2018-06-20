<?php
/**
 * Profile.php
 * CommerceExtensions @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commerceextensions.com/LICENSE-M1.txt
 *
 * @category   Profile
 * @package    Profile
 * @copyright  Copyright (c) 2003-2017 CommerceExtensions @ InterSEC Solutions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */ 

class CommerceExtensions_Advancedcustomerimportexport_Model_Profile extends Mage_Dataflow_Model_Profile
{
    protected function _construct()
    {
        $this->_init('dataflow/profile');
    }
    
    public function _parseGuiData()
    {
        $nl = "\r\n";
        $import = $this->getDirection()==='import';
        $p = $this->getGuiData();

        if ($this->getDataTransfer()==='interactive') {

            $interactiveXml = '<action type="dataflow/convert_adapter_http" method="'
                . ($import ? 'load' : 'save') . '">' . $nl;
            #$interactiveXml .= '    <var name="filename"><![CDATA['.$p['interactive']['filename'].']]></var>'.$nl;
            $interactiveXml .= '</action>';

            $fileXml = '';
        } else {
            $interactiveXml = '';
			$downloadUrl = Mage::helper("adminhtml")->getUrl("/advancedcustomerimportexport/download", array('file'=>$p['file']['filename']));

            $fileXml = '<action type="dataflow/convert_adapter_io" method="'
                . ($import ? 'load' : 'save') . '">' . $nl;
            $fileXml .= '    <var name="type">' . $p['file']['type'] . '</var>' . $nl;
            $fileXml .= '    <var name="path">' . $p['file']['path'] . '</var>' . $nl;
            $fileXml .= '    <var name="filename"><![CDATA[' . $p['file']['filename'] . ']]></var>' . $nl;
			$fileXml .= '    <var name="link">'. $downloadUrl . '</var>' . $nl;
			
            if ($p['file']['type']==='ftp') {
                $hostArr = explode(':', $p['file']['host']);
                $fileXml .= '    <var name="host"><![CDATA[' . $hostArr[0] . ']]></var>' . $nl;
                if (isset($hostArr[1])) {
                    $fileXml .= '    <var name="port"><![CDATA[' . $hostArr[1] . ']]></var>' . $nl;
                }
                if (!empty($p['file']['passive'])) {
                    $fileXml .= '    <var name="passive">true</var>' . $nl;
                }
                if ((!empty($p['file']['file_mode']))
                        && ($p['file']['file_mode'] == FTP_ASCII || $p['file']['file_mode'] == FTP_BINARY)
                ) {
                    $fileXml .= '    <var name="file_mode">' . $p['file']['file_mode'] . '</var>' . $nl;
                }
                if (!empty($p['file']['user'])) {
                    $fileXml .= '    <var name="user"><![CDATA[' . $p['file']['user'] . ']]></var>' . $nl;
                }
                if (!empty($p['file']['password'])) {
                    $fileXml .= '    <var name="password"><![CDATA[' . $p['file']['password'] . ']]></var>' . $nl;
                }
            }
            if ($import) {
                $fileXml .= '    <var name="format"><![CDATA[' . $p['parse']['type'] . ']]></var>' . $nl;
            }
            $fileXml .= '</action>' . $nl . $nl;
        }

        switch ($p['parse']['type']) {
            case 'excel_xml':
                $parseFileXml = '<action type="dataflow/convert_parser_xml_excel" method="'
                    . ($import ? 'parse' : 'unparse') . '">' . $nl;
                $parseFileXml .= '    <var name="single_sheet"><![CDATA['
                    . ($p['parse']['single_sheet'] !== '' ? $p['parse']['single_sheet'] : '')
                    . ']]></var>' . $nl;
                break;

            case 'csv':
                $parseFileXml = '<action type="dataflow/convert_parser_csv" method="'
                    . ($import ? 'parse' : 'unparse') . '">' . $nl;
                $parseFileXml .= '    <var name="delimiter"><![CDATA['
                    . $p['parse']['delimiter'] . ']]></var>' . $nl;
                $parseFileXml .= '    <var name="enclose"><![CDATA['
                    . $p['parse']['enclose'] . ']]></var>' . $nl;
                break;
        }
        $parseFileXml .= '    <var name="fieldnames">' . $p['parse']['fieldnames'] . '</var>' . $nl;
        $parseFileXmlInter = $parseFileXml;
        $parseFileXml .= '</action>' . $nl . $nl;

        $mapXml = '';

        if (isset($p['map']) && is_array($p['map'])) {
            foreach ($p['map'] as $side=>$fields) {
                if (!is_array($fields)) {
                    continue;
                }
                foreach ($fields['db'] as $i=>$k) {
                    if ($k=='' || $k=='0') {
                        unset($p['map'][$side]['db'][$i]);
                        unset($p['map'][$side]['file'][$i]);
                    }
                }
            }
        }
        $mapXml .= '<action type="dataflow/convert_mapper_column" method="map">' . $nl;
        //$map = $p['map'][$this->getEntityType()];
		$map = isset($p['map'][$this->getEntityType()]) ? $p['map'][$this->getEntityType()] : null;
		if(isset($map['db'])) {
			if (sizeof($map['db']) > 0) {
				$from = $map[$import?'file':'db'];
				$to = $map[$import?'db':'file'];
				$mapXml .= '    <var name="map">' . $nl;
				$parseFileXmlInter .= '    <var name="map">' . $nl;
				foreach ($from as $i=>$f) {
					$mapXml .= '        <map name="' . $f . '"><![CDATA[' . $to[$i] . ']]></map>' . $nl;
					$parseFileXmlInter .= '        <map name="' . $f . '"><![CDATA[' . $to[$i] . ']]></map>' . $nl;
				}
				$mapXml .= '    </var>' . $nl;
				$parseFileXmlInter .= '    </var>' . $nl;
			}
		}
        if ($p['map']['only_specified']) {
            $mapXml .= '    <var name="_only_specified">' . $p['map']['only_specified'] . '</var>' . $nl;
            //$mapXml .= '    <var name="map">' . $nl;
            $parseFileXmlInter .= '    <var name="_only_specified">' . $p['map']['only_specified'] . '</var>' . $nl;
        }
        $mapXml .= '</action>' . $nl . $nl;

        $parsers = array(
            'product'=>'catalog/convert_parser_product',
            'customer'=>'customer/convert_parser_customer',
        );

        if ($import) {
//            if ($this->getDataTransfer()==='interactive') {
                $parseFileXmlInter .= '    <var name="store"><![CDATA[' . $this->getStoreId() . ']]></var>' . $nl;
//            } else {
//                $parseDataXml = '<action type="' . $parsers[$this->getEntityType()] . '" method="parse">' . $nl;
//                $parseDataXml = '    <var name="store"><![CDATA[' . $this->getStoreId() . ']]></var>' . $nl;
//                $parseDataXml .= '</action>'.$nl.$nl;
//            }
//            $parseDataXml = '<action type="'.$parsers[$this->getEntityType()].'" method="parse">'.$nl;
//            $parseDataXml .= '    <var name="store"><![CDATA['.$this->getStoreId().']]></var>'.$nl;
//            $parseDataXml .= '</action>'.$nl.$nl;
        } else {
            $parseDataXml = '<action type="advancedcustomerimportexport/convert_parser_customerexport" method="unparse">' . $nl;
            $parseDataXml .= '    <var name="store"><![CDATA[' . $this->getStoreId() . ']]></var>' . $nl;
            if (isset($p['export']['add_url_field'])) {
                $parseDataXml .= '    <var name="url_field"><![CDATA['
                    . $p['export']['add_url_field'] . ']]></var>' . $nl;
            }
            
            // Custom fields
			$recordLimitStart = isset($p['unparse']['recordlimitstart']) ? $p['unparse']['recordlimitstart'] : '0';
			$parseDataXml .= '    <var name="recordlimitstart"><![CDATA[' . $recordLimitStart . ']]></var>' . $nl;
			
			$recordLimitEnd = isset($p['unparse']['recordlimitend']) ? $p['unparse']['recordlimitend'] : '100';
			$parseDataXml .= '    <var name="recordlimitend"><![CDATA[' . $recordLimitEnd . ']]></var>' . $nl;
			
			$exportCustomerId = isset($p['unparse']['export_customer_id']) ? $p['unparse']['export_customer_id'] : 'false';
			$parseDataXml .= '    <var name="export_customer_id"><![CDATA[' . $exportCustomerId . ']]></var>' . $nl;
			
			$exportMultipleAddresses = isset($p['unparse']['export_multiple_addresses']) ? $p['unparse']['export_multiple_addresses'] : 'false';
			$parseDataXml .= '    <var name="export_multiple_addresses"><![CDATA[' . $exportMultipleAddresses . ']]></var>' . $nl;
			
			$filter_by_city = isset($p['unparse']['filter_by_city']) ? $p['unparse']['filter_by_city'] : '';
			$parseDataXml .= '    <var name="filter_by_city"><![CDATA[' . $filter_by_city . ']]></var>' . $nl;
            
            $parseDataXml .= '</action>' . $nl . $nl;
        }

        $adapters = array(
            'product'=>'catalog/convert_adapter_product',
            'customer'=>'customer/convert_adapter_customer',
        );

        if ($import) {
            $entityXml = '<action type="' . $adapters[$this->getEntityType()] . '" method="save">' . $nl;
            $entityXml .= '    <var name="store"><![CDATA[' . $this->getStoreId() . ']]></var>' . $nl;
            $entityXml .= '</action>' . $nl . $nl;
        } else {
            $entityXml = '<action type="advancedcustomerimportexport/convert_adapter_customerimport" method="load">' . $nl;
            $entityXml .= '    <var name="store"><![CDATA[' . $this->getStoreId() . ']]></var>' . $nl;
            foreach ($p[$this->getEntityType()]['filter'] as $f=>$v) {

                if (empty($v)) {
                    continue;
                }
                if (is_scalar($v)) {
                    $entityXml .= '    <var name="filter/' . $f . '"><![CDATA[' . $v . ']]></var>' . $nl;
                    $parseFileXmlInter .= '    <var name="filter/' . $f . '"><![CDATA[' . $v . ']]></var>' . $nl;
                } elseif (is_array($v)) {
                    foreach ($v as $a=>$b) {

                        if (strlen($b) == 0) {
                            continue;
                        }
                        $entityXml .= '    <var name="filter/' . $f . '/' . $a
                            . '"><![CDATA[' . $b . ']]></var>' . $nl;
                        $parseFileXmlInter .= '    <var name="filter/' . $f . '/'
                            . $a . '"><![CDATA[' . $b . ']]></var>' . $nl;
                    }
                }
            }
            
            //$entityXml .= '    <var name="filter/adressType"><![CDATA[default_billing]]></var>' . $nl;
            
            $entityXml .= '</action>' . $nl . $nl;
        }

        // Need to rewrite the whole xml action format
        if ($import) {
            $numberOfRecords = isset($p['import']['number_of_records']) ? $p['import']['number_of_records'] : 1;
            $decimalSeparator = isset($p['import']['decimal_separator']) ? $p['import']['decimal_separator'] : ' . ';
            $parseFileXmlInter .= '    <var name="number_of_records">'
                . $numberOfRecords . '</var>' . $nl;
            $parseFileXmlInter .= '    <var name="decimal_separator"><![CDATA['
                . $decimalSeparator . ']]></var>' . $nl;
            
            // Custom fields
            $do_not_update_customer = isset($p['parse']['do_not_update_customer']) ? $p['parse']['do_not_update_customer'] : 'false';
            $parseFileXmlInter .= '    <var name="do_not_update_customer"><![CDATA[' . $do_not_update_customer . ']]></var>' . $nl;
			
            $auto_create_customer_groups = isset($p['parse']['auto_create_customer_groups']) ? $p['parse']['auto_create_customer_groups'] : 'false';
            $parseFileXmlInter .= '    <var name="auto_create_customer_groups"><![CDATA[' . $auto_create_customer_groups . ']]></var>' . $nl;
			
            $insert_customer_id = isset($p['parse']['insert_customer_id']) ? $p['parse']['insert_customer_id'] : 'false';
            $parseFileXmlInter .= '    <var name="insert_customer_id"><![CDATA[' . $insert_customer_id . ']]></var>' . $nl;
			
            $delete_all_customer_address_first = isset($p['parse']['delete_all_customer_address_first']) ? $p['parse']['delete_all_customer_address_first'] : 'false';
            $parseFileXmlInter .= '    <var name="delete_all_customer_address_first"><![CDATA[' . $delete_all_customer_address_first . ']]></var>' . $nl;
            $update_customer_address = isset($p['parse']['update_customer_address']) ? $p['parse']['update_customer_address'] : 'false';
            $parseFileXmlInter .= '    <var name="update_customer_address"><![CDATA[' . $update_customer_address . ']]></var>' . $nl;
			
            $import_multiple_customer_address = isset($p['parse']['import_multiple_customer_address']) ? $p['parse']['import_multiple_customer_address'] : 'false';
            $parseFileXmlInter .= '    <var name="import_multiple_customer_address"><![CDATA[' . $import_multiple_customer_address . ']]></var>' . $nl;
			
            $update_customer_password = isset($p['parse']['update_customer_password']) ? $p['parse']['update_customer_password'] : 'false';
            $parseFileXmlInter .= '    <var name="update_customer_password"><![CDATA[' . $update_customer_password . ']]></var>' . $nl;
			
            $email_customer_password = isset($p['parse']['email_customer_password']) ? $p['parse']['email_customer_password'] : 'false';
            $parseFileXmlInter .= '    <var name="email_customer_password"><![CDATA[' . $email_customer_password . ']]></var>' . $nl;
			
            
            if ($this->getDataTransfer()==='interactive') {
                $xml = $parseFileXmlInter;
                $xml .= '    <var name="adapter">advancedcustomerimportexport/convert_adapter_customerimport</var>' . $nl;
                $xml .= '    <var name="method">parse</var>' . $nl;
                $xml .= '</action>';
            } else {
                $xml = $fileXml;
                $xml .= $parseFileXmlInter;
                $xml .= '    <var name="adapter">advancedcustomerimportexport/convert_adapter_customerimport</var>' . $nl;
                $xml .= '    <var name="method">parse</var>' . $nl;
                $xml .= '</action>';
            }
            //$xml = $interactiveXml.$fileXml.$parseFileXml.$mapXml.$parseDataXml.$entityXml;

        } else {
            $xml = $entityXml . $parseDataXml . $mapXml . $parseFileXml . $fileXml . $interactiveXml;
        }

        $this->setGuiData($p);
        $this->setActionsXml($xml);
/*echo "<pre>" . print_r($p,1) . "</pre>";
echo "<xmp>" . $xml . "</xmp>";
die;*/
        return $this;
    }
}