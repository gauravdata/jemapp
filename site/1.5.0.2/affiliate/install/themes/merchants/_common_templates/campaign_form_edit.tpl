<!-- campaign_form_edit -->

<div class="Details">
<fieldset>
<legend>##Details##</legend>
{widget id="name"}
{widget id="logourl" class="CampaignLogo"}
{widget id="description"}
{widget id="longdescription"}
</fieldset>
</div>


<div class="CampaignStatus">
<fieldset>
<legend>##Campaign status##</legend>
<div class="CampaignFormEdit_CampaignStatus">{widget id="rstatus"}</div>
</fieldset>
</div>

{widget id="accountid"}

{widget id="rtype"}

<div class="Cookies">
<fieldset>
<legend>##Cookies##</legend>
{widget id="cookielifetime"}
<div class="Line"></div>
{widget id="overwritecookie" class="OCookies}
</fieldset>
</div>

<div class="ProductId">
<fieldset>
<legend>##Product ID matching##</legend>
{widget id="productid" class="CampaignProductId"}
</fieldset>
</div>

{widget id="campaignDetailsAdditionalForm"}
{widget id="campaignDetailsFeaturesPlaceholder"}

{widget id="FormMessage"}<br/>
{widget id="SaveButton"} {widget id="NextButton"}

<div class="clear"></div>
