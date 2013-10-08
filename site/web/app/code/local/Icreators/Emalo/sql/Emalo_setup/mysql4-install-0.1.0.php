<?php

 $installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
 
$setup->addAttribute('catalog_category', 'emalo_cat', array(
    'group'         => 'General',
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Catalog ID',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
 
$installer->endSetup();


//create directory structure if it doesnt exist
$dir  = Mage::getBaseDir().DS.'var'.DS.'emalo'.DS;
$import_dir = $dir.DS.'import'.DS;
$export_dir = $dir.DS.'export'.DS;
$image_dir	 = Mage::getBaseDir().DS.'media'.DS.'catalog'. DS .'category'.DS;

$_fs = array($import_dir,$export_dir,$image_dir);
foreach ($_fs as $_dir) 
{
	if (!file_exists($_dir)) 
	{
		if (!mkdir($_dir,0777,true)) 
		{
	    	throw new Exception ('failed to create dir, check your permission in var');
	  	}
	}
}