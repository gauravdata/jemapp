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

class Mage_Pap_Block_Clicktracking extends Mage_Core_Block_Text
{
    protected function _toHtml()
    {
        $config = Mage::getSingleton('pap/config');
        if (!$config->getTrackClicks()) {
            return '';
        }

        $id = 'pap_x2s6df8d';
        global $have_pap_x2s6df8d;
        if (isset($have_pap_x2s6df8d) && $have_pap_x2s6df8d)
        {
          $id = 'pap_x2s6df8d_clicktrack';
        }
        $have_pap_x2s6df8d = true;

        $this->addText('
          <!-- BEGIN AFFILIATE TRACKING CODE -->
          <script id="'.$id.'" src="'.$config->getRemotePath().'/scripts/'.$config->getTrackclickscript().'" type="text/javascript">
          </script>
          <script type="text/javascript">
          <!--
          papTrack();
          //-->
          </script>
          <!-- END AFFILIATE TRACKING CODE -->
        ');

/* Asynchronous version. We can't use this currently because the tracking script uses document.write, and
   that causes problems if the page is already fully loaded. Chrome also has security issues with asynchronous
   document.write.

   QualityUnit has been asked to update the script to allow for this.

        $this->addText('
<!-- BEGIN AFFILIATE TRACKING CODE -->
<script type="text/javascript">
  (function() {
    var pap_script = document.createElement("script"); pap_script.type = "text/javascript"; pap_script.async = true;
    pap_script.id = "'.$id.'";
    pap_script.src = "'.$config->getRemotePath().'/scripts/'.$config->getTrackclickscript().'";
    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(pap_script, s);
  })();

  var pap_script_init = function()
  {
    if(typeof papTrack == "function")
    {
      clearInterval(pap_script_init_interval);
      papTrack();
    }
  }
  pap_script_init_interval = setInterval("pap_script_init()", 100);
</script>
<!-- END AFFILIATE TRACKING CODE -->
        ');
*/
        
        return parent::_toHtml();
    }
}
