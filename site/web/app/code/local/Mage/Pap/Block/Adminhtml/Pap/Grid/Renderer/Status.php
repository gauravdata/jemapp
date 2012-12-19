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

class Mage_Pap_Block_Adminhtml_Pap_Grid_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
      $rstatus = $row->getRstatus();
      if ($row->getPayoutstatus() == 'P')
      {
        return '<span class="papstatus-paid">Paid</span>';
      }
      else if ($row->getRstatus() == 'A')
      {
        return '<span class="papstatus-approved">Approved</span>';
      }
      else if ($row->getRstatus() == 'D')
      {
        return '<span class="papstatus-declined">Declined</span>';
      }
      else if ($row->getRstatus() == 'P')
      {
        return '<span class="papstatus-pending">Pending</span>';
      }
      else
      {
        return '<span class="papstatus-unknown">Unknown</span>';
      }
    }
}
