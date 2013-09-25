<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:49
         compiled from full_commision_stats.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'full_commision_stats.tpl', 2, false),)), $this); ?>
<!-- full_commision_stats -->
<?php echo smarty_function_localize(array('str' => 'You have generated'), $this);?>
&nbsp;<?php echo "<div id=\"totalCommisions\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'in commissions from'), $this);?>
&nbsp;<?php echo "<div id=\"totalSales\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'total sale value.'), $this);?>

<br/>
<?php echo smarty_function_localize(array('str' => 'You have'), $this);?>
&nbsp;<?php echo "<div id=\"totalPendingCommisions\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'pending commissions.'), $this);?>

<br/>
<?php echo smarty_function_localize(array('str' => 'You have'), $this);?>
&nbsp;<?php echo "<div id=\"totalUnpaidApprovedCommisions\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'in unpaid approved commissions.'), $this);?>
