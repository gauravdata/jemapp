<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:17
         compiled from campaign_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'campaign_filter.tpl', 6, false),)), $this); ?>
<!-- campaign_filter -->
    
   			<div class="CampaignsFilter">   
   			 
     			<fieldset class="Filter">        
            	<legend><?php echo smarty_function_localize(array('str' => 'Date created'), $this);?>
</legend>
            	<div class="Resize">
            	<?php echo "<div id=\"dateinserted\"></div>"; ?>
            	</div>
       			</fieldset>  
       		   <?php echo "<div id=\"additionalFilters\"></div>"; ?>      
      		</div>
            <div class="CampaignsFilter">   
             
                <fieldset class="Filter">        
                <legend><?php echo smarty_function_localize(array('str' => 'Invisible Campaigns'), $this);?>
</legend>
                <div class="Resize">
                <?php echo "<div id=\"rstatus\"></div>"; ?><strong><?php echo smarty_function_localize(array('str' => 'Show invisible campaigns'), $this);?>
</strong>
                </div>
                </fieldset>  
            </div>          
            
        	<div style="clear: both;"></div>
   