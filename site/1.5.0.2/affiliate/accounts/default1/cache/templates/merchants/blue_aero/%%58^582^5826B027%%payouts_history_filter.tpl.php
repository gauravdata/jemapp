<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:21
         compiled from payouts_history_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'payouts_history_filter.tpl', 6, false),)), $this); ?>
<!-- payouts_history_filter -->

		<div class="PayoutsHistoryFilter">		

			<fieldset class="Filter FilterDate">
			<legend><?php echo smarty_function_localize(array('str' => 'Date paid'), $this);?>
</legend>
			<div class="Resize">
			<?php echo "<div id=\"dateinserted\"></div>"; ?>
			</div>
			</fieldset>

			<fieldset class="Filter FiterMerchantNote">
			<legend><?php echo smarty_function_localize(array('str' => 'Merchant note'), $this);?>
</legend>
			<div class="Resize">
			<?php echo "<div id=\"merchantnote\"></div>"; ?>
			</div>
			</fieldset>
			
		
		</div>


		<div style="clear: both;"></div>