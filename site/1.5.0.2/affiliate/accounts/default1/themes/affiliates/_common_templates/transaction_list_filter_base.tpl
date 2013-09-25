<!-- transaction_list_filter_base -->

			<div class="TransactionsFilter">
				<div class="ColumnFieldset">
					<fieldset class="Filter FilterChannel">
	                    <legend>##Channel##</legend>
	                    <div class="Resize">
	                    {widget id="channelValue"}
	                    </div>
	   				</fieldset>    
	    
	  				<fieldset class="Filter FilterCampaign">
	    			<legend>##Campaign##</legend>
	    			<div class="Resize">
	    			{widget id="campaignid"}
	    			</div>
	    			</fieldset>
	    
	    		</div>
				 <fieldset class="Filter FilterDate">
                        <legend>##Date created##</legend>
                        <div class="Resize">
                            {widget id="dateinserted"}
                        </div>
                </fieldset>   
            
           	
            	<div class="ColumnFieldset">
			
					<fieldset class="Filter">
					<legend>##Status##</legend>
					<div class="Resize">
            		{widget id="rstatus"}
            		</div>
            		</fieldset>
            
            		<fieldset class="Filter">
					<legend>##Type##</legend>
					<div class="Resize">
            		{widget id="rtype"}
            		</div>
            		</fieldset>
            
            	</div>

            	<div class="ColumnFieldset">
	            	<fieldset class="Filter"> 
	            		<legend>##Order ID##</legend>
	            		<div class="Resize">
	            		{widget id="orderId"}
	            		</div>
	            		</fieldset>
            	
	            	<fieldset class="Filter">
	            	<legend>##Custom##</legend>
	            	<div class="Resize">
	            	{widget id="custom"}
	            	</div>
	            	</fieldset>
            	</div>
            </div>
            
            <div style="clear: both;"></div>
