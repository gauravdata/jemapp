<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from accounting_stats_data.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'accounting_stats_data.tpl', 5, false),)), $this); ?>
<!--	accounting_stats_data	-->

<table>
	<tr>
		<td><?php echo smarty_function_localize(array('str' => 'Commissions'), $this);?>
 <?php echo "<div id=\"commissions\"></div>"; ?></td>
		<td><?php echo smarty_function_localize(array('str' => 'Fees'), $this);?>
 <?php echo "<div id=\"fees\"></div>"; ?></td>
		<td><?php echo smarty_function_localize(array('str' => 'Payments'), $this);?>
 <?php echo "<div id=\"payments\"></div>"; ?></td>
		<td><?php echo smarty_function_localize(array('str' => 'Ballance'), $this);?>
 <?php echo "<div id=\"ballance\"></div>"; ?></td>
	</tr>
</table>