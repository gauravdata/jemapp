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

class Mage_Pap_Model_Apisession extends Mage_Core_Model_Abstract
{
    protected $_session;
    
    protected function _init($resourceModel)
    {
        $this->_setResourceModel($resourceModel);
    }
    
    public function getSession($url = null, $username = null, $password = null, $roleType = Gpf_Api_Session::MERCHANT)
    {
      if (isset($this->_session))
      {
        return $this->_session;
      }
      
      // else open the session with the given information
      
      $config = Mage::getSingleton('pap/config');
      if (is_null($url))
      {
        $url = $config->getRemotePath().'/scripts/server.php';
      }
      if (is_null($username))
      {
        $username = $config->getAPIUsername();
      }
      if (is_null($password))
      {
        $password = $config->getAPIPassword();
      }
      
      $this->_session = new Gpf_Api_Session($url);
      
      $this->_session->login($username, $password, $roleType);
      
      return $this->_session;
    }
    
}

?>