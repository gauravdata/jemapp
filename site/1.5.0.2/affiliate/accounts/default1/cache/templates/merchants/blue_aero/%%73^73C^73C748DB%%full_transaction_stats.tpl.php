<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:49
         compiled from full_transaction_stats.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'full_transaction_stats.tpl', 2, false),)), $this); ?>
<!-- full_transaction_stats -->
<?php echo smarty_function_localize(array('str' => 'You have generated'), $this);?>
&nbsp;<?php echo "<div id=\"totalCommisions\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'in commissions from'), $this);?>
&nbsp;<?php echo "<div id=\"totalSales\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'total sales.'), $this);?>

<br/>
<?php echo smarty_function_localize(array('str' => 'You have'), $this);?>
&nbsp;<?php echo "<div id=\"totalPendingCommisions\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'in'), $this);?>
&nbsp;<?php echo "<div id=\"countPendingCommissions\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'pending commissions.'), $this);?>

<br/><br/>
<?php echo smarty_function_localize(array('str' => 'You have'), $this);?>
&nbsp;<?php echo "<div id=\"totalUnpaidApprovedComm\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'in'), $this);?>
&nbsp;<?php echo "<div id=\"countUnpaidApprovedComm\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'unpaid approved commissions.'), $this);?>
