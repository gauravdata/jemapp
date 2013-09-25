<!-- fraud_protection_clicks -->

<div class="FraudProtection">
    {widget id="bannedips_clicks_from_iframe"}
	<div class="Inliner">{widget id="repeating_clicksInput"}</div><div class="Inliner"><div class="Label">##Recognize multiple repeating clicks that come from the same IP address within##</div></div>
	<div class="FormFieldSmallInline">{widget id="repeating_clicks_seconds"}</div> <div class="Inliner">## seconds.##</div>
	<div class="clear"></div>
	<div class="FraudProtectionAction">
        <div class="Inliner FraudProtectionActionLabel">##What to do with these clicks##</div><div class="Inliner">{widget id="repeating_clicks_action"}</div>
        <div class="ClearBoth"></div>
    </div>
    <div class="clear"></div>    
	<div class="Inliner">{widget id="repeating_banner_clicksInput"}</div><div class="Inliner">##Click from same IP, but on different banners, don't recognize as repeating click##</div>		
	
	<div class="Line" ></div>
	
	<div class="Inliner">{widget id="bannedips_clicksInput"}</div><div class="Inliner"><div class="Label">##Ban clicks from IP addresses##</div></div>
	<div class="Inliner">{widget id="bannedips_list_clicks" class="BannedIps"}</div> <div class="Inliner"></div>
	<div class="clear"></div>
	<div class="FraudProtectionAction">
		<div class="Inliner FraudProtectionActionLabel">##What to do with these clicks##</div><div class="Inliner">{widget id="bannedips_clicks_action"}</div>
		<div class="ClearBoth"></div>
	</div>

	<div class="Line" ></div>
	
	{widget id="FraudFeaturesPanel"}

</div>
