<?php /* Smarty version 2.6.18, created on 2011-08-17 14:21:51
         compiled from banner_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'banner_filter.tpl', 6, false),)), $this); ?>
<!-- banner_filter -->
<div class="BannersFilter">

		<div class="ColumnFieldset">
  				<fieldset class="Filter FilterCampaign">
	    			<legend><?php echo smarty_function_localize(array('str' => 'Campaign'), $this);?>
</legend>
	    			<div class="Resize">
	    			<?php echo "<div id=\"campaignid\"></div>"; ?>
	    			</div>
    			</fieldset>		
				<fieldset class="Filter FilterChannel">
                    <legend><?php echo smarty_function_localize(array('str' => 'Channel'), $this);?>
</legend>
                    <div class="Resize">
                    <?php echo "<div id=\"channel\"></div>"; ?>
                    </div>
   				</fieldset>       
                <fieldset class="Filter FilterChannel">
                    <legend><?php echo smarty_function_localize(array('str' => 'Target url'), $this);?>
</legend>
                    <div class="Resize">
                    <?php echo "<div id=\"destinationurl\"></div>"; ?>
                    </div>
                </fieldset>
                <fieldset class="Filter FilterCustom">
                    <legend><?php echo smarty_function_localize(array('str' => 'Custom'), $this);?>
</legend>
                    <div class="Resize">
                    <?php echo "<div id=\"custom\"></div>"; ?>
                    </div>
                </fieldset>
		</div>
    <fieldset class="Filter FilterAdditionalData">
        <legend><?php echo smarty_function_localize(array('str' => 'Additional data'), $this);?>
</legend>
        <div class="Resize">
            <?php echo "<div id=\"displaystats\"></div>"; ?>
            <?php echo smarty_function_localize(array('str' => 'For date range'), $this);?>
 <?php echo "<div id=\"statsdate\"></div>"; ?>
            <?php echo "<div id=\"show_with_stats_only\"></div>"; ?>
        </div> 
     </fieldset>
     
     <fieldset class="Filter FilterBannerTypes">            
	   	<legend><?php echo smarty_function_localize(array('str' => 'Banner type'), $this);?>
</legend>
	      	<div class="Resize">
	          	<?php echo "<div id=\"type\"></div>"; ?>
	       	</div>
     </fieldset>

     <div class="clear"></div> 
	        <?php echo smarty_function_localize(array('str' => 'If you choose channel, the banner code will have channel tracking code included, and also statistics will be displayed for this channel'), $this);?>

     <div class="clear"></div> 
</div>    
	