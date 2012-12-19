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

class Mage_Pap_Block_Adminhtml_Pap_Grid_Renderer_Orderid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
      $paporderid = null;
      if ($row->getOrderid())
      {
        $paporderid = $row->getOrderid();
      }
      else
      {
        // someone changed the name
        $paporderid = $row->getTOrderid();
      }

      // strip the item number from the id in PAP
      $incrementid = preg_replace('~^([^\{]*).*~', '$1', $paporderid);

      // convert the id in PAP into an order id
      $order = Mage::getModel('sales/order')->loadByIncrementId($incrementid);
      if ($order)
      {
        $orderid = $order->getId();
      }
      else
      {
        $orderid = null;
      }

      if ($orderid)
      {
        // we can link to an order. Return the link

        //****************************************
        // BEGIN UGLY HACK
        //****************************************
        
        // Unfortunately, getting the link is a real pain. We have to do some funky stuff
        // to get a link that works, because the URL mechanism in Magento is broken.
        $readNode = Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
        $customadmin = (string)$readNode;
        if (!$customadmin)
        {
          $customadmin = "admin";
        }
      
        $url = Mage::getModel('adminhtml/url')->getUrl($customadmin.'/sales_order/view/', array('order_id' => $orderid));
        // Because of the new secret key # in 1.3.0 we MUST use the line above for the URL, however,
        // this returns a BROKEN URL with the "admin" portion stripped. We have to correct this.
        $url = preg_replace("~(/)(/sales_order/view.*)~", "\\1".$customadmin."\\2", $url);

        //****************************************
        // END UGLY HACK
        //****************************************
        
        // This is the code that should have been able to do what the above did
//        $url = $this->getUrl('admin/sales_order/view', array('order_id' => $orderid));

        return '<a href="'.$url.'">'.htmlentities($incrementid).'</a>';
      }
      else
      {
        // no order found, just return the increment ID
        return htmlentities($incrementid);
      }
    }
}
