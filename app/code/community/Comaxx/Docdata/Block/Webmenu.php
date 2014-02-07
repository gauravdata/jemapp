<?php
/**
 * Block which redirects the user to Docdata 
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
class Comaxx_Docdata_Block_Webmenu extends Mage_Core_Block_Abstract {
	
	protected function _toHtml()
	{
		$html = '<html><body>';

		/* temporary message */
		$html.= $this->__('Redirecting to Docdata...');

		/* check if the cluster already exists
		   (used when customer click on the previous button of his browser) */
		if ($this->getPaymentOrderExists()) {
			$html.= '<script type="text/javascript">history.go(1);</script>';
		} else {
			/* retrieving values */
			$webmenu = $this->getWebmenuUrl();
			$params = $this->getParams();

			/* creating the form with action and hidden fields */
			$form = new Varien_Data_Form();
			$form->setAction($webmenu)
				 ->setId('docdata_checkout')
				 ->setName('docdata_checkout')
				 ->setMethod('GET')
				 ->setUseContainer(true);

			foreach($params as $key => $value) {
				$form->addField($key, 'hidden', array('name'=>$key, 'value'=>$value));
			}
			
			$html.= $form->toHtml();

			/* send the form */
			$html.= '<script type="text/javascript">document.getElementById("docdata_checkout").submit();</script>';
		}

		$html.= '</body></html>';
		
		return $html;
	}
	
}