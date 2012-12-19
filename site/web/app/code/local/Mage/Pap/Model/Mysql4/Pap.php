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

class Mage_Pap_Model_Mysql4_Pap extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // grabbing the singleton will ensure that the connection settings
        // are available for access when the connection is established
        $config = Mage::getSingleton('pap/config');
        
        $this->_init('pap/pap', 'transid');
    }

    // the base getTable() may tack on an unwanted prefix. Kill it here.
    public function getTable($name)
    {
        $ret = parent::getTable($name);
        
        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();
        $ret = preg_replace('~^'.preg_quote($tablePrefix).'~', '', $ret);

        return $ret;
    }
    
    public function SetOrderStatus($order, $status)
    {
      $select = $this->_getReadAdapter()->select()->from($this->getTable('pap/pap'))
                            ->where("orderid like CONCAT(? ,'%')", $order->getIncrementId());
      $orders = $this->_getReadAdapter()->fetchAll($select);
      foreach ($orders as $order)
      {
        $this->internal_SetOrderStatus($order, $status);
      }
    }
    
    protected function internal_SetOrderStatus($order, $status)
    {
      if ($status == $order['rstatus'])
      {
        return; // nothing to change
      }
      
      $data = array(
          'dateapproved' => $order['dateapproved'] ? $order['dateapproved'] : now(),
          'rstatus'  => $status
      );
      $condition = $this->_getWriteAdapter()->quoteInto('transid=?', $order['transid']);
      $this->_getWriteAdapter()->update($this->getTable('pap/pap'), $data, $condition);
//      $condition = $this->_getWriteAdapter()->quoteInto('transid=?', $order['transid']);
//      $this->_getWriteAdapter()->update($this->getTable('pap/pap'), $data, $condition);
    }
}
?>