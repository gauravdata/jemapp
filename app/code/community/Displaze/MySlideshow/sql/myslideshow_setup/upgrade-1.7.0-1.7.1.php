<?php

/* 
 * upgrade-1.7.0-1.7.1.php
 * 
 * Copyright (c) 2012 Aftab Naveed <aftabnaveed@gmail.com>. 
 * 
 * This file is part of Displaze Web Services Inc..
 * 
 * Displaze Web Services Inc. is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Displaze Web Services Inc. is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Displaze Web Services Inc..  If not, see <http ://www.gnu.org/licenses/>.
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$table = $installer->getConnection();

$table->addColumn($installer->getTable('myslideshow/slideshow'), 'url', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => '255',
    'position'  => '2',
    'comment'   => 'Slide Url'
));