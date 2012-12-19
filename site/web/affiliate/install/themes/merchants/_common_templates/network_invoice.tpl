<!--	network_invoice		-->

<div class="NetworkInvoiceForm">
    <fieldset>
        <legend>##Invoice format##</legend>
        ##HTML format of the invoice. You can use Smarty syntax in this template and the constants from the list below.##<br/>
        <div class="FormFieldLabel"><div class="Inliner">##Invoice##</div></div>
        <div class="FormFieldInputContainer">
            <div class="FormFieldInput">{widget id="network_invoiceInput"}</div>
        </div>
        <div class="clear"></div>
        <div class="FormFieldLabel"><div class="Inliner">##Invoice preview##</div></div>
        <div class="FormFieldInputContainer">
            <div class="FormFieldInput">{widget id="account"}</div>
            <div>{widget id="previewButton"}</div>
            {widget id="previewPanel"}
            <div class="FormFieldDescription">##By clicking Preview you can see how the invoice will look like for the specified account.##</div>
        </div>
        <div class="clear"></div>
    </fieldset>
    {widget id="saveButton"}
</div>
