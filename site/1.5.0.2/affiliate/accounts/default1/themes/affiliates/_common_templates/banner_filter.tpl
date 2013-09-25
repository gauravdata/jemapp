<!-- banner_filter -->
<div class="BannersFilter">

		<div class="ColumnFieldset">
  				<fieldset class="Filter FilterCampaign">
	    			<legend>##Campaign##</legend>
	    			<div class="Resize">
	    			{widget id="campaignid"}
	    			</div>
    			</fieldset>		
				<fieldset class="Filter FilterChannel">
                    <legend>##Channel##</legend>
                    <div class="Resize">
                    {widget id="channel"}
                    </div>
   				</fieldset>       
                <fieldset class="Filter FilterChannel">
                    <legend>##Target url##</legend>
                    <div class="Resize">
                    {widget id="destinationurl"}
                    </div>
                </fieldset>
                <fieldset class="Filter FilterCustom">
                    <legend>##Custom##</legend>
                    <div class="Resize">
                    {widget id="custom"}
                    </div>
                </fieldset>
		</div>
    <fieldset class="Filter FilterAdditionalData">
        <legend>##Additional data##</legend>
        <div class="Resize">
            {widget id="displaystats"}
            ##For date range## {widget id="statsdate"}
            {widget id="show_with_stats_only"}
        </div> 
     </fieldset>
     
     <fieldset class="Filter FilterBannerTypes">            
	   	<legend>##Banner type##</legend>
	      	<div class="Resize">
	          	{widget id="type"}
	       	</div>
     </fieldset>

     <div class="clear"></div> 
	        ##If you choose channel, the banner code will have channel tracking code included, and also statistics will be displayed for this channel##
     <div class="clear"></div> 
</div>    
	
