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
 * @copyright Copyright (c) 2012 NoStress Commerce (http://www.nostresscommerce.cz)
 *
 */

/**
 * Frontend kontroler pro exportni modul
 *
 * @category Nostress
 * @package Nostress_Nscexport
 *
 */

class Nostress_Nscexport_CheckController extends Mage_Core_Controller_Front_Action 
{
	protected function getMessages()
	{
		$catalogConfigUrl = Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit/section/catalog');
		$moduleConfigUrl = Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit/section/advanced');
		$indexUrl = Mage::helper("adminhtml")->getUrl('adminhtml/process/list');
		$compilationUrl = Mage::helper("adminhtml")->getUrl('adminhtml/compiler_process/index');
		$coreUrlHelpUrl = "https://docs.koongo.com/display/KoongoConnector/Slow+export+process+of+some+export+profiles";
		$flatHelpUrl = "https://docs.koongo.com/display/KoongoConnector/Installation#Installation-EnableFlatCatalog";
		$reindexHelpUrl = "https://docs.koongo.com/display/KoongoConnector/Installation#Installation-ReindexCatalogandCategoryIndex";
		
		$messages = array(	
			'php' => array(	0 => "<li>You need<strong> PHP 5.2.0</strong> (or greater)</li>",
						   	1 => "<li>You have<strong> PHP 5.2.0</strong> (or greater)</li>"	),
			'safe_mode' => array(	0 => '<li>Safe Mode is <strong>on</strong></li>',
									1 => '<li>Safe Mode is <strong>off</strong></li>'	),
			'mysql' => array(	0 => '<li>You need<strong> MySQL 4.1.20</strong> (or greater)</li>',
								1 => '<li>You have<strong> MySQL 4.1.20</strong> (or greater)</li>'	),
			'ext' => array(	0 => '<li> You are missing the <strong>{{ext}}</strong> extension</li>',
							1 => '<li>You have the <strong>{{ext}}</strong> extension</li>',
							"replace" => '{{ext}}'	),
			'xslt' => array(	0 => "<li> You are missing the <strong>XsltProcessor</strong> library. Please ask your hosting provider or server administrator to allow or install <a target='blank' href='http://php.net/manual/en/book.xsl.php'>XSLT PHP library</a>. Export module can't work without this extension.</li>",
								1 => '<li>You have the <strong>XsltProcessor</strong> library</li>'	),
			'memory' => array(	0 => '<li> Your memory limit is <strong>{{memory}}M </strong>, which is not enought. You need to setup Memory Limit value as 256M - this value is reguired by Magento by default. You do this in php.ini or .htaccess. Ask your hosting provider.</li>',
							1 => '<li>You have appropriate memory limit of <strong>{{memory}}M</strong></li>',
							"replace" => '{{memory}}'	),
			'time' => array(	0 => '<li> Your max execution time limit is <strong>{{time}} seconds</strong>, which is not enought. Recommended max execution time limit is 1800 seconds.</li>',
							1 => '<li>You have appropriate max execution time of <strong>{{time}} s</strong>.</li>',
							2 => '<li> Your max execution time limit is <strong>{{time}} seconds</strong>, which fulfill the minimum requirements. Recommended max execution time limit is 1800 seconds.</li>',
							"replace" => '{{time}}'	),
			'flat_product' => array(	0 => '<li>Catalog product flat is <strong>Disabled</strong>. Please enable catalog category flat in <a href="'.$catalogConfigUrl.'" target="_blank">configuration</a>. See more information in <a href="'.$flatHelpUrl.'" target="_blank">documentation</a>.</li>',
										1 => '<li>Catalog product flat is <strong>Enabled</strong>.</li>'),
			'flat_category' => array(	0 => '<li>Catalog category flat is <strong>Disabled</strong>. Please enable catalog category flat in <a href="'.$catalogConfigUrl.'" target="_blank">configuration</a>. See more information in <a href="'.$flatHelpUrl.'" target="_blank">documentation</a>.</li>',
										1 => '<li>Catalog category flat is <strong>Enabled</strong>.</li>'),
			'indexes' => array(	0 => '<li>One or more of the Indexes are <strong>not up to date</strong>: {{indexes}}. Click here to go to <a href="'.$indexUrl.'" target="_blank">Index Management</a> and rebuild required indexes. See more information in <a href="'.$reindexHelpUrl.'" target="_blank">documentation</a>.</li>',
								1 => '<li>All of the Indexes are <strong>up to date</strong>.</li>',
								"replace" => '{{indexes}}'	),
			'compiler' => array(	0 => '<li>Magento compiler is <strong>Enabled</strong>. We recommend to turn off the compiler during installation in <a href="'.$compilationUrl.'" target="_blank">Tools - Compilation</a>.</li>',
									1 => '<li>Magento compiler is <strong>Disabled</strong>, which is recommended state for module installation. </li>'),
			'moduleEnabled' => array(	0 => '<li>Module Koongo Connector is <strong>Disabled</strong>. Please enable Koongo Connector in <a href="'.$moduleConfigUrl.'" target="_blank">configuration</a>.',
										1 => '<li>Module Koongo Connector is <strong>Enabled</strong>.</li>'),
			'coreUrlRewrites' => array(	0 => '<li>Table Core Url Rewrite contains <strong>{{rows_count}} rows</strong>, which may slow down the export process. See more information in <a href="'.$coreUrlHelpUrl.'" target="_blank">documentation</a>.</li>',
										1 => '<li>Table Core Url Rewrite contains <strong>{{rows_count}} rows</strong>. Too heigh number of the rows may slow down export process. See more information in <a href="'.$coreUrlHelpUrl.'" target="_blank">documentation</a>.</li>',
										"replace" => '{{rows_count}}'),
			);
		return $messages;
	}
	
	public function indexAction() 
	{
		$checkResult = Mage::helper('nscexport/data_check')->run();
		$messagesServer = $this->convertToMessages($checkResult["server"]);		
		$messagesMagento = $this->convertToMessages($checkResult["magento"]);
		$messagesRec = $this->convertToMessages($checkResult["recommend"]);
		$this->renderServerResult($messagesServer);
		$this->renderMagentoResult($messagesMagento);
		$this->renderRecommendResult($messagesRec);
	}
	
	
	protected function renderServerResult($messages)
	{
		$m = "<h2>Server Settings</h2>";
		if(empty($messages[0]))	
		{
			$m .= '<p><strong>Congratulations!</strong> Your server meets the requirements for Koongo Connector.</p>';
			$m .= '<ul>'.implode("",array_values($messages[1])).'</ul>';
		}
		else
		{					
			$m .= '<p><strong>Your server does not meet the following requirements in order to install Koongo Connector.</strong>';
			$m .= '<br><br>The following requirements <u>failed</u>, please contact your hosting provider in order to receive assistance with meeting the system requirements for Koongo Connector:';
			$m .= '<ul>'.implode("",array_values($messages[0])).'</ul></p>';
			$m .= 'See more information about server setup for Koongo Connector at <a target="blank" href="https://docs.koongo.com/display/KoongoConnector/Server+Settings" > our pages </a></p>';
			$m .= 'The following requirements were <u>successfully met</u>:';
			$m .= '<ul>'.implode("",array_values($messages[1])).'</ul>';
		}
		echo $m;	
	}
	
	protected function renderMagentoResult($messages)
	{
		$m = "<h2>Magento Settings</h2>";
		if(empty($messages[0]))
		{
			$m .= '<p><strong>Congratulations!</strong> Your Magento eshop meets the requirements for Koongo Connector.</p>';
			$m .= '<ul>'.implode("",array_values($messages[1])).'</ul>';
		}
		else
		{
			$m .= '<p><strong>Your Magento does not meet the following requirements for successful run of Koongo Connector.</strong>';
			$m .= '<br><br>The following requirements <u>failed</u>:';
			$m .= '<ul>'.implode("",array_values($messages[0])).'</ul></p>';			
			$m .= 'The following requirements were <u>successfully met</u>:';
			$m .= '<ul>'.implode("",array_values($messages[1])).'</ul>';
		}
		echo $m;
	}
	
	protected function renderRecommendResult($messages)
	{
		$m = "<h2>Recommendations</h2>";
		$m .= '<ul>'.implode("",array_values($messages[1])).'</ul>';
		$m .= '<ul>'.implode("",array_values($messages[0])).'</ul>';
		echo $m;
	
	}
	
	protected function convertToMessages($checkResult)
	{
		$message = array(0 => array(), 1 => array());
		$itemMessages = $this->getMessages();
		foreach($checkResult as $index => $item)
		{
			$resultIndex = $item["result"];
			$mi = $resultIndex == 1?1:0;
			$ci = $index;
			if(empty($itemMessages[$index]))
				$index = "ext";
			
			$tmpMes = $itemMessages[$index][$resultIndex];			
			if(!empty($itemMessages[$index]["replace"]))
			{	
				$value = "";
				if(isset($item["value"]))
					$value = $item["value"];
				$tmpMes = str_replace($itemMessages[$index]["replace"], $value, $tmpMes);
			}
			$message[$mi][$ci] = $tmpMes;
		}
		return $message;
	}
}