<!-- tracking_tab -->

<div class="TrackingSettingsForm">

<fieldset>
    <legend>##URLs##</legend>
    {widget id="mainSiteUrl" class="MainSiteUrl"}
</fieldset>

<fieldset>
    <legend>##Clicks##</legend>
    <div class="Inliner"><div class="Label">##Delete raw click records older than##</div></div>
    <div class="FormFieldSmallInline">{widget id="deleterawclicks"}</div><div class="Inliner">##days##</div>
    <div class="clear"></div>
</fieldset>

<fieldset>
    <legend>##Tracking##</legend>
    {widget id="track_by_ip"}
    {widget id="ip_validity" class="IpValidity"}
    {widget id="ip_validity_format" class="Validity"}
    <div class="Line"></div>
    {widget id="save_unrefered_sale_lead" class="SaveUnrefered"}
    {widget id="default_affiliate"}
    <div class="Line"></div>
    {widget id="force_choosing_productid"}
    <div class="Line"></div>
    {widget id="deleteExpiredVisitors"}
    <div class="Line"></div>
    {widget id="allowComputeNegativeCommission"}
</fieldset>

<fieldset>
    <legend>##Affiliate linking method##</legend>
    ##This will set the style of affiliate link URL that your affiliates will put to their pages. You can choose from the methods below.<br/>Note that some methods have different requirements##
    
    {widget id="linking_method" class="LinkingMethod"}
    
    
    <div class="Line"></div>
    <div class="HintText">##You can choose to support DirectLink linking as an addition to your standard affiliate links.<br/>The links chosen above will work, plus your affiliates will have option to use DirectLinks. All affiliate DirectLink URLs require merchant's approval.##</div>
    <div class="Line"></div>
    
    {widget id="support_direct_linking" class="SupportDirect"}
    
    {widget id="support_short_anchor_linking"}
</fieldset>

</div>
{widget id="SaveButton"}

<div class="clear"></div>
