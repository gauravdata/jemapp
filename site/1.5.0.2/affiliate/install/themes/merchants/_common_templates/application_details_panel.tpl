<!-- application_details_panel -->
<div class="ApplicationDetailsPanel">
    <div class="ApplicationMainInfo">
        <h2>{$postAffiliatePro}</h2>
        <h4>{widget id="variation"}</h4>
        <div class="ProductOf">##Product of## <a href="{$qualityUnitBaseLink}" target="_blank" class="ProductOfCompany">{$qualityUnit}</a></div>
    </div>  
    <div class="ApplicationVersionInfo">
        <div class="ApplicationVersionInfoLine">##Version##: {widget id="version" class="ActualVersion"}</div>
        <div class="ApplicationVersionInfoLine">{widget id="changelogLink"}</div>
        <div class="ApplicationVersionInfoLine">{widget id="checkForNewVersion"}</div>
        <div class="ApplicationVersionInfoLine">{widget id="License"}</div>
    </div>
    <div class="ClearBoth"></div>
</div>
