<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:06
         compiled from banner_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'banner_filter.tpl', 6, false),)), $this); ?>
<!-- banner_filter -->
          
         		<div class="BannersFilter">
          
                <fieldset class="Filter FilterDate">
                <legend><?php echo smarty_function_localize(array('str' => 'Statistics date range'), $this);?>
</legend>
                <div class="Resize">
                <?php echo "<div id=\"date\"></div>"; ?>
                </div> 
                </fieldset>
                
    			<fieldset class="Filter">
            	<legend><?php echo smarty_function_localize(array('str' => 'Campaign'), $this);?>
</legend>
            	<div class="Resize">
            	<?php echo "<div id=\"campaignid\" class=\"CampaignId\"></div>"; ?>
            	</div> 
            	</fieldset>
            
            	<fieldset class="Filter">            
            	<legend><?php echo smarty_function_localize(array('str' => 'Banner type'), $this);?>
</legend>
            	<div class="Resize">
            	<?php echo "<div id=\"type\"></div>"; ?>
            	</div>
            	</fieldset>
            
            	<fieldset class="Filter">
            	<legend><?php echo smarty_function_localize(array('str' => 'Target url'), $this);?>
</legend>
            	<div class="Resize">
            	<?php echo "<div id=\"destinationurl\" class=\"TargetUrlFilter\"></div>"; ?>
            	</div>
            	</fieldset>
            	
            	<fieldset class="Filter">
            	<legend><?php echo smarty_function_localize(array('str' => 'Hidden banners'), $this);?>
</legend>
            	<div class="Resize">
            	<?php echo "<div id=\"rstatus\"></div>"; ?><strong><?php echo smarty_function_localize(array('str' => 'Show hidden banners'), $this);?>
</strong>
            	</div>
            	</fieldset>
            </div>      
                  
       		<div style="clear: both;"></div> 

    
    