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

abstract class Mage_Pap_Model_Papapi_Collection_Abstract extends Varien_Data_Collection
{
    /**
     * Model name
     *
     * @var string
     */
    protected $_model;

    /**
     * Resource model name
     *
     * @var string
     */
    protected $_resourceModel;

    /**
     * Resource instance
     *
     * @var Mage_Core_Model_Mysql4_Abstract
     */
    protected $_resource;
    
    /**
     * All collection data array
     * Used for getData method
     *
     * @var array
     */
    protected $_data = null;

    /**
     * Fields map for corellation names & real selected fields
     *
     * @var array
     */
    protected $_map = null;

    protected $_grid = null;
    
    /**
     * Collection constructor
     *
     * @param Mage_Core_Model_Mysql4_Abstract $resource
     */
    public function __construct($resource=null)
    {
        Mage::getSingleton('pap/config')->RequirePapAPI();

        parent::__construct();
        $this->_construct();
        $this->_resource = $resource;
        // create an API object for communication
        $this->_grid = $this->getResource()->createGridObject();
    }

    /**
     * Initialization here
     *
     */
    protected function _construct()
    {

    }

    /**
     * Standard resource collection initalization
     *
     * @param string $model
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    protected function _init($model, $resourceModel=null)
    {
        $this->setModel($model);
        if (is_null($resourceModel)) {
            $resourceModel = $model;
        }
        $this->setResourceModel($resourceModel);
        return $this;
    }

    /**
     * Set model name for collection items
     *
     * @param string $model
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function setModel($model)
    {
        if (is_string($model)) {
            $this->_model = $model;
            $this->setItemObjectClass(Mage::getConfig()->getModelClassName($model));
        }
        return $this;
    }

    /**
     * Get model instance
     *
     * @param array $args
     * @return Varien_Object
     */
    public function getModelName($args=array())
    {
        return $this->_model;
    }

    public function setResourceModel($model)
    {
        $this->_resourceModel = $model;
    }

    public function getResourceModelName()
    {
        return $this->_resourceModel;
    }

    /**
     * Get resource instance
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    public function getResource()
    {
        if (empty($this->_resource)) {
            $this->_resource = Mage::getResourceModel($this->getResourceModelName());
        }
        return $this->_resource;
    }

    /**
     * Retrive all ids for collection
     *
     * @return array
     */
    public function getAllIds()
    {
      // create an API object for communication
      $request = $this->getResource()->createGridObject();
      
      // request all rows
      $request->sendNow();
      $recordset = $request->getGrid()->getRecordset();

      // grab the id for every row in the record set
      $ret = array();
      $fieldname = $this->getResource()->getIdFieldName();
      foreach($recordset as $record)
      {
        $ret[] = $record->get($fieldname);
      }
      
      return $ret;
    }

    /**
     * Load data
     *
     * @return  Varien_Data_Collection_Db
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        
        Mage::dispatchEvent('core_collection_abstract_load_before', array('collection' => $this));
        
        $this->_renderFilters()
             ->_renderOrders()
             ->_renderLimit();

        $data = $this->getData();
        $this->resetData();

        if (is_array($data)) {
            foreach ($data as $row) {
                $item = $this->getNewEmptyItem();
                if ($this->getIdFieldName()) {
                    $item->setIdFieldName($this->getIdFieldName());
                }
                $item->addData($row);
                $this->addItem($item);
            }
        }

        $this->_setIsLoaded();
        $this->_afterLoad();
        return $this;
    }

    /**
     * Redeclare after load method for specifying collection items original data
     *
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $item->setOrigData();
        }
        Mage::dispatchEvent('core_collection_abstract_load_after', array('collection' => $this));
        return $this;
    }

    /**
     * Save all the entities in the collection
     *
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function save()
    {
        foreach ($this->getItems() as $item) {
            $item->save();
        }
        return $this;
    }

    protected function _canUseCache()
    {
        return Mage::app()->useCache('collections');
    }

    /**
     * Redeclared for processing cache tags throw application object
     *
     * @return array
     */
    protected function _getCacheTags()
    {
        $tags = array();
        if (isset($this->_cacheConf['tags'])) {
            $tags = $this->_cacheConf['tags'];
        }
        
        foreach ($tags as $key => $value) {
            $tags[$key] = Mage::app()->prepareCacheId($value);
        }
        $tags[] = Mage_Core_Model_App::CACHE_TAG;
        return $tags;
    }

    public function initCache($object, $idPrefix, $tags)
    {
        $this->_cacheConf = array(
            'object'    => $object,
            'prefix'    => $idPrefix,
            'tags'      => $tags
        );
        return $this;
    }

    protected function _setIdFieldName($fieldName)
    {
        $this->_idFieldName = $fieldName;
        return $this;
    }

    public function getIdFieldName()
    {
        return $this->_idFieldName;
    }

    protected function _getItemId(Varien_Object $item)
    {
        if ($field = $this->getIdFieldName()) {
            return $item->getData($field);
        }
        return parent::_getItemId($item);
    }

    /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        // TODO: This is a rotten way to get the size. It would be much better to get it
        // using something like unto count(*). It appears that the API may be able to
        // execute arbitrary SQL (yeah, scary) so this MIGHT be possible somehow. 
        $this->_grid->sendNow();
        $grid = $this->_grid->getGrid();
        return $grid->getTotalCount();
    }

    /**
     * Add select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  Varien_Data_Collection_Db
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->_grid->setSorting($field, $direction != self::SORT_ORDER_DESC);
        return $this;
    }

    /**
     * self::setOrder() alias
     *
     * @param string $field
     * @param string $direction
     * @return Varien_Data_Collection_Db
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        return $this->_setOrder($field, $direction);
    }

    /**
     * Add select order to the beginning
     *
     * @param string $field
     * @param string $direction
     * @return Varien_Data_Collection_Db
     */
    public function unshiftOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        return $this->_setOrder($field, $direction, true);
    }

    /**
     * Add ORDERBY to the end or to the beginning
     *
     * @param string $field
     * @param string $direction
     * @param bool $unshift
     * @return Varien_Data_Collection_Db
     */
    private function _setOrder($field, $direction, $unshift = false)
    {
        // the API only supports single sort, so this is just an alias
        return $this->setOrder($field, $direction = self::SORT_ORDER_DESC);
    }

    /**
     * Add field filter to collection
     *
     * If $attribute is an array will add OR condition with following format:
     * array(
     *     array('attribute'=>'firstname', 'like'=>'test%'),
     *     array('attribute'=>'lastname', 'like'=>'test%'),
     * )
     *
     * @see self::_getConditionSql for $condition
     * @param string|array $attribute
     * @param null|string|array $condition
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition=null)
    {
        $field = $this->_getMappedField($field);
        $condition = $this->_getConditionArray($field, $condition);
        foreach ($condition as $c)
        {
          $this->_grid->addFilter($field, $c['op'], $c['val']);
        }
        return $this;
    }

    /**
     * Try to get mapped field name for filter to collection
     *
     * @param string
     * @return string
     */
    protected function _getMappedField($field)
    {
        $mappedFiled = $field;

        $mapper = $this->_getMapper();

        if (isset($mapper['fields'][$field])) {
            $mappedFiled = $mapper['fields'][$field];
        }

        return $mappedFiled;
    }

    protected function _getMapper()
    {
        if (isset($this->_map)) {
            return $this->_map;
        }
        else {
            return false;
        }
    }

    /**
     * Build array for condition
     *
     * If $condition integer or string - exact value will be filtered
     *
     * If $condition is array is - one of the following structures is expected:
     * - array("from"=>$fromValue, "to"=>$toValue)
     * - array("like"=>$likeValue)
     * - array("neq"=>$notEqualValue)
     * - array("in"=>array($inValues))
     * - array("nin"=>array($notInValues))
     *
     * @param string $fieldName
     * @param integer|string|array $condition
     * @return array( array('op'=>?, 'val'=>?) , ...)
     */
    protected function _getConditionArray($fieldName, $condition)
    {
        $ret = array();
        $fieldName = $this->_getConditionFieldName($fieldName);
        if (is_array($condition) && isset($condition['field_expr'])) {
            $fieldName = str_replace('#?', $this->quoteIdentifier($fieldName), $condition['field_expr']);
        }
        if (is_array($condition)) {
            if (isset($condition['from']) || isset($condition['to'])) {
                if (isset($condition['from'])) {
                    $op = "=>"; // TODO: Does this even work?
                    if (empty($condition['date'])) {
                        if ( empty($condition['datetime'])) {
                            $from = $condition['from'];
                        }
                        else {
                            $from = $this->convertDateTime($condition['from']);
                            $op = Gpf_Data_Filter::TIME_EQUALS_GREATER;
                        }
                    }
                    else {
                        $from = $this->convertDate($condition['from']);
                        $op = Gpf_Data_Filter::DATE_EQUALS_GREATER;
                    }
                    $ret[] = array('op'=>$op, 'val'=>$from);
                }
                if (isset($condition['to'])) {
                    $op = "=<"; // TODO: Does this even work?
                    if (empty($condition['date'])) {
                        if ( empty($condition['datetime'])) {
                            $to = $condition['to'];
                        }
                        else {
                            $to = $this->convertDateTime($condition['to']);
                            $op = Gpf_Data_Filter::TIME_EQUALS_LOWER;
                        }
                    }
                    else {
                        $to = $this->convertDate($condition['to']);
                        $op = Gpf_Data_Filter::DATE_EQUALS_LOWER;
                    }

                    $ret[] = array('op'=>$op, 'val'=>$to);
                }
            }
            elseif (isset($condition['eq'])) {
              $ret[] = array('op'=>Gpf_Data_Filter::EQUALS, 'val'=>$condition['eq']);
            }
            elseif (isset($condition['neq'])) {
              $ret[] = array('op'=>Gpf_Data_Filter::NOT_EQUALS, 'val'=>$condition['neq']);
            }
            elseif (isset($condition['like'])) {
              $ret[] = array('op'=>Gpf_Data_Filter::LIKE, 'val'=>$condition['like']);
            }
            elseif (isset($condition['nlike'])) {
              $ret[] = array('op'=>Gpf_Data_Filter::NOT_LIKE, 'val'=>$condition['nlike']);
            }
            elseif (isset($condition['in'])) {
              $ret[] = array('op'=>"IN", 'val'=>$condition['in']); // TODO: Will this work?
            }
            elseif (isset($condition['nin'])) {
              $ret[] = array('op'=>"NOT IN", 'val'=>$condition['nin']); // TODO: Will this work?
            }
            elseif (isset($condition['is'])) {
              $ret[] = array('op'=>"IS", 'val'=>$condition['is']); // TODO: Will this work?
            }
            elseif (isset($condition['notnull'])) {
              $ret[] = array('op'=>"IS NOT NULL", 'val'=>$condition['notnull']); // TODO: Will this work?
            }
            elseif (isset($condition['null'])) {
              $ret[] = array('op'=>"IS NULL", 'val'=>$condition['null']); // TODO: Will this work?
            }
            elseif (isset($condition['moreq'])) {
              $ret[] = array('op'=>">=", 'val'=>$condition['moreq']); // TODO: Will this work?
            }
            elseif (isset($condition['gt'])) {
              $ret[] = array('op'=>">", 'val'=>$condition['gt']); // TODO: Will this work?
            }
            elseif (isset($condition['lt'])) {
              $ret[] = array('op'=>"<", 'val'=>$condition['lt']); // TODO: Will this work?
            }
            elseif (isset($condition['gteq'])) {
              $ret[] = array('op'=>">=", 'val'=>$condition['gteq']); // TODO: Will this work?
            }
            elseif (isset($condition['lteq'])) {
              $ret[] = array('op'=>"<=", 'val'=>$condition['lteq']); // TODO: Will this work?
            }
            elseif (isset($condition['finset'])) {
              // unsupported
            }
            else {
              // unsupported
            }
        } else {
          $ret[] = array('op'=>Gpf_Data_Filter::EQUALS, 'val'=>(string)$condition);
        }
        return $ret;
    }
    
    protected function quoteIdentifier($fieldName)
    {
      // identifiers shouldn't need quoting for the API
      return $fieldName;
    }
    
    protected function convertDateTime($time)
    {
      if (is_object($time))
      {
        return $date->toString("YYYY-MM-dd HH:mm:ss");
      }
      return $time;
    }
    
    protected function convertDate($date)
    {
      if (is_object($date))
      {
        return $date->toString("YYYY-MM-dd");
      }
      return $date;
    }
    
    protected function _getConditionFieldName($fieldName)
    {
        return $fieldName;
    }

    /**
     * Render sql select orders
     *
     * @return  Varien_Data_Collection_Db
     */
    protected function _renderOrders()
    {
      // since multiple orders aren't allowed, there is nothing left to do here.
      return $this;
    }

    /**
     * Render sql select limit
     *
     * @return  Varien_Data_Collection_Db
     */
    protected function _renderLimit()
    {
        if($this->_pageSize)
        {
          $start = ($this->getCurPage() - 1) * $this->_pageSize;
          $this->_grid->setLimit($start, $start + $this->_pageSize);
        }
        
        return $this;
    }

    /**
     * Get all data array for collection
     *
     * @return array
     */
    public function getData()
    {
        if ($this->_data === null) {
            $this->_renderFilters()
                 ->_renderOrders()
                 ->_renderLimit();
            $this->_data = $this->_fetchAll();
            $this->_afterLoadData();
        }
        return $this->_data;
    }

    /**
     * Proces loaded collection data
     *
     * @return Varien_Data_Collection_Db
     */
    protected function _afterLoadData()
    {
        return $this;
    }

    /**
     * Reset loaded for collection data array
     *
     * @return Varien_Data_Collection_Db
     */
    public function resetData()
    {
        $this->_data = null;
        return $this;
    }

    public function loadData($printQuery = false, $logQuery = false)
    {
        return $this->load($printQuery, $logQuery);
    }

    /**
     * Reset collection
     *
     * @return Varien_Data_Collection_Db
     */
    protected function _reset()
    {
        $this->_grid = $this->getResource()->createGridObject();
        $this->_setIsLoaded(false);
        $this->_items = array();
        $this->_data = null;
        return $this;
    }

    /**
     * Fetch collection data
     *
     * @param   Zend_Db_Select $select
     * @return  array
     */
    protected function _fetchAll()
    {
      $this->_grid->sendNow();
      $recordset = $this->_grid->getGrid()->getRecordset();

      $data = array();
      foreach($recordset as $record)
      {
        $data[] = $record->getAttributes();
      }
      return $data;
    }

    protected function _getSelectCacheId($select)
    {
        // this ugly hack will get us an md5 hash based on
        // the contents of the grid request object.
        ob_start(); print_r($this->_grid);
        $id = md5(ob_get_contents());
        ob_end_clean();
        
        if (isset($this->_cacheConf['prefix'])) {
            $id = $this->_cacheConf['prefix'].'_'.$id;
        }
        return $id;
    }

    /**
     * Retrieve cache instance
     *
     * @return Zend_Cache_Core
     */
    protected function _getCacheInstance()
    {
        if (isset($this->_cacheConf['object'])) {
            return $this->_cacheConf['object'];
        }
        return false;
    }
}

?>