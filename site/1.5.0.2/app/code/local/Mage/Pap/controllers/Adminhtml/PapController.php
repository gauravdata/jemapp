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

class Mage_Pap_Adminhtml_PapController extends Mage_Adminhtml_Controller_Action
{
    var $localconfig; // local config var. Once set, we know we have the real config settings
    
    protected function _initAction()
    {
    die;
        return $this;
    }
    
    public function indexAction()
    {
      // index is the same as manage (show the grid)
      return $this->manageAction();
    }
    
    public function manageAction()
    {
      $config = Mage::getSingleton('pap/config');
      if (!$config->IsConfigured())
      {
        $this->doConfigNotice('pap/adminhtml_pap/manage');
        return;
      }
      
      $this->loadLayout()
          ->_setActiveMenu('pap/adminhtml_pap/manage')
          ->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Affiliate Orders'), Mage::helper('adminhtml')->__('Manage Affiliate Orders'));
      $this->_addContent(
          $this->getLayout()->createBlock('pap/adminhtml_pap')
      );
      $this->renderLayout();
    }
  
    public function checkconfigAction()
    {
      $config = Mage::getSingleton('pap/config');
      if (!$config->IsConfigured())
      {
        $this->doConfigNotice('pap/adminhtml_pap/checkconfig');
        return;
      }
      
      $this->loadLayout()
          ->_setActiveMenu('pap/adminhtml_pap/checkconfig')
          ->_addBreadcrumb(Mage::helper('adminhtml')->__('Affiliate Module Configured'), Mage::helper('adminhtml')->__('Affiliate Module Configured'));

      // TODO: It would be better to put this markup in a separate block of our own.
      ob_start();
      ?>
        <div class="content-header">
          <h3 class="icon-head">Post Affiliate Pro Connector is Configured</h3>
        </div>
        <div style="width: 600px;">
          <p>The Post Affiliate Pro Connector is ready for use.</p>
        </div>
      <?php
      $text = ob_get_contents();
      ob_end_clean();
      
      
      $textblock = $this->getLayout()->createBlock('core/text');
      $textblock->addText($text);
      $this->_addContent(
               $textblock
      );
      $this->renderLayout();
    }
  
    // notice displayed when this hasn't been configured yet.
    public function doConfigNotice($menu)
    {
      $this->loadLayout()
          ->_setActiveMenu($menu)
          ->_addBreadcrumb(Mage::helper('adminhtml')->__('Affiliate Module Not Configured'), Mage::helper('adminhtml')->__('Affiliate Module Not Configured'));

      // TODO: It would be better to put this markup in a separate block of our own.
      
      // declare the URLs that we will use
      $getpapurl = "http://www.priacta.com/code/magento-affiliate/pap.php";
      $papinfourl = "http://www.priacta.com/code/magento-affiliate/papinfo.php";
      $getpaphostedurl = "http://www.priacta.com/code/magento-affiliate/paphosted.php";
      $getpaplicenseurl = "http://www.priacta.com/code/magento-affiliate/paplicense.php";
      
      $papbannerurl = $getpapurl;
      $papinstallurl = "http://www.priacta.com/code/magento-affiliate/papinstall.php";
      
      $readNode = Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
      $customadmin = (string)$readNode;
      if (!$customadmin)
      {
        $customadmin = "admin";
      }
      
      $configurl = Mage::getModel('adminhtml/url')->getUrl($customadmin.'/system_config/edit/section/pap_config/');
      // Because of the new secret key # in 1.3.0 we MUST use the line above for the URL, however,
      // this returns a BROKEN URL with the "admin" portion stripped. We have to correct this.
      $configurl = preg_replace("~(/)(/system_config/edit/section/pap_config.*)~", "\\1".$customadmin."\\2", $configurl);
      ob_start();
      ?>
        <table><tr>
          <td>
            <a href="<?php echo $papbannerurl; ?>" target="_blank"><img src="http://www.qualityunit.com/affiliate/banners/pap_skyscraper.gif" alt="" title=""   /></a><img style="border:0" src="http://www.qualityunit.com/affiliate/scripts/imp.php?a_aid=499e253c9a2ce&amp;a_bid=a1a133d5" width="1" height="1" alt="" />
          </td>
          <td style="padding-left:10px; width: 100%;">
            <div class="content-header">
              <h3 class="icon-head">Setting Up Post Affiliate Pro</h3>
            </div>
            <div style="width: 600px;">
              <p style="font-weight: bold;">The Post Affiliate Pro Connector is not ready yet. It has been installed, but not configured.</p>
              <p style="font-weight: bold;">Follow the steps below to complete the process.</p>
              <ol style="list-style-type: decimal; margin-left: 25px;">
              <li style="margin-bottom: 30px;">
                  <h5><a href="<?php echo $getpapurl; ?>" target="_blank">Get Post Affiliate Pro</a></h5>
                  <p>If you don't have it already, you'll need a license for Post Affiliate Pro.
                  This isn't free, but it's cheap, and worth it. Quality Unit (the company that sells PAP) currently has
                  two different licensing options:
                  <ol>
                    <li>Month to month - <a href="<?php echo $getpaphostedurl; ?>">Hosted Plans</a> currently start as low as $19/mo, with no extra surcharge per commission.</li>
                    <li>One Time Purchase - <a href="<?php echo $getpaplicenseurl; ?>">Licenses</a> currently start as low as a $119, one time fee. You get source code, and install PAP on your own server, alongside Magento.</li>
                  </ol>
                  Don't be fooled by the low price. This is high quality software, and the feature puts everyone else to shame.</p>
                  <a href="<?php echo $getpapurl; ?>" target="_blank"><button class="scalable">Get Post Affiliate Pro</button></a>
                  <a href="<?php echo $papinfourl; ?>" target="_blank"><button class="scalable">More Information</button></a>
              </li>
              <li style="margin-bottom: 30px;">
                  <h5><a href="<?php echo $papinstallurl; ?>" target="_blank">Install Post Affiliate Pro</a></h5>
                  <p>This is really a piece of cake. You could be done in as little as 5-10 minutes.</p>
                  <a href="<?php echo $papinstallurl; ?>" target="_blank"><button class="scalable">Install Post Affiliate Pro</button></a>
              </li>
              <li style="margin-bottom: 30px;">
                  <h5><a href="<?php echo $configurl; ?>" target="_blank">Configure Magento</a></h5>
                  <p>The configuration is basic. Enter the username and password for your PAP merchant
                  account, and make sure the domain name and path are set correctly.</p>
                  <a href="<?php echo $configurl; ?>" target="_blank"><button class="scalable">Configure Magento</button></a>
              </li>
              </ol>
            </div>
          </td>
        </tr></table>
      <?php
      $text = ob_get_contents();
      ob_end_clean();
      
      
      $textblock = $this->getLayout()->createBlock('core/text');
      $textblock->addText($text);
      $this->_addContent(
               $textblock
      );
      $this->renderLayout();
    }
    
    public function connectAction()
    {
      $config = Mage::getSingleton('pap/config');
      if (!$config->IsConfigured())
      {
        $this->doConfigNotice('pap/adminhtml_pap/connect');
        return;
      }
      
      $papId     = $this->getRequest()->getParam('id');
      $papModel  = Mage::getModel('pap/pap')->load($papId);
      if ($papModel->getId() || $papId == 0)
      {
        Mage::register('pap_data', $papModel);
 
        $this->loadLayout();
        $this->_setActiveMenu('pap/adminhtml_pap/connect');
           
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Connect Affiliate to Order'), Mage::helper('adminhtml')->__('Connect Affiliate to Order'));
           
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
        $this->_addContent($this->getLayout()->createBlock('pap/adminhtml_pap_connect'))
             ->_addLeft($this->getLayout()->createBlock('pap/adminhtml_pap_connect_tabs'));
               
        $this->renderLayout();
      }
      else
      {
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pap')->__('Item does not exist'));
        $this->_redirect('*/*/');
      }
    }
  
    public function connectPostAction()
    {
      if ( $this->getRequest()->getPost() )
      {
        try
        {
          $postData = $this->getRequest()->getPost();

          // Send the keys for the order...
          $papModel = Mage::getModel('pap/pap');
          $papModel->connectAffiliateToOrder($postData['affiliateemail'], $postData['orderid'], false);
          
          Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
          Mage::getSingleton('adminhtml/session')->setTrogbarData(false);

          $this->_redirect('*/*/');
          return;
        }
        catch (Exception $e)
        {
          Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
          Mage::getSingleton('adminhtml/session')->setTrogbarData($this->getRequest()->getPost());
          $this->_redirect('*/*/connect', array('id' => $this->getRequest()->getParam('id')));
          return;
        }
      }
      $this->_redirect('*/*/');
    }
    
    public function setStatusAction()
    {
      $config = Mage::getSingleton('pap/config');
      if (!$config->IsConfigured())
      {
        $this->doConfigNotice('pap/adminhtml_pap/setStatus');
        return;
      }
      
      $papId     = $this->getRequest()->getParam('id');
      $papModel  = Mage::getModel('pap/pap')->load($papId);
      if ($papModel->getId() || $papId == 0)
      {
        Mage::register('pap_data', $papModel);
 
        $this->loadLayout();
        $this->_setActiveMenu('pap/adminhtml_pap/setStatus');
           
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Set Affiliate Order Status'), Mage::helper('adminhtml')->__('Set Affiliate Order Status'));
           
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
        $this->_addContent($this->getLayout()->createBlock('pap/adminhtml_pap_setstatus'))
             ->_addLeft($this->getLayout()->createBlock('pap/adminhtml_pap_setstatus_tabs'));
               
        $this->renderLayout();
      }
      else
      {
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pap')->__('Item does not exist'));
        $this->_redirect('*/*/');
      }
    }
  
    public function setStatusPostAction()
    {
      if ( $this->getRequest()->getPost() )
      {
        try
        {
          $postData = $this->getRequest()->getPost();

          // Send the keys for the order...
          $papModel = Mage::getModel('pap/pap');

          // Get the order
          $order = Mage::getModel('sales/order');
          // auto detect ID type by trying both
          $order->load($postData['orderid']);
          if (!$order->getId())
          {
            $order->loadByIncrementId($postData['orderid']);
          }
          if (!$order->getId())
          {
            // bad order #
            Mage::getSingleton('adminhtml/session')->addError("No order found with this ID");
            Mage::getSingleton('adminhtml/session')->setSetstatusData($this->getRequest()->getPost());
            $this->_redirect('*/*/setStatus', array('id' => $this->getRequest()->getParam('id')));
            return;
          }
          
          $papModel->SetOrderStatus($order, $postData['orderstatus']);
          
          Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
          Mage::getSingleton('adminhtml/session')->setSetstatusData(false);

          $this->_redirect('*/*/');
          return;
        }
        catch (Exception $e)
        {
          Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
          Mage::getSingleton('adminhtml/session')->setSetstatusData($this->getRequest()->getPost());
          $this->_redirect('*/*/setStatus', array('id' => $this->getRequest()->getParam('id')));
          return;
        }
      }
      $this->_redirect('*/*/');
    }
    
    protected function _validateSecretKey()
    {
      // We don't need any secret key validation on the back end at the moment.
      // This is specifically interfering with the checkconfig page. 
//        $url = Mage::getSingleton('adminhtml/url');
//
//        if (!($secretKey = $this->getRequest()->getParam(Mage_Adminhtml_Model_Url::SECRET_KEY_PARAM_NAME, null))
//            || $secretKey != $url->getSecretKey($url->getOriginalControllerName(), $url->getOriginalActionName())) {
//            return false;
//        }
        return true;
    }
}

?>