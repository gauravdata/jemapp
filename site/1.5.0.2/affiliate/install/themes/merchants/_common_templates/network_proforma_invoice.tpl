<!--	network_proforma_invoice	-->

<div class="NetworkInvoiceForm">
    <fieldset>
        <legend>##Proforma invoice format##</legend>
        ##HTML format of the proforma invoice. You can use Smarty syntax in this template and the constants from the list below.##<br/>
        <div class="FormFieldLabel"><div class="Inliner">##Proforma invoice##</div></div>
        <div class="FormFieldInputContainer">
            <div class="FormFieldInput">{widget id="network_proforma_invoiceInput"}</div>
        </div>
        <div class="clear"></div>
        <div class="FormFieldLabel"><div class="Inliner">##Proforma invoice preview##</div></div>
        <div class="FormFieldInputContainer">
            <div class="FormFieldInput">{widget id="account"}</div>
            <div>{widget id="previewButton"}</div>
            {widget id="previewPanel"}
            <div class="FormFieldDescription">##By clicking Preview you can see how the proforma invoice will look like for the specified account.##</div>
        </div>
        <div class="clear"></div>
    </fieldset>
    {widget id="saveButton"}
</div>
