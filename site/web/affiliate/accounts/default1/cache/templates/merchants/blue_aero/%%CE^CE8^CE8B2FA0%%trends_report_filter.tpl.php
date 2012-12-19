<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:43
         compiled from trends_report_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'trends_report_filter.tpl', 6, false),)), $this); ?>
<!-- trends_report_filter -->
    
            <div class="TrendReportsFilter">
                
                <fieldset class="Filter FilterCampaign">
                <legend><?php echo smarty_function_localize(array('str' => 'Campaign'), $this);?>
</legend>
                    <div class="Resize">
                        <?php echo "<div id=\"campaignid\"></div>"; ?>
                    </div>
                </fieldset>
     	  		
                <fieldset class="Filter FilterCampaign">
                <legend><?php echo smarty_function_localize(array('str' => 'Banner'), $this);?>
</legend>
                    <div class="Resize">
                        <?php echo "<div id=\"bannerid\"></div>"; ?>
                    </div>
                </fieldset>
     	  		
     	  		<fieldset class="Filter FilterCampaign">
                <legend><?php echo smarty_function_localize(array('str' => 'DestinationURL'), $this);?>
</legend>
                    <div class="Resize">
                        <?php echo "<div id=\"destinationurl\"></div>"; ?>
                    </div>
                </fieldset>         	  		
     	  		
     	  		<fieldset class="Filter FilterStatus">
                    <legend><?php echo smarty_function_localize(array('str' => 'Status'), $this);?>
</legend>
                    <div class="Resize">
                        <?php echo "<div id=\"rstatus\"></div>"; ?>
                    </div>
                </fieldset>
       		    <?php echo "<div id=\"additionalFilters\"></div>"; ?>
            </div>        
                               
            <div style="clear: both;"></div>
   