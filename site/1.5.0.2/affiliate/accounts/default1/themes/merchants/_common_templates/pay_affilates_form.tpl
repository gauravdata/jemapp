<!-- pay_affilates_form -->

<fieldset>
<legend>##Notes about this payment##</legend>
		<div class="Inliner">##Merchant note##</div>
	<div class="clear"></div>
		<div class="PayAffiliatesTextArea">{widget id="paymentNote"}</div>
	<div class="clear"></div>
		<div class="Inliner">##Affiliate note (visible to affiliate)##</div>
	<div class="clear"></div>
		<div class="PayAffiliatesTextArea">{widget id="affiliateNote"}</div>
	<div class="clear"></div>
</fieldset>

<fieldset>
<legend>##Payout information email##</legend>
	{widget id="send_payment_to_affiliate"}
	{widget id="send_generated_invoices_to_merchant"}
	{widget id="send_generated_invoices_to_affiliates"}
</fieldset>

<fieldset>
<legend>##MassPay export files##</legend>
##Here you can download export files for all payouts grouped by payout option.##
{widget id="filesList" class="ExportFilesList"}
</fieldset>

<div style="clear: both;"></div>
{widget id="sendButton"}
