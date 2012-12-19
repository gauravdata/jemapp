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

class Mage_Pap_Model_Papapi_Pap extends Mage_Pap_Model_Papapi_Resource_Abstract
{
    public function _construct()
    {
        // grabbing the singleton will ensure that the connection settings
        // are available for access when the connection is established
        $config = Mage::getSingleton('pap/config');
        
        $this->_init('pap/pap', 'transid');
    }

    public function SetOrderStatus($order, $status)
    {
      // create an API object for communication
      $request = $this->createGridObject();
      
      // get a list of transactions for this order
      // we have TWO filters for orderid, because somewhere along the way, someone decided to
      // use t_orderid rather than orderid, but only in the grid. Go figure.
      $request->addFilter("orderid", Gpf_Data_Filter::LIKE, $order->getIncrementId()."%");
      $request->addFilter("t_orderid", Gpf_Data_Filter::LIKE, $order->getIncrementId()."%");
      $request->sendNow();
      
      $grid = $request->getGrid();
      $recordset = $grid->getRecordset();
      
      foreach($recordset as $record)
      {
        $data = $record->getAttributes();
        
        if ($status == $data['rstatus'])
        {
          continue; // nothing to change
        }

        // load the transaction
        // NOTE: We used to just copy the data to an empty transaction, but the
        // API wasn't reliable about returning all fields in the grid
        $transaction = Mage::getModel("pap/pap")->load($data['transid']);

        if (substr_compare($transaction->getOrderid(), $order->getIncrementId(), 0, strlen($order->getIncrementId())) != 0)
        {
          // Shouldn't happen, but the API changes enough that it might, (it has before)
          // and we don't want to corrupt everything if it does, so we test.
          // Skip this record.
          continue;
        }
        
        // set the date approved and the status
        $transaction->setDateapproved($order['dateapproved'] ? $order['dateapproved'] : now());
        $transaction->setRstatus($status);

        // write the transaction
        $transaction->save();
      }
    }
    
//    protected function internal_SetOrderStatus($order, $status)
//    {
//      if ($status == $order['rstatus'])
//      {
//        return; // nothing to change
//      }
//      
//      $data = array(
//          'dateapproved' => $order['dateapproved'] ? $order['dateapproved'] : now(),
//          'rstatus'  => $status
//      );
//      $condition = $this->_getWriteAdapter()->quoteInto('transid=?', $order['transid']);
//      $this->_getWriteAdapter()->update($this->getTable('pap/pap'), $data, $condition);
////      $condition = $this->_getWriteAdapter()->quoteInto('transid=?', $order['transid']);
////      $this->_getWriteAdapter()->update($this->getTable('pap/pap'), $data, $condition);
//    }
}
?>