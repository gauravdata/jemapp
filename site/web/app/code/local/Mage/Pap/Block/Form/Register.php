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

class Mage_Pap_Block_Form_Register extends Mage_Directory_Block_Data
{
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('pap')->__('Create New Affiliate Account'));
        return parent::_prepareLayout();
    }

    public function getBackUrl()
    {
        $url = $this->getData('back_url');
        return $url;
    }

    public function getFormData()
    {
      return new Varien_Object($_REQUEST);
    }
}