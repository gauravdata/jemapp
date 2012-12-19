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

class Mage_Pap_Block_Adminhtml_Pap_Setstatus_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('pap_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('pap')->__('Set Affiliate Order Status'));
    }

    protected function _beforeToHtml()
    {
        $model = Mage::registry('pap_data');
      
        $generalBlock = $this->getLayout()->createBlock('pap/adminhtml_pap_setstatus_tab_general');
        $generalBlock->addData($model->getData());

        $new = !$model->getId();

        $this->addTab('general', array(
            'label'     => Mage::helper('pap')->__('General Information'),
            'content'   => $generalBlock->initForm()->toHtml(),
            'active'    => true,
        ));

        return parent::_beforeToHtml();
    }
}