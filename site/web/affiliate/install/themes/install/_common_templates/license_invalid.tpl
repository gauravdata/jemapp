<!-- license_invalid -->
<div id="Container">
	<div id="InvalidLicenseContainer">
		{include file="header.tpl"}
		<div id="Content">
			<div class="InvalidLicense">
				<div class="InvalidLicenseIcon">##Invalid License.##</div>
				<div class="InvalidLicenseText">
				    Your license is invalid or expired. There may be several reasons for this:<div class="clear"></div><br/>
				    1. You have moved your installation to different url.<div class="clear"></div></br/>
			    	2. Your license id has changed or expired.<div class="clear"></div><br/><br/>
			    	{widget id="RevalidateLicenseButton"}<div class="clear"></div><br/><br/></br/>
			    	Set new license id {widget id="NewLicenseId"} {widget id="UpdateLicenseButton"}    <div class="clear"></div><br/><br/></br/>
			    	If you believe this is an error, contact us at <a href="{$qualityUnitBaseLink}">{$qualityUnitBaseLink}</a>
				</div>
			</div>
		</div>
		{include file="footer.tpl"}
	</div>
</div>
