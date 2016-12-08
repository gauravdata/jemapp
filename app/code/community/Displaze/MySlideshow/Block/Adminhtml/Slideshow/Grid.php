<?php

/*
 * Grid.php
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

class Displaze_MySlideshow_Block_Adminhtml_Slideshow_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{

    public function __construct() 
    {
        parent::__construct();
        $this->setId('displazeSlideshowGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('myslideshow/slideshow')->getCollection();
        
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        
        $this->addColumn('id', array(
            'header'    => Mage::helper('myslideshow')->__('ID'),
            'align'     => 'left',
            'width'     => '10px',
            'index'     => 'id',
        ));
        
        $this->addColumn('image', array(
            'header'    => Mage::helper('myslideshow')->__('Slide'),
            'align'     => 'center',
            'index'     => 'image',
            'width'     => '100px',
            'renderer'  => 'myslideshow/adminhtml_slideshow_image_renderer'
        ));
         
        $this->addColumn('title', array(
            'header'    => Mage::helper('myslideshow')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));
        
        $this->addColumn('url', array(
            'header'    => Mage::helper('myslideshow')->__('Url'),
            'align'     => 'left',
            'index'     => 'url',
            'width'     => '200px',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('myslideshow')->__('Status'),
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('myslideshow/slideshow')->getAvailableStatuses()
        ));

        $this->addColumn('creation_time', array(
            'header'    => Mage::helper('myslideshow')->__('Date Created'),
            'index'     => 'creation_time',
            'type'      => 'datetime',
        ));

        $this->addColumn('update_time', array(
            'header'    => Mage::helper('myslideshow')->__('Last Modified'),
            'index'     => 'update_time',
            'type'      => 'datetime',
        ));

        

        return parent::_prepareColumns();
    }
    
    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    

}