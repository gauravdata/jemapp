<?php /* Smarty version 2.6.18, created on 2011-08-17 14:21:53
         compiled from clicks_list_filter_base.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'clicks_list_filter_base.tpl', 5, false),)), $this); ?>
 <!-- clicks_list_filter_base -->
<div class="RawClicksFilter">
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
	<fieldset class="Filter FilterDate">
		<legend><?php echo smarty_function_localize(array('str' => 'Date'), $this);?>
</legend>
		<div class="Resize">
			<?php echo "<div id=\"datetime\"></div>"; ?>
		</div>
	</fieldset>  
 
	<fieldset class="Filter">
		<legend><?php echo smarty_function_localize(array('str' => 'Type'), $this);?>
</legend>
		<div class="Resize">
			<?php echo "<div id=\"rtype\"></div>"; ?>
		</div>
	</fieldset>
	<fieldset class="Filter">
		<legend><?php echo smarty_function_localize(array('str' => 'Custom'), $this);?>
</legend>
		<div class="Resize">
			<?php echo "<div id=\"custom\"></div>"; ?>
		</div>
	</fieldset>
</div>
<div class="clear"></div>
    