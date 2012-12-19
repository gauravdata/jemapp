<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:49
         compiled from fee_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'fee_panel.tpl', 5, false),)), $this); ?>
<!--	fee_panel	-->

<table>
	<tr>
		<td><?php echo "<div id=\"fixedFeeCheckbox\"></div>"; ?> <?php echo smarty_function_localize(array('str' => 'Fixed'), $this);?>
</td>
		<td><?php echo "<div id=\"fixedFeeValue\"></div>"; ?></td>
		<td>&nbsp;</td>		
	</tr>
	<tr>
		<td><div class="FeePercentageCheckBox"><?php echo "<div id=\"percentageCheckBox\"></div>"; ?> <?php echo smarty_function_localize(array('str' => 'Percentage from commission'), $this);?>
</div></td>
		<td><?php echo "<div id=\"percentageValue\"></div>"; ?></td>
		<td><?php echo "<div id=\"percentageFeeValue\"></div>"; ?></td>
	</tr>	
</table>