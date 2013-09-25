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

class Mage_Pap_Block_Adminhtml_Pap_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();        
        $this->setId('papGrid');
        $this->setDefaultSort('dateinserted');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('pap/pap')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
//        $this->addColumn('transid', array(
//            'header'    =>Mage::helper('pap')->__('ID'),
//            'width'     =>'50px',
//            'index'     =>'transid',
//        ));
        $this->addColumn('orderid', array(
            'header'    =>Mage::helper('pap')->__('Order ID'),
            'width'     =>'50px',
            'index'     =>'orderid',
            'renderer' => 'pap/adminhtml_pap_grid_renderer_orderid'
        ));
        $this->addColumn('productid', array(
            'header'    =>Mage::helper('pap')->__('Product ID'),
            'width'     =>'50px',
            'index'     =>'productid',
        ));
//        $this->addColumn('userid', array(
//            'header'    =>Mage::helper('pap')->__('User ID'),
//            'width'     =>'50px',
//            'index'     =>'userid',
//        ));
        $this->addColumn('dateinserted', array(
            'header'    =>Mage::helper('pap')->__('Date'),
            'type'      => 'date',
            'align'     => 'center',
            'index'     =>'dateinserted',
        ));
        $this->addColumn('dateapproved', array(
            'header'    =>Mage::helper('pap')->__('Approved'),
            'type'      => 'date',
            'align'     => 'center',
            'index'     =>'dateapproved',
        ));
        $this->addColumn('rstatus', array(
            'header'    =>Mage::helper('pap')->__('Status'),
            'width'     =>'50px',
            'index'     =>'rstatus',
            'renderer' => 'pap/adminhtml_pap_grid_renderer_status'
        ));
        $this->addColumn('refererurl', array(
            'header'    =>Mage::helper('pap')->__('Referer URL'),
            'index'     =>'refererurl',
        ));
        $this->addColumn('totalcost', array(
            'header'    =>Mage::helper('pap')->__('Total'),
            'width'     =>'50px',
            'index'     =>'totalcost',
        ));
        $this->addColumn('commission', array(
            'header'    =>Mage::helper('pap')->__('Commission'),
            'width'     =>'50px',
            'index'     =>'commission',
        ));
		
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
      $config = Mage::getSingleton('pap/config');
      
      return $config->getRemotePath().'/merchants/#Transaction-Manager';
    }

}
