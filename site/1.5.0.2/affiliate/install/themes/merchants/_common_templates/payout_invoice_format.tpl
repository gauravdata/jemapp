<!-- payout_invoice_format -->

<div class="PayoutsInvoiceSettingsForm">
<fieldset>
<legend>##Invoicing settings##</legend>
{widget id="generate_invoices"}
{widget id="invoice_bcc_recipient"}
</fieldset>
</div>

<div class="PayoutsInvoiceForm">
<fieldset>
<legend>##Payout invoice##</legend>
##HTML format of the invoice.##
##You can use Smarty syntax in this template and the constants from the list below.##
<br/>

{widget id="payoutInvoice"}
<div class="FormFieldLabel"><div class="Inliner">##Payout preview##</div></div>
<div class="FormFieldInputContainer">
    <div class="FormFieldInput">{widget id="userid"}</div>
    <div class="FormFieldHelp">{widget id="previewInvoiceHelp"}</div>
    <div>{widget id="previewInvoice"}</div>
    {widget id="formPanel"}
    <div class="FormFieldDescription">##By clicking Preview invoice you can see how the invoice will look like for the specified affiliate.##</div>
</div>
<div class="clear"/></div>
</fieldset>


{widget id="SaveButton"}
<div class="clear"></div>
</div>
