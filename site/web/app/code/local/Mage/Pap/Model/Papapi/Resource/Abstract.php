<?php

/*********************************************************************************
 * Copyright 2009 Priacta, Inc.
 * 
 * This software is provided free of charge, but you may NOT distribute any
 * derivative works or publicly redistribute the software in any form, in whole
 * or in part, without the express permission of the copyright holder.
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *********************************************************************************/

// Use these to assist in debugging API related errors
//function exceptions_error_handler($severity, $message, $filename, $lineno) {
//    echo "Caught an error in $filename, $lineno: $message";
//    die;
//}
//
//set_error_handler('exceptions_error_handler');

abstract class Mage_Pap_Model_Papapi_Resource_Abstract extends Mage_Core_Model_Resource_Abstract
{
    const CHECKSUM_KEY_NAME = 'Checksum';

    /**
     * Session cache for this resource model
     *
     * @var array
     */
    protected $_session;

    /**
     * Resource model name that contains entities
     *
     * @var string
     */
    protected $_resourceModel;

    /**
     * Main PAP API class name
     *
     * @var string
     */
    protected $_papClass;
    protected $_papGridClass;
    
    /**
     * Primary key field name
     *
     * @var string
     */
    protected $_idFieldName;

    protected $_papClassFields;

    /**
     * Unique keys field names
     *
     * could array(
     *   array('field' => 'db_field_name1', 'title' => 'Field 1 should be unique')
     *   array('field' => 'db_field_name2', 'title' => 'Field 2 should be unique')
     *   array(
     *      'field' => array('db_field_name3', 'db_field_name3'),
     *      'title' => 'Field 3 and Field 4 combination should be unique'
     *   )
     * )
     *
     * or string 'my_field_name' - will be autoconverted to
     *      array( array( 'field' => 'my_field_name', 'title' => 'my_field_name' ) )
     *
     * @var array
     */
    protected $_uniqueFields = null;
  
    protected function _init($papClass, $idFieldName)
    {
        Mage::getSingleton('pap/config')->RequirePapAPI();
        
        $this->_setPapClass($papClass, $idFieldName);
    }
    
    public function createObject()
    {
        $apiclass = $this->getPapClass();
        return new $apiclass($this->getSession()->getSession());
    }
    
    public function createGridObject()
    {
        $apigridclass = $this->getPapGridClass();
        return new $apigridclass($this->getSession()->getSession());
    }
    
    /**
     * Initialize session for this resource model
     *
     * @param string $model
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _setResource($model)
    {
        $this->_session = $this->getResourceSession();

        if (is_string($model))
        {
            $this->_resourceModel = $model;
        }
        return $this;
    }

    /**
     * Creates a connection to resource whenever needed
     *
     * @param string $name
     * @return mixed
     */
    public function getResourceSession()
    {
        if (isset($this->_session)) {
            return $this->_session;
        }
        $session = Mage::getSingleton("pap/apisession");
        $this->_session = $session;
        return $session;
    }

    
    /**
     * Set main entity API class name and primary key field name
     *
     * If field name is ommited {papClass}_id will be used
     *
     * @param string $papClass
     * @param string|null $idFieldName
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _setPapClass($papClass, $idFieldName=null)
    {
        $papClassArr = explode('/', $papClass);

        if (!empty($papClassArr[1])) {
            if (empty($this->_resourceModel)) {
                $this->_setResource($papClassArr[0]);
            }
            $this->_setPapClass($papClassArr[1], $idFieldName);
        } else {
            $this->_papClass = $papClass;
            $this->_papGridClass = $papClass;
            if (is_null($idFieldName)) {
                $idFieldName = $papClass.'_id';
            }
            $this->_idFieldName = $idFieldName;
        }

        return $this;
    }

    /**
     * Get primary key field name
     *
     * @return string
     */
    public function getIdFieldName()
    {
        if (empty($this->_idFieldName)) {
            Mage::throwException(Mage::helper('core')->__('Empty identifier field name'));
        }
        return $this->_idFieldName;
    }

    /**
     * Get main API class name
     *
     * @return string
     */
    public function getPapClass()
    {
        if (empty($this->_papClass)) {
            Mage::throwException(Mage::helper('core')->__('Empty API class name'));
        }
        return $this->getApiClass($this->_papClass);
    }

    /**
     * Get main API class name
     *
     * @return string
     */
    public function getPapGridClass()
    {
        if (empty($this->_papGridClass)) {
            Mage::throwException(Mage::helper('core')->__('Empty API class name'));
        }
        return $this->getApiGridClass($this->_papGridClass);
    }

    /**
     * Get API class name for the entity
     *
     * @param string $entityName
     */
    public function getApiClass($entityName)
    {
        if (strpos($entityName, '/')) {
            $this->_papClass = $this->getResourcePapClassName($entityName, 'papclass');
        } elseif (!empty($this->_resourceModel)) {
            $this->_papClass = $this->getResourcePapClassName(
                $this->_resourceModel.'/'.$entityName, 'papclass');
        } else {
            $this->_papClass = $entityName;
        }
        return $this->_papClass;
    }
    
    public function getApiGridClass($entityName)
    {
        if (strpos($entityName, '/')) {
            $this->_papGridClass = $this->getResourcePapClassName($entityName, 'papgridclass');
        } elseif (!empty($this->_resourceModel)) {
            $this->_papGridClass = $this->getResourcePapClassName(
                $this->_resourceModel.'/'.$entityName, 'papgridclass');
        } else {
            $this->_papGridClass = $entityName;
        }
        return $this->_papGridClass;
    }
    
    public function getResourcePapClassName($modelEntity, $type)
    {
        $arr = explode('/', $modelEntity);
        if (isset($arr[1])) {
            list($model, $entity) = $arr;
            //$resourceModel = (string)Mage::getConfig()->getNode('global/models/'.$model.'/resourceModel');
            $resourceModel = (string) Mage::getConfig()->getNode()->global->models->{$model}->resourceModel;
            $entityConfig = $this->getResourceEntity($resourceModel, $entity);
            if ($entityConfig) {
              if (is_object($entityConfig))
              {
                $papclassName = (string)$entityConfig->{$type};
              }
              else
              {
                $papclassName = (string)$entityConfig;
              }
            } else {
                Mage::throwException(Mage::helper('core')->__('Can\'t retrieve entity config: %s', $modelEntity));
            }
        } else {
            $papclassName = $modelEntity;
        }
        
        Mage::dispatchEvent('resource_get_papclassname', array('resource' => $this, 'model_entity' => $modelEntity, 'papclass_name' => $papclassName));

        return $papclassName;
    }
    
    public function getResourceEntity($model, $entity)
    {
      if (preg_match('~^Pap_Api_~', $entity))
      {
        // This is an API class, we don't need to look anything up
        return $entity;
      }
        //return Mage::getConfig()->getNode("global/models/$model/entities/$entity");
        return Mage::getConfig()->getNode()->global->models->{$model}->entities->{$entity};
    }
    
    protected function _getSession()
    {
        if (isset($this->_session)) {
            return $this->_session;
        }
        $this->_session = $this->getResourceSession();

        return $this->_session;
    }

    protected function getSession()
    {
        return $this->_getSession();
    }

    /**
     * Retrieve session for read data
     *
     * @return  Zend_Db_Adapter_Abstract
     */
    protected function _getReadAdapter()
    {
        return $this->_getSession();
    }

    /**
     * Retrieve session for write data
     *
     * @return  Zend_Db_Adapter_Abstract
     */
    protected function _getWriteAdapter()
    {
        return $this->_getSession();
    }

    /**
     * Temporary resolving collection compatibility
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getReadSession()
    {
        return $this->_getReadAdapter();
    }

    /**
     * Load an object
     *
     * @param   Mage_Core_Model_Abstract $object
     * @param   mixed $value
     * @param   string $field field to load by (defaults to model id)
     * @return  Mage_Core_Model_Mysql4_Abstract
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
        if (is_null($field)) {
            $field = $this->getIdFieldName();
        }
        
        // create the API object
        $apiobject = $this->createObject();

        // set only the provided field
        $apiobject->setField($field, $value);
        
        // Load the object using the API
        try {
          $apiobject->load();
        } catch (Exception $e) {
          // Ignore any exceptions we encounter
        } catch(Gpf_Exception $e) {
        // Ignore any exceptions we encounter
        }
        
        // Clone the data from the api object to the Varien object 
        $newfields = $apiobject->getFields();
        $data = array();
        foreach($newfields as $newfield)
        {
          $data[$newfield->get(Pap_Api_Object::FIELD_NAME)] = $newfield->get(Pap_Api_Object::FIELD_VALUE);
        }
        $object->setData($data);
        
        $this->_afterLoad($object);

        return $this;
    }

    /**
     * Save object object data
     *
     * @param   Mage_Core_Model_Abstract $object
     * @return  Mage_Core_Model_Mysql4_Abstract
     */
    public function save(Mage_Core_Model_Abstract $object)
    {
        if ($object->isDeleted()) {
            return $this->delete($object);
        }

        $this->_beforeSave($object);
        $this->_checkUnique($object);

        // create an API object for communication
        $apiobject = $this->createObject();
        
        if (!is_null($object->getId()))
        {
            // clone the data to the apiobject
            $data = $this->_prepareDataForSave($object);
            foreach($data as $k=>$v)
            {
              $apiobject->setField($k, $v);
            }
            
            // save using the API
            $apiobject->save();
        }
        else
        {
            // clone the data to the apiobject
            $data = $this->_prepareDataForSave($object);
            foreach($data as $k=>$v)
            {
              $apiobject->setField($k, $v);
            }
            
            // insert using the API
            $apiobject->add();
            
            // Clone the data from the api object to the Varien object 
            $newfields = $apiobject->getFields();
            $data = array();
            foreach($newfields as $newfield)
            {
              $data[$newfield->get(Pap_Api_Object::FIELD_NAME)] = $newfield->get(Pap_Api_Object::FIELD_VALUE);
            }
            $object->setData($data);
        }
        
        // TODO: What if there were errors? What do we do?
        // For the moment, we'll just log errors for debugging purposes.
        Mage::log($apiobject->getMessage());

        $this->_afterSave($object);

        return $this;
    }

    /**
     * Delete the object
     *
     * @param Varien_Object $object
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    public function delete(Mage_Core_Model_Abstract $object)
    {
        // Not currently supported by the API.
        return $this;
    }

    /**
     * Add unique field restriction
     *
     * @param   array|string $field
     * @return  Mage_Core_Model_Mysql4_Abstract
     */
    public function addUniqueField($field)
    {
        $this->_initUniqueFields();
        if(is_array($this->_uniqueFields) ) {
            $this->_uniqueFields[] = $field;
        }
        return $this;
    }

    /**
     * Reset unique fields restrictions
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    public function resetUniqueField()
    {
         $this->_uniqueFields = array();
         return $this;
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array();
        return $this;
    }

    /**
     * Get configuration of all unique fields
     *
     * @return array
     */
    public function getUniqueFields()
    {
        if (is_null($this->_uniqueFields)) {
            $this->_initUniqueFields();
        }
        return $this->_uniqueFields;
    }

    /**
     * Prepare data for save
     *
     * @param   Mage_Core_Model_Abstract $object
     * @return  array
     */
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object)
    {
        // nothing special at this level
        return $object->getData();
    }

    protected function _checkUnique(Mage_Core_Model_Abstract $object)
    {
        // Dummy. Does nothing.
        // TODO: perhaps someday this would be worth writing support for.
        return $this;
    }

    public function afterLoad(Mage_Core_Model_Abstract $object)
    {
        $this->_afterLoad($object);
    }

    /**
     * Perform actions after object load
     *
     * @param Varien_Object $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        return $this;
    }

    /**
     * Perform actions before object save
     *
     * @param Varien_Object $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        return $this;
    }

    /**
     * Perform actions after object save
     *
     * @param Varien_Object $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        return $this;
    }

    /**
     * Perform actions before object delete
     *
     * @param Varien_Object $object
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        return $this;
    }

    /**
     * Perform actions after object delete
     *
     * @param Varien_Object $object
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        return $this;
    }

    public function beginTransaction()
    {
        // Dummy. Does nothing.
        return $this;
    }

    public function commit()
    {
        // Dummy. Does nothing.
        return $this;
    }

    public function rollBack()
    {
        // Dummy. Does nothing.
        return $this;
    }

}

?>