<!-- vat_settings -->

<div class="VatForm">
<fieldset>
<legend>##VAT settings##</legend>
{widget id="support_vat" class="VatSettingsForm"}
{widget id="vat_percentage"}
{widget id="vat_computation"}
</fieldset>

<fieldset>
<legend>##Payout invoice - VAT version##</legend>
##HTML format of the invoice for users with VAT applicable.##
##You can use Smarty syntax in this template and the constants from the list below.##

{widget id="payout_invoice_with_vat"}
<div class="FormFieldLabel"><div class="Inliner">##Payout preview##</div></div>
<div class="FormFieldInputContainer">
    <div class="FormFieldInput">{widget id="userid"}</div>
    <div class="FormFieldHelp">{widget id="previewInvoiceHelp"}</div>
    <div>{widget id="previewInvoiceWithVat"}</div>
    {widget id="formPanel"}
    <div class="FormFieldDescription">
        ##By clicking Preview invoice you can see how the invoice will look like for the specified affiliate.##
        <br/>
        ##WARNING: VAT constants will be empty if you show preview for the affiliate that has VAT invoicing disabled.##
    </div>
</div>
<div class="clear"/></div>  
</fieldset>
</div>

{widget id="FormMessage"}
{widget id="SaveButton"}
<div class="clear"></div>
