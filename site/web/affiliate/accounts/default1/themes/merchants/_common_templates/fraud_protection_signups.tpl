<!-- fraud_protection_signups -->

<div class="FraudProtection">
	<div class="Inliner">{widget id="repeating_signupsInput"}</div><div class="Inliner"><div class="Label">##Recognize multiple signups that come from the same IP address within##</div></div>
	<div class="FormFieldSmallInline">{widget id="repeating_signups_seconds"}</div> <div class="Inliner">## seconds.##</div>
	<div class="clear"></div>
	<div class="FraudProtectionAction">
		<div class="Inliner FraudProtectionActionLabel">##What to do with these signups##</div><div class="Inliner">{widget id="repeating_signups_actionInput"}</div>
		<div class="ClearBoth"></div>
	</div>
		
	<div class="Line" ></div>
	
	<div class="Inliner">{widget id="bannedips_signupsInput"}</div><div class="Inliner"><div class="Label">##Don't allow signups from following IP addresses##</div></div>
	<div class="Inliner">{widget id="bannedips_list_signups" class="BannedIps"}</div> <div class="Inliner"></div>
	<div class="clear"></div>
	<div class="FraudProtectionAction">
		<div class="Inliner FraudProtectionActionLabel">##What to do with these signups##</div><div class="Inliner">{widget id="bannedips_signups_actionInput"}</div>
		<div class="ClearBoth"></div>
	</div>

	<div class="Line" ></div>
	
	{widget id="FraudFeaturesPanel"}
</div>
