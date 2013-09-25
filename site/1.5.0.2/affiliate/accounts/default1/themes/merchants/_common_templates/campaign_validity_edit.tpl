<!-- campaign_validity_edit -->
<div class="submenu">##Campaign capping description##</div>

<fieldset>
<legend>##Campaign Type##</legend>
{widget id="rtype"}
</fieldset>

<fieldset>
	<legend>##Campaign Validity##</legend>
	{widget id="rstatus"}
	{widget id="discontinueurl"}
</fieldset>

<fieldset>
  <legend>##Allowed countries##</legend>
  ##Choose countries for this campaign##
  {widget id="countries"}
  ##Country capping behavior##
  {widget id="geocampaigndisplay"}
  {widget id="geobannersshow"}
  {widget id="geotransregister"}
</fieldset>


{widget id="FormMessage"}<br/>
{widget id="SaveButton"}

