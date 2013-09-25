<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:06
         compiled from affiliate_tracking_code_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_tracking_code_filter.tpl', 6, false),)), $this); ?>
<!-- affiliate_tracking_code_filter -->
    
   			<div class="AffiliateTrackingCodeFilter">      			          
           
        			<fieldset class="Filter FilterCampaign"> 
                	<legend><?php echo smarty_function_localize(array('str' => 'Campaign'), $this);?>
</legend>
                	<div class="Resize">
                	<?php echo "<div id=\"campaignid\"></div>"; ?>
                	</div>
                	</fieldset>   
                
       		    
       		    	<fieldset class="Filter FilterStatus">
					<legend><?php echo smarty_function_localize(array('str' => 'Status'), $this);?>
</legend>
					<div class="Resize">
            		<?php echo "<div id=\"rstatus\"></div>"; ?>
            		</div>
            		</fieldset>  
      		</div>        
        
        	<div style="clear: both;"></div>
   