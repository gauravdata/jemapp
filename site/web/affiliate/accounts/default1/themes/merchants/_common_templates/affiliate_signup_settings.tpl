<!-- affiliate_signup_settings -->

<div class="TabDescription">
<h3>##Affiliate signup##</h3>
##General configuration of your signup form. Write the Terms & Conditions of your affiliate program, choose if to display payout option and what to do after signup.##
</div>

<div class="AffiliateSignupForm">
<fieldset>
<legend>##Affiliate approval##</legend>
{widget id="affiliate_approval"}
</fieldset>

<fieldset>
    <legend>##Assign non-referred affiliate to##</legend>
    {widget id="assignAffiliateTo"}
</fieldset>


<div class="AffiliateSignupAfter">
<fieldset>
<legend>##After signup##</legend>
##What to do after signup?##
{widget id="postSignupType" class="SignUrl"}
</fieldset>
</div>

<fieldset>
<legend>##Terms & conditions##</legend>
##Set up Terms & conditions for your affiliate program## 
{widget id="forceTermsAcceptance"}
<div class="Line"></div>
{widget id="termsAndConditions" class="TermsAndConditions"}
</fieldset>
    
<fieldset>
<legend>##Payout option##</legend>
##Check if you want to display payout options in your signup form## 
{widget id="includePayoutOptions"}
<div class="Line"></div>
{widget id="forcePayoutOption"}
<div class="Line"></div>
{widget id="payoutOptions"}
</fieldset>
{widget id="reCatpchaSettings"}

{widget id="forcedMatrix"}
    
{widget id="FormMessage"}
{widget id="SaveButton"}
<div class="clear"></div>
