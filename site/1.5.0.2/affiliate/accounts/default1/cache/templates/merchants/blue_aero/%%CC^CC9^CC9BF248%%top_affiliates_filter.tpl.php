<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:43
         compiled from top_affiliates_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'top_affiliates_filter.tpl', 4, false),)), $this); ?>
<!--	top_affiliates_filter	-->
<div>
	<fieldset class="Filter">
		<legend><?php echo smarty_function_localize(array('str' => 'Statistics date range'), $this);?>
</legend>
		<div class="Resize"><?php echo "<div id=\"statsdaterange\"></div>"; ?></div>
	</fieldset>

	<fieldset class="Filter">
		<legend><?php echo smarty_function_localize(array('str' => 'Campaign'), $this);?>
</legend>
		<div class="Resize"><?php echo "<div id=\"campaignid\"></div>"; ?></div>
	</fieldset>
	
	<fieldset class="Filter">
		<legend><?php echo smarty_function_localize(array('str' => 'Transaction status'), $this);?>
</legend>
		<div class="Resize"><?php echo "<div id=\"transactionstatus\"></div>"; ?></div>
	</fieldset>	
	
    <fieldset class="Filter">
        <legend><?php echo smarty_function_localize(array('str' => 'Custom filter'), $this);?>
</legend>
        <div class="Resize"><?php echo "<div id=\"custom\"></div>"; ?></div>
    </fieldset>	
</div>
<div style="clear: both;"></div>