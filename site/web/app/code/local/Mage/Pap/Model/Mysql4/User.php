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

class Mage_Pap_Model_Mysql4_User extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // grabbing the singleton will ensure that the connection settings
        // are available for access when the connection is established
        $config = Mage::getSingleton('pap/config');
        
        $this->_init('pap/user', 'userid');
    }

    // the base getTable() may tack on an unwanted prefix. Kill it here.
    public function getTable($name)
    {
        $ret = parent::getTable($name);
        
        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();
        $ret = preg_replace('~^'.preg_quote($tablePrefix).'~', '', $ret);

        return $ret;
    }
    
    function loadByEmail($user, $email)
    {
      // find the user ID for the email address
      $select = $this->_getReadAdapter()->select()
                     ->from("qu_pap_users", array("userid"))
                     ->join("qu_g_users", "qu_pap_users.accountuserid = qu_g_users.accountuserid")
                     ->join("qu_g_authusers", "qu_g_users.authid = qu_g_authusers.authid")
                     ->where('qu_g_authusers.username=?', $email);
      $id = $this->_getReadAdapter()->fetchOne($select);
      if ($id)
      {
        $this->load($user, $id);
      }
      else
      {
        $user->setData(array());
      }
      return $this;
    }
}
?>