<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:31
         compiled from quick_report_filter_base.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'quick_report_filter_base.tpl', 7, false),)), $this); ?>
<!-- quick_report_filter_base -->

			<div class="ReportsFilter">
			
			<div class="ColumnFieldset">
				<fieldset class="Filter FilterChannel">
                    <legend><?php echo smarty_function_localize(array('str' => 'Channel'), $this);?>
</legend>
                    <div class="Resize">
                    <?php echo "<div id=\"channel\"></div>"; ?>
                    </div>
   				</fieldset>    
    
  				<fieldset class="Filter FilterCampaign">
    			<legend><?php echo smarty_function_localize(array('str' => 'Campaign'), $this);?>
</legend>
    			<div class="Resize">
    			<?php echo "<div id=\"campaignid\"></div>"; ?>
    			</div>
    			</fieldset>
    
    		</div>
    		
    		<fieldset class="Filter FilterCampaign">
    			<legend><?php echo smarty_function_localize(array('str' => 'Destination URL'), $this);?>
</legend>
    			<div class="Resize">
    				<?php echo "<div id=\"destinationurl\"></div>"; ?>
    			</div>
    		</fieldset>        		
    		  			
    			<fieldset class="Filter FilterDate">
	    			<legend><?php echo smarty_function_localize(array('str' => 'Date'), $this);?>
</legend>
	    			<div class="Resize">
	    			<?php echo "<div id=\"datetime\"></div>"; ?>
	    			</div>
    			</fieldset>
				
			</div>  		
				
			<div class="clear"></div>	


