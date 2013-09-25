<!-- fraud_protection_sales -->

<div class="FraudProtection">
	<div class="Inliner">{widget id="duplicate_orders_ipInput"}</div>
	<div class="Inliner">##Recognize  duplicate orders comming from same IP address within##</div>
	<div class="FormFieldSmallInline">{widget id="duplicate_orders_ip_seconds"}</div>
	<div class="Inliner">##seconds.##</div>
	<div class="clear"></div>
	<div class="FraudProtectionAction">
		<div class="Inliner FraudProtectionActionLabel">##What to do with these orders##</div>
		<div class="Inliner" style="width: 150px;">{widget id="duplicate_orders_ip_action"}</div>
		<div class="ClearBoth"></div>
		<div class="InlinerLabel">{widget id="duplicate_orders_ip_message"}</div>
		<div class="ClearBoth"></div>
	    <div class="InlinerLabel">{widget id="duplicate_orders_ip_samecampaign"}</div>
	    <div class="ClearBoth"></div>
	    <div class="InlinerLabel">{widget id="duplicate_orders_ip_sameorderid"}</div>
	    <div class="ClearBoth"></div>
	</div>
	
	<div class="Line" ></div>
	
	<div class="clear"></div>
	<div class="Inliner">{widget id="duplicate_orders_idInput"}</div>
	<div class="Inliner">##Recognize duplicate orders comming with the same order ID within##</div>
	<div class="Inliner">{widget id="duplicate_order_id_hours"}</div>
	<div class="Inliner">##hours from initial sale.##</div>
	<div class="clear"></div>
	<div class="Inliner">{widget id="aplly_to_empty_orders_idInput"}</div>
	<div class="Inliner">##Apply also to empty OrderIDs.##</div>
	<div class="clear"></div>
	<div class="FraudProtectionAction">
		<div class="Inliner FraudProtectionActionLabel">##What to do with these orders##</div>
		<div class="Inliner" style="width: 150px;">{widget id="duplicate_orders_id_action"}</div>
		<div class="ClearBoth"></div>
		<div class="InlinerLabel">{widget id="duplicate_orders_id_message"}</div>
		<div class="ClearBoth"></div>
	</div>
		
	<div class="Line" ></div>
	
	<div class="Inliner">{widget id="bannedips_salesInput"}</div>
	<div class="Inliner">##Ban sales from IP addresses##</div>
	<div class="Inliner">{widget id="bannedips_list_sales" class="BannedIps"}</div>	
	<div class="clear"></div>
	<div class="FraudProtectionAction">
		<div class="Inliner FraudProtectionActionLabel">##What to do with these orders##</div>
		<div class="Inliner">{widget id="bannedips_sales_action"}</div>
		<div class="ClearBoth"></div>
		<div class="InlinerLabel">{widget id="bannedips_sales_message"}</div>
		<div class="ClearBoth"></div>
	</div>

	<div class="Line" ></div>
	
	{widget id="FraudFeaturesPanel"}
</div>
