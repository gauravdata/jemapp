<!--	account_signup_settings_form	-->

{widget id="SignupFormUrl"}
<div class="clear"></div>
<fieldset>
	<legend>##Account approval##</legend>
	{widget id="account_approval"}
</fieldset>

<fieldset>
	<legend>##After signup##</legend>
	{widget id="account_post_signup_type" class="SignUrl"}	
</fieldset>

<fieldset>
    <legend>##Default Merchant Agreement##</legend>
    <div class="AccountDetailsAgreement">
    {widget id="forceMerchantAgreementAcceptance"}
    {widget id="merchant_agreement"}
    </div>  
</fieldset>

{widget id="formMessage"}
{widget id="saveButton"}
