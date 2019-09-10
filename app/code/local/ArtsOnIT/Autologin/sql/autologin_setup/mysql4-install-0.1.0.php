<?php

$installer = $this;

$installer->startSetup();

$this->addAttribute('customer', 'autologin_hash', array(
                        'type'          => 'varchar',
                        'visible'       => false,
                        'required'      => false,
						'unique'        => true,
                        'user_defined'      => false,
));
$this->addAttribute('customer', 'autologin_is_active', array(
                        'type'              => 'int',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Is Autologin Active',
                        'input'             => 'select',
                        'class'             => '',
                        'source'            => 'eav/entity_attribute_source_boolean',                       
                        'visible'           => true,
                        'required'          => true,
                        'user_defined'      => false,
                        'default'           => '1',
                        'unique'            => false,
));

Mage::helper('autologin')->bulkGenerate();

$installer->endSetup(); 