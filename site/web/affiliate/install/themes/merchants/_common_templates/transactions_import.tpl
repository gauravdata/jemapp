<!-- affiliate_fields -->
<div class="TabDescription">
<h3>##Available fields for affiliate data##</h3>
##Choose which fields you want to use to store data for your affiliates. Some fields are mandatory, and you have up to 25 optional fields for which you can decide what information they will keep, if they will be optional, mandatory, or not displayed at all. These fields will be displayed in affiliate signup form and affiliate profile editation.##
</div>
<div class="TransactionImport">
    <table>
        <tr>
            <td valign='top'>
    			<div class="TransactionImportFields FloatLeft">
        			<fieldset>
            			<legend>##Import file format##</legend>
            			{widget id="fields"}
                        {widget id="addButton"}
        			</fieldset>
    			</div>
            </td>
            <td valign='top' >
                <div class="TransactionImportFile FloatLeft">
                    <fieldset>
                        <legend>##Import file##</legend>
                        {widget id="delimiter"}
                        {widget id="source" class="ImportRadioGroup"}
                        {widget id="url"}
                        {widget id="uploadFile"}
                        {widget id="exportFilesGrid"} 
                        {widget id="serverFile"}
                        {widget id="skipFirstRow"}
                        {widget id="transactionType"}
                        {widget id="importButton"}
                    </fieldset>
                </div>
                <div class="clear">
                <div class="TransactionImportSettings FloatLeft">
                    <fieldset>
                        <legend>##Transaction import settings##</legend>
                        {widget id="computeAtomaticaly"}
                        {widget id="matchTransaction"} 
                        {widget id="transactionStatus"}
                    </fieldset>
                </div>
            </td>
        </tr>
    </table>
</div>
