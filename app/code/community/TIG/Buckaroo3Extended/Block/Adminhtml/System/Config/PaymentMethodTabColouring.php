<?php 
class TIG_Buckaroo3Extended_Block_Adminhtml_System_Config_PaymentMethodTabColouring
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'buckaroo3extended/system/config/paymentmethodtabcolouring.phtml';
    
    public $methods = array(
        'amex',
        'directdebit',
        'giropay',
        'ideal',
        'mastercard',
        'onlinegiro',
        'paypal',
        'paysafecard',
        'sofortueberweisung',
        'transfer',
        'visa',
        'payperemail',
        'paymentguarantee'
    );
    
    public $services = array(
        'refund',
    );

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }
    
    public function getScriptHtml()
    {
        
        $buckarooLargeImage = $this->getSkinUrl("images/tig_buckaroo3extended/buckaroo_large.png");
    
        $script = "<script type = 'text/javascript'>"
                . "jQuery.noConflict();"
                . "jQuery('document').ready(";
                
        $script .= <<<SCRIPT
function() {
	if (jQuery("#buckaroo_buckaroo3extended_active").val() == 0 ) {
		jQuery("#buckaroo_buckaroo3extended").prev().prev().addClass('disabled');
	} 
	else if (jQuery("#buckaroo_buckaroo3extended_active").val() == 1 
			&& jQuery("#buckaroo_buckaroo3extended_mode").val() == 1)
	{
		jQuery("#buckaroo_buckaroo3extended").prev().prev().css('background-color','rgb(196,226,159)');
	} 
	else
	{
		jQuery("#buckaroo_buckaroo3extended").prev().prev().css('background-color','rgb(137,197,64)');
	}
	
	jQuery('#buckaroo_buckaroo3extended_advanced').prev().prev().css('background-color','rgb(245,111,9)')

	jQuery('.buckaroo-header').html('&nbsp;').css('background-image', "url('{$buckarooLargeImage}')").css('width','151').css('height','30');
SCRIPT;

        foreach ($this->methods as $method) {
            $script .= "if (jQuery('#buckaroo_buckaroo3extended_{$method}_active').val() == 0 ) {
    			jQuery('#buckaroo_buckaroo3extended_{$method}').prev().prev().addClass('disabled');
    		} 
    		else if (jQuery('#buckaroo_buckaroo3extended_{$method}_active').val() == 1 
    				&& jQuery('#buckaroo_buckaroo3extended_{$method}_mode').val() == 1)
    		{
    			jQuery('#buckaroo_buckaroo3extended_{$method}').prev().prev().css('background-color','rgb(196,226,159)');
    		} 
    		else
    		{
    			jQuery('#buckaroo_buckaroo3extended_{$method}').prev().prev().css('background-color','rgb(137,197,64)');
    		}";
        }
        
        foreach ($this->services as $service) {
            $script .= "if (jQuery('#buckaroo_buckaroo3extended_{$service}_active').val() == 0 ) {
    			jQuery('#buckaroo_buckaroo3extended_{$service}').prev().prev().addClass('disabled');
    		} 
    		else if (jQuery('#buckaroo_buckaroo3extended_{$service}_active').val() == 1 
    				&& jQuery('#buckaroo_buckaroo3extended_{$service}_allow_push').val() == 0)
    		{
    			jQuery('#buckaroo_buckaroo3extended_{$service}').prev().prev().css('background-color','rgb(174,216,230)');
    		} 
    		else
    		{
    			jQuery('#buckaroo_buckaroo3extended_{$service}').prev().prev().css('background-color','rgb(30,143,255)');
    		}
            ";
        }

        $script .= "
                }
            );
            </script>";
    }
}