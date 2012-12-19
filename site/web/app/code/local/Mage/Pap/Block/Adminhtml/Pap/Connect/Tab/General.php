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

class Mage_Pap_Block_Adminhtml_Pap_Connect_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{

    function initForm()
    {
        $model = Mage::registry('pap_data');
      

        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('pap')->__('General Information')));
        
        $fieldset->addField('affiliateemail', 'text', array(
            'label'     => $this->__('Affiliate Email'),
            'title'     => $this->__('Affiliate Email'),
            'name'      => 'affiliateemail',
            'required'  => true,
        ));
          
        $fieldset->addField('orderid', 'text', array(
            'label'     => $this->__('Order ID'),
            'title'     => $this->__('Order ID'),
            'name'      => 'orderid',
            'required'  => true,
        ));
        
//        $values = $model->getData();
//        $form->setValues($values);

        $this->setForm($form);

        return $this;
    }
}
