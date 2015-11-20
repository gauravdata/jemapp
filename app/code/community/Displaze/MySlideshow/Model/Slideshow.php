<?php
/*
 * Slide.php
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

class Displaze_MySlideshow_Model_Slideshow extends Mage_Core_Model_Abstract
{
    
    /**
     * Slide's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    
    
    protected $_path = '';
    
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('myslideshow/slideshow');
    }
    
    /**
     * Save Slideshow image. 
     */
    public function saveImage()
    {
        if(isset($_FILES['image']['name']) && (file_exists($_FILES['image']['tmp_name']))) {
            
            try {
                
                $uploader = new Varien_File_Uploader('image');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png', 'bmp'));
                
                $uploader->setAllowRenameFiles(true);
                //$path = Mage::getBaseDir('media') . DS ;// 'displaze' . DS . 'myslideshow' . DS;
                $path = $this->_initFolderStructure();
                       
                $uploader->save($path, $_FILES['image']['name']);
                
                $this->setData('image', $_FILES['image']['name']);
                
                
            } catch(Exception $e) {
                
            }
        }
        
        return $this;
    }
    
    protected function _initFolderStructure()
    {
        $path = Mage::getBaseDir('media') . DS . 'displaze' . DS;
        if(!is_dir($path)) {
            mkdir($path, 0755);
        }
        
        $path = $path . 'myslideshow' . DS;
        if(!is_dir($path)) {
            mkdir($path, 0755);
        }
        
        return $this->_path = $path;
        
    }
    
    public function save() 
    {
        $deleteImage = Mage::app()->getRequest()->getPost('delete_image');
        if($deleteImage) {
            
        }
        
        $this->saveImage();
        parent::save();
        
        return $this;
    }
    
    
    /**
     * Prepare slide's statuses.
     * Available event myslideshow_slide_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        $statuses = new Varien_Object(array(
            self::STATUS_ENABLED => Mage::helper('myslideshow')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('myslideshow')->__('Disabled'),
        ));

        Mage::dispatchEvent('myslideshow_slide_get_available_statuses', array('statuses' => $statuses));

        return $statuses->getData();
    }
}