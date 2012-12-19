<!-- commissions_edit_with_fixedcost -->
<div class="CommissionEditWithFixedCostTopExtensionPanel">
    {widget id="FeatureTopExtensionFormPanel"}
</div>
<fieldset>
<legend>##Commission type settings##</legend>
    {widget id="code"}
    {widget id="name"}
    {widget id="approval" class="Approval"}
    {widget id="zeroorderscommission" class="ZeroOrdersCommissions"}
    {widget id="savezerocommission" class="ZeroOrdersCommissions"}
    {widget id="useFixedCost"}{widget id="fixedCostHelp"}
    {widget id="FixedCost"}

</fieldset>

<fieldset>
<legend>##Commissions##</legend>
{widget id="NormalCommissionValues"}
</fieldset>

{widget id="FeatureExtensionFormPanel"}

{widget id="PluginExtensionFormPanel"}

{widget id="FormMessage"}
{widget id="SaveButton"} {widget id="CloseButton"}
