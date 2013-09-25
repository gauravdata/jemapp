<!-- affiliate_payout_settings -->
<table>
<tr><td valign="top">
		<fieldset>
		    <legend>##Payout method and data##</legend>
		    {widget id="payoutoptionid"}
		    {widget id="payoutOptions" class="PayoutOptions"}
		</fieldset>
		<fieldset>
		    <legend>##Payout balances##</legend>
            {widget id="minimumPayoutOptions"}
            {widget id="minimumpayout"}
        </fieldset>
		<fieldset>
		    <legend>##Invoicing options##</legend>
		    {widget id="invoicingNotSupported"}
		    {widget id="applyVatInvoicing"}
		    {widget id="vatPercentage"}
		    {widget id="vatNumber"}
		    {widget id="amountOfRegCapital"}
		    {widget id="regNumber"}
		</fieldset>
	</td><td valign="top">
	     <fieldset>
            <legend>##Payout history##</legend>
            {widget id="PayoutHistory"}
        </fieldset>
	</td></tr>
</table>
{widget id="FormMessage"}
{widget id="SaveButton"}
<div class="clear"></div>
