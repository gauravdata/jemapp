<?php
/* 
 * SlideshowController.php
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
class Displaze_MySlideshow_Adminhtml_SlideshowController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct()
    {
        $this->setUsedModuleName('Displaze_MySlideshow');
    }
    
    protected function _initAction()
    {
        $this->loadLayout()
         ->_setActiveMenu('displaze')
         ->_addBreadcrumb(Mage::helper('myslideshow')->__('Slideshow'), Mage::helper('myslideshow')->__('Slideshow'))
         ->_addBreadcrumb(Mage::helper('myslideshow')->__('Manage Slideshow'), Mage::helper('myslideshow')->__('Manage Slideshow'));
         return $this;
    }
    
    
    public function indexAction() 
    {
        $this->_initAction();
       // $this->_addContent($this->getLayout()->createBlock('myslideshow/adminhtml_slideshow_grid'));
        $this->renderLayout();
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function editAction()
    {
        $this->_title($this->__('Displaze Slideshow'))
             ->_title($this->__('Slideshow'))
             ->_title($this->__('Manage Slideshow'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('myslideshow/slideshow');
        
        // 2. Initial checking
        if ($id) {
            $model->load($id); 
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('myslideshow')->__('This slide no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        
        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Slide'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }
        
        // 4. Register model to use later in blocks
        Mage::register('myslideshow_slideshow', $model);
        
        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('myslideshow')->__('Edit Slide')
                    : Mage::helper('myslideshow')->__('New Slide'),
                $id ? Mage::helper('myslideshow')->__('Edit Slide')
                    : Mage::helper('myslideshow')->__('New Slide'));

        $this->renderLayout();
    }
    
    /**
     * save slideshow
     */
    public function saveAction()
    {
       // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterPostData($data);
            //init model and set data
            $model = Mage::getModel('myslideshow/slideshow');

            if ($id = $this->getRequest()->getParam('id')) {
                $model->load($id);
            }

            $model->setData($data);

            Mage::dispatchEvent('myslideshow_slideshow_prepare_save', array('slideshow' => $model, 'request' => $this->getRequest()));

            //validating
            //if (!$this->_validatePostData($data)) {
            //    $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current' => true));
            //    return;
            //}

            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('myslideshow')->__('The slide has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current'=>true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('myslideshow')->__('An error occurred while saving the slide.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
        $this->_redirect('*/*/');
    }
    
    
    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('activation_time_from', 'activation_time_to'));
        return $data;
    }
    
    
    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('myslideshow/slideshow');
                $model->load($id);
                $title = $model->getTitle();
                $image = $model->getImage();
                $this->_deleteImage($image);
                $model->delete(); 
                
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('myslideshow')->__('The slide has been deleted.'));
                // go to grid
                Mage::dispatchEvent('displaze_adminhtml_slide_on_delete', array('title' => $title, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::dispatchEvent('displaze_adminhtml_slide_on_delete', array('title' => $title, 'status' => 'fail'));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('myslideshow')->__('Unable to find a slide to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }
    
    protected function _deleteImage($image)
    {
        $filename = Mage::helper('myslideshow')->getSlideshowPath() . $image;
        $file = new Varien_Io_File();
        $file->rm($filename);
    }
}