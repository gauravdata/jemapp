<?php

/* 
 * Tabs.php
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


class Displaze_MySlideshow_Block_Adminhtml_Slideshow_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('myslideshow_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('myslideshow')->__('Slide'));
    }

    /**
     * if you would want to create the tabs using code uncomment the following, else
     * it has to be created using xml file which is the proper and neat method of doing it
   
    protected function _beforeToHtml()
    {
       $this->addTab('main_section', array(
            'label' => Mage::helper('mylink')->__('Link Information'),
            'title' => Mage::helper('mylink')->__('Link Information'),
            'content' => $this->getLayout()->createBlock('mylink/adminhtml_link_edit_tab_main')->toHtml(),
        ));

       $this->addTab('meta_section', array(
            'label' => Mage::helper('mylink')->__('Meta Data'),
            'title' => Mage::helper('mylink')->__('Meta Data'),
            'content' => $this->getLayout()->createBlock('mylink/adminhtml_link_edit_tab_meta')->toHtml(),
        ));

        parent::_beforeToHtml();
    }*/
}