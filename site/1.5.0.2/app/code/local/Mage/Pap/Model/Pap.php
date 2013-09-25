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

class Mage_Pap_Model_Pap extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'pap';
    protected $_eventObject = 'pap';
    protected $_errors    = array();
    
    // creates training credits from order information 
    public function connectAffiliateToOrder($affailiateemail, $orderid, $realid = null)
    {
      $config = Mage::getSingleton('pap/config');
      if (!$config->IsConfigured())
      {
        throw new Exception('Magento is not properly configured to use this feature of the Post Affiliate Pro Connector.');
        return;
      }
      
      $order = Mage::getModel('sales/order')->load($orderid);
      if (is_null($realid))
      {
        // auto detect by trying both
        $order->load($orderid);
        if (!$order->getId())
        {
          $order->loadByIncrementId($orderid);
        }
      }
      else if ($realid)
      {
        $order->load($orderid);
      }
      else
      {
        $order->loadByIncrementId($orderid);
      }
      if (!$order->getId())
      {
        throw new Exception('No order found with this id');
      }
      
      // lookup the affiliate by email address
      $papUserModel = Mage::getModel('pap/user');
      $papUserModel->loadByEmail($affailiateemail);
      $refid = $papUserModel->getRefid();
      if (is_null($refid) || !$refid || $refid == '')
      {
        throw new Exception('No affiliate with this email address');
//        $this->addError('No affiliate with this email address');
        return;
      }

//      $includefile = $config->getLocalPath().'/api/PapApi.class.php';
//      if (!file_exists($includefile))
//      {
//        throw new Exception("the file ".$includefile." does not exist. This usually means that the Document Root setting is incorrect. Normally this should be left blank.");
//        return;
//      }
      $config->RequirePapAPI();
      
      $this->registerOrder($order, $refid);
    }
    
    public function registerOrderByID($orderid, $realid = true)
    {
      $order = Mage::getModel('sales/order')->load($orderid);
      if ($realid)
      {
        $order->load($orderid);
      }
      else
      {
        $order->loadByIncrementId($orderid);
      }
      
      $this->registerOrder($order);
    }
    
    public function registerOrder($order, $refid = null)
    {
      $items = $this->getOrderSaleDetails($order, $refid);
      
      $this->registerSaleDetails($items);
    }
    
    public function registerSaleDetails($items, $cookievalue = null)
    {
      $config = Mage::getSingleton('pap/config');
      
      error_reporting(E_ALL & ~E_NOTICE); // drop the error level just a notch because the included code can't handle it

//      $includefile = $config->getLocalPath().'/api/PapApi.class.php';
//      if (!file_exists($includefile))
//      {
//        // We can't recover from this, but showing an error is not an option either.
//        Mage::Log("Failed to track affiliate order because the file ".$includefile." does not exist");
//        return; // fail silently
//      }
//      
////      require_once($_SERVER['DOCUMENT_ROOT'].'/affiliate/api/PapApi.class.php');
//      require_once($includefile);
      $config->RequirePapAPI();
      
      // create a sale tracker
      $saleTracker = new Pap_Api_SaleTracker($config->getRemotePath().'/scripts/sale.php');
//      $saleTracker = new Pap_Api_SaleTracker('www.priacta.com/affiliate/scripts/sale.php');
      $sales = array();

      foreach($items as $idx=>$item)
      {
        $sale = $saleTracker->createSale();
        $sale->setTotalCost($item['totalcost']);
        $sale->setOrderID($item['orderid']);
        if ($item['channelid']) { $sale->setChannelID($item['channelid']); }
        if ($item['data1']) { $sale->setData1($item['data1']); }
        if ($item['data2']) { $sale->setData2($item['data2']); }
        if ($item['data3']) { $sale->setData3($item['data3']); }
        if ($item['data4']) { $sale->setData4($item['data4']); }
        if ($item['data5']) { $sale->setData5($item['data5']); }
        $sale->setProductID($item['productid']);
        if (method_exists($sale, 'setCouponCode'))
        {
          if ($item['couponcode']) { $sale->setCouponCode($item['couponcode']); }
        }
        if ($item['affiliateid']) { $sale->setAffiliateID($item['affiliateid']); }
        $sales[] = $sale;
      }
      
      // if provided, drop in the cookie value
      if (isset($cookievalue) && !is_null($cookievalue) && $cookievalue)
      {
        $saleTracker->setCookieValue($cookievalue);
      }
      
      $saleTracker->register();
    }

    public function getOrderSaleDetails($order, $refid = null)
    {
      $config = Mage::getSingleton('pap/config');
      
      // Check the sales rules. If any of the sales rules have a
      // coupon code, we'll want to send that code with the order
      $ruleIds = explode(',', $order->getAppliedRuleIds());
      $ruleIds = array_unique($ruleIds);

      $quote = Mage::getModel('sales/quote')->load($order->getQuoteId());
      $couponcode = $quote->getCouponCode();
      
      $sales = array();

      if ($config->getPerProduct())
      {
        // Count the keys we need to deliver
  //      $items = $order->getAllItems();
        $items = $order->getAllVisibleItems(); // get only top level items
        foreach($items as $idx=>$item)
        {
          // lookup the product
          $productid = $item->getProductId();
          $product = Mage::getModel('catalog/product')->load($productid);
  
          $sales[$idx] = array();
          $rowtotal = $item->getBaseRowTotal();
          if (is_null($rowtotal))
          {
            $rowtotal = $item->getBasePrice();
          }
          $sales[$idx]['totalcost'] = $rowtotal - $item->getBaseDiscountAmount();
  //        $sales[$idx]['totalcost'] = $item->getBasePrice() - $item->getBaseDiscountAmount();
          if (count($items) > 1)
          {
            // tack on the index of this row to cheat the fraud detection filters
            $sales[$idx]['orderid'] = $order->getIncrementId()."{".($idx+1)."}";
          }
          else
          {
            $sales[$idx]['orderid'] = $order->getIncrementId();
          }
          if ($config->getChannelID())
          {
            $sales[$idx]['channelid'] = $config->getChannelID();
          }
          if ($config->getUseLifetimeReferrals())
          {
            $sales[$idx]['data1'] = $order->getCustomerName();
          }
          else if ($config->getData1())
          {
            $sales[$idx]['data1'] = $this->TranslateData($config->getData1(), $order, $item, $product);
          }
          if ($config->getData2())
          {
            $sales[$idx]['data2'] = $this->TranslateData($config->getData2(), $order, $item, $product);
          }
          if ($config->getData3())
          {
            $sales[$idx]['data3'] = $this->TranslateData($config->getData3(), $order, $item, $product);
          }
          if ($config->getData4())
          {
            $sales[$idx]['data4'] = $this->TranslateData($config->getData4(), $order, $item, $product);
          }
          if ($config->getData5())
          {
            $sales[$idx]['data5'] = $this->TranslateData($config->getData5(), $order, $item, $product);
          }
          $sales[$idx]['productid'] = $product->getSku();
          $sales[$idx]['couponcode'] = $couponcode;
          
          if (!is_null($refid) && $refid)
          {
            $sales[$idx]['affiliateid'] = $refid;
          }
          else
          {
            if ($config->getAffiliateID())
            {
              $sales[$idx]['affiliateid'] = $this->TranslateData($config->getAffiliateID(), $order, $item, $product);
            }
          }
        }
        // add one more for shipping, if relevant
        if($config->getAddShipping() && $order->getShippingAmount() != 0)
        {
          $idx++;
          $sales[$idx] = array();
          $sales[$idx]['totalcost'] = $order->getShippingAmount();
          // tack on a little extra to cheat the fraud detection filters
          $sales[$idx]['orderid'] = $order->getIncrementId()."{SHIPPING}";
          if ($config->getChannelID())
          {
            $sales[$idx]['channelid'] = $config->getChannelID();
          }
          if ($config->getUseLifetimeReferrals())
          {
            $sales[$idx]['data1'] = $order->getCustomerId();
          }
          else if ($config->getData1())
          {
            $sales[$idx]['data1'] = $this->TranslateData($config->getData1(), $order, null, null);
          }
          if ($config->getData2())
          {
            $sales[$idx]['data2'] = $this->TranslateData($config->getData2(), $order, null, null);
          }
          if ($config->getData3())
          {
            $sales[$idx]['data3'] = $this->TranslateData($config->getData3(), $order, null, null);
          }
          if ($config->getData4())
          {
            $sales[$idx]['data4'] = $this->TranslateData($config->getData4(), $order, null, null);
          }
          if ($config->getData5())
          {
            $sales[$idx]['data5'] = $this->TranslateData($config->getData5(), $order, null);
          }
          $sales[$idx]['productid'] = "SHIPPING CHARGES";
          $sales[$idx]['couponcode'] = $couponcode;
          if (!is_null($refid) && $refid)
          {
            $sales[$idx]['affiliateid'] = $refid;
          }
          else
          {
            if ($config->getAffiliateID())
            {
              $sales[$idx]['affiliateid'] = $this->TranslateData($config->getAffiliateID(), $order, null, null);
            }
          }
        }
      }
      else
      {
        // Do NOT split by items. Just get basic order information
        $sales[0] = array();

        $subtotal = $order->getSubtotal();
        $discount = $order->getBaseDiscountAmount();
        $shipping = $config->getAddShipping() ? $order->getShippingAmount() : 0;

        $sales[0]['totalcost'] = $subtotal + $shipping - $discount;
        $sales[0]['orderid'] = $order->getIncrementId();
        if ($config->getChannelID())
        {
          $sales[0]['channelid'] = $config->getChannelID();
        }
        if ($config->getUseLifetimeReferrals())
        {
          $sales[0]['data1'] = ($order->getCustomerId() ? $order->getCustomerId() : null);
        }
        else if ($config->getData1())
        {
          $sales[0]['data1'] = $this->TranslateData($config->getData1(), $order, null, null);
        }
        if ($config->getData2())
        {
          $sales[0]['data2'] = $this->TranslateData($config->getData2(), $order, null, null);
        }
        if ($config->getData3())
        {
          $sales[0]['data3'] = $this->TranslateData($config->getData3(), $order, null, null);
        }
        if ($config->getData4())
        {
          $sales[0]['data4'] = $this->TranslateData($config->getData4(), $order, null, null);
        }
        if ($config->getData5())
        {
          $sales[0]['data5'] = $this->TranslateData($config->getData5(), $order, null, null);
        }
        $sales[0]['productid'] = null;
        $sales[0]['couponcode'] = $couponcode;
        
        if (!is_null($refid) && $refid)
        {
          $sales[0]['affiliateid'] = $refid;
        }
        else
        {
          if ($config->getAffiliateID())
          {
            $sales[0]['affiliateid'] = $this->TranslateData($config->getAffiliateID(), $order, null, null);
          }
        }
      }
      
      return $sales;
    }

    function TranslateData($data, $order, $item, $product)
    {
      switch ($data)
      {
        case "{{item_name}}": return !is_null($item) ? $item->getName() : null ;
        case "{{item_qty}}": return !is_null($item) ? $item->getQtyOrdered() : null ;
        case "{{item_price}}":
          if (!is_null($item))
          {
            $rowtotal = $item->getBaseRowTotal();
            if (is_null($rowtotal))
            {
              $rowtotal = $item->getBasePrice();
            }
            return $rowtotal;
          }
          return null;
        case "{{product_category_id}}": return !is_null($product) ? $product->getCategoryId() : null ;
        case "{{item_sku}}": return !is_null($item) ? $item->getSku() : null ;
        case "{{product_url}}": return !is_null($product) ? $product->getProductUrl(false) : null ;
        case "{{item_weight}}": return !is_null($item) ? $item->getWeight() : null ;
        case "{{item_cost}}": return !is_null($item) ? $item->getCost() : null ;
        case "{{item_discount_percent}}": return !is_null($item) ? $item->getDiscountPercent() : null ;
        case "{{item_discount_amount}}": return !is_null($item) ? $item->getBaseDiscountAmount() : null ;
        case "{{item_tax_percent}}": return !is_null($item) ? $item->getTaxPercent() : null ;
        case "{{item_tax_amount}}": return !is_null($item) ? $item->getTaxAmount() : null ;
        case "{{item_row_weight}}": return !is_null($item) ? $item->getRowWeight() : null ;
        case "{{order_store_id}}": return !is_null($order) ? $order->getStoreId() : null ;
        case "{{order_id}}": return !is_null($order) ? $order->getId() : null ;
        case "{{customer_id}}": return (!is_null($order) && $order->getCustomerId()) ? $order->getCustomerId() : null ;
        case "{{customer_email}}": return (!is_null($order) && $order->getCustomerEmail()) ? $order->getCustomerEmail() : null ;
        default: return $data;
      }
    }
    
    function _construct()
    {
        $this->_init('pap/pap');
    }

    function addError($error)
    {
        $this->_errors[] = $error;
    }

    function getErrors()
    {
        return $this->_errors;
    }

    function resetErrors()
    {
        $this->_errors = array();
    }

    function printError($error, $line = null)
    {
        if ($error == null) return false;
        $img = 'error_msg_icon.gif';
        $liStyle = 'background-color:#FDD; ';
        echo '<li style="'.$liStyle.'">';
        echo '<img src="'.Mage::getDesign()->getSkinUrl('images/'.$img).'" class="v-middle"/>';
        echo $error;
        if ($line) {
            echo '<small>, Line: <b>'.$line.'</b></small>';
        }
        echo "</li>";
    }
    
    protected function _beforeSave()
    {
      return parent::_beforeSave();
    }
    
    public function SetOrderStatus($order, $status)
    {
      $this->getResource()->SetOrderStatus($order, $status);
    }
    
}

?>