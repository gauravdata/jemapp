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

class Mage_Pap_Model_Config extends Mage_Core_Model_Config_Base
{
    var $dbconfigured;
    var $configured;
    
    var $apiusername;
    var $apipassword;
    
    public function __construct()
    {
      error_reporting(E_ALL & ~E_NOTICE); // We have no interest in supporting notice based errors. Just turn them off.
      
      // grab the nodes with the connection information
      $foreignNode = Mage::getConfig()->getNode('global/resources/pap_foreign/connection');
      
      // grab the connection information from the store config
      $host = Mage::getStoreConfig('pap_config/connection/host');
      $username = Mage::getStoreConfig('pap_config/connection/username');
      $password = Mage::getStoreConfig('pap_config/connection/password');
      $dbname = Mage::getStoreConfig('pap_config/connection/dbname');

      // also get API information
      $this->apiusername = Mage::getStoreConfig('pap_config/api/username');
      $this->apipassword = Mage::getStoreConfig('pap_config/api/password');
      
      // update the configuration information in memory from the store configuration
      // We have to do this so that when things attempt to connect to a database,
      // the right database can be found and connected to
      $foreignNode->setNode('host', $host);
      $foreignNode->setNode('username', $username);
      $foreignNode->setNode('password', $password);
      $foreignNode->setNode('dbname', $dbname);
      
      // remember whether we have enough information to connect
      $this->dbconfigured = ($host && $username && $password && $dbname);
      $this->configured = ($this->apiusername && $this->apipassword);
      
      if ($this->dbconfigured && !$this->configured)
      {
        // we have the old DB information, but not the new password information.
        // The module was rewritten to use the API (which requires a PAP username
        // and password, rather than database access.)
        
        // Try to get an admin password from the DB.
        $foreignConnection = Mage::getSingleton('core/resource')->getConnection('pap_foreign');
        try
        {
          if ($mercaccount = $foreignConnection->fetchRow("SELECT au.username AS username, au.rpassword AS password FROM qu_g_authusers AS au INNER JOIN qu_g_users AS u ON au.authid = u.authid WHERE u.roleid = 'pap_merc'"))
          {
            // we found the first merchant account. remember the username and password.
            $this->apiusername = $mercaccount['username'];
            $this->apipassword = $mercaccount['password'];
            
            // recalculate the "configured" value.
            $this->configured = ($this->apiusername && $this->apipassword);
            
            // save off the username and password so we don't have to recalculate again.
            Mage::getConfig()->saveConfig('pap_config/api/username', $this->apiusername);
            Mage::getConfig()->saveConfig('pap_config/api/password', $this->apipassword);
          }
        } catch (Exception $e) {
          // Something bad happened. I guess we won't be able to update this automatically.
        }
      }
      
      parent::__construct(Mage::getConfig()->getNode('global'));
    }
    
    public function IsConfigured()
    {
        return $this->configured;
    }
    
    public function getAPIUsername()
    {
        return $this->apiusername;
    }
    
    public function getAPIPassword()
    {
        return $this->apipassword;
    }
    
    public function getLocalPath()
    {
        $server = Mage::getStoreConfig('pap_config/general/docroot');
        if (!$server)
        {
          $server = $_SERVER["DOCUMENT_ROOT"];
        }
        $server = rtrim($server, "/\\ \r\n\t");
        $dir = trim(Mage::getStoreConfig('pap_config/general/directory'), "/\\ \r\n\t");
        if ($dir != "")
        {
          $dir = "/".$dir;
        }
        return $server.$dir;
    }

    // ensures that the PAP API has been loaded 
    public function RequirePapAPI()
    {
      error_reporting(E_ALL & ~E_NOTICE); // drop the error level just a notch because the included code can't handle it

      // calculate the path to the local API file (where it should be anyway.)
      $includefile = $this->getLocalPath().'/api/PapApi.class.php';
      if (file_exists($includefile))
      {
        // We have a local version of the API. Perfect.
        require_once($includefile);
      }
      else
      {
        // No local API file found. The most likely scenario for this is that
        // they are using a hosted PAP plan. We distribute the latest API files
        // with the connector specifically to handle this scenario.
        //
        // This still won't work reliably for cross domain installs, because the
        // API file doesn't usually work well across versions, so unless the user
        // is running the absolute latest version, API based tracking still might
        // not work reliably.
        require_once('PAP/PapApi.class.php');
      }
    }
    
    public function getRemotePath()
    {
        $server = Mage::getStoreConfig('pap_config/general/domain');
        
        if (!$server)
        {
          $server = $_SERVER["SERVER_NAME"];
        }
        
        // Sometimes the users put extra stuff in the domain. Strip it out.
        // This regex will capture anything after the http[s]://, and before
        // the next /, \, ?, #, or <space>
        $server = preg_replace('~(https?://)?([^/\\\\\\?#\\s]*).*~', '$2', $server);

        $dir = trim(Mage::getStoreConfig('pap_config/general/directory'), "/\\ \r\n\t");
        // do we use HTTP, or HTTPS protocol? 
        $s = empty($_SERVER["HTTPS"]) ? '' 
                : ($_SERVER["HTTPS"] == "on") ? "s"
                : "";
        $protocol = "http".$s;
        if ($dir != "")
        {
          $dir = "/".$dir;
        }
        return $protocol."://".$server.$dir;
    }
    
    public function getTrackClicks()
    {
        return Mage::getStoreConfigFlag('pap_config/tracking/trackclicks') ? true : false;
    }
    
    public function getTrackclickscript()
    {
        return Mage::getStoreConfig('pap_config/tracking/trackclickscript');
    }
    
    public function getTrackSales($language = null)
    {
        $val = Mage::getStoreConfig('pap_config/tracking/tracksales');
        if (!$language)
        {
          return $val;
        }
        
        // make sure tracking for a specific language is allowed
        switch($language)
        {
        case "php":
          return (($val == "php") ? true : false); // only if PHP was specifically asked for
        case "javascript":
          return (($val == "javascript" || $val == "default") ? true : false); // javascript is also the default
        }
        return false; // unknown
    }
    
    public function getTracksalescript()
    {
        return Mage::getStoreConfig('pap_config/tracking/tracksalescript');
    }
    
    public function getChannelID()
    {
      return Mage::getStoreConfig('pap_config/tracking/channelid');
    }
    
    public function getAffiliateID()
    {
      return Mage::getStoreConfig('pap_config/tracking/affiliateid');
    }
    
    public function getData1()
    {
      return Mage::getStoreConfig('pap_config/tracking/data1');
    }
    
    public function getData2()
    {
      return Mage::getStoreConfig('pap_config/tracking/data2');
    }
    
    public function getData3()
    {
      return Mage::getStoreConfig('pap_config/tracking/data3');
    }
    
    public function getData4()
    {
      return Mage::getStoreConfig('pap_config/tracking/data4');
    }
    
    public function getData5()
    {
      return Mage::getStoreConfig('pap_config/tracking/data5');
    }
    
    public function getTerms()
    {
      return Mage::getStoreConfig('pap_config/pap_legal/pap_terms');
    }
    
    public function getAddShipping()
    {
        return Mage::getStoreConfigFlag('pap_config/tracking/addshipping') ? true : false;
    }
    
    public function getPerProduct()
    {
        return Mage::getStoreConfigFlag('pap_config/tracking/perproduct') ? true : false;
    }
    
    public function getUseLifetimeReferrals()
    {
        return Mage::getStoreConfigFlag('pap_config/tracking/lifetimereferrals') ? true : false;
    }
}
