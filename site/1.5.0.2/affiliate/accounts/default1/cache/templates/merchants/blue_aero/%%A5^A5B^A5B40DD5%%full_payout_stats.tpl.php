<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:49
         compiled from full_payout_stats.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'full_payout_stats.tpl', 2, false),)), $this); ?>
<!-- full_payout_stats -->
<?php echo smarty_function_localize(array('str' => 'You have paid'), $this);?>
&nbsp;<?php echo "<div id=\"paid\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'in commissions.'), $this);?>

<br/>
<?php echo smarty_function_localize(array('str' => 'You have'), $this);?>
&nbsp;<?php echo "<div id=\"unpaidApprovedComm\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'in unpaid approved commissions.'), $this);?>

<br/>
<?php echo smarty_function_localize(array('str' => 'You have'), $this);?>
&nbsp;<?php echo "<div id=\"unpaidPendingComm\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'in unpaid pending commission.'), $this);?>

<br/><br/>
<?php echo smarty_function_localize(array('str' => 'You have'), $this);?>
&nbsp;<?php echo "<div id=\"unpaidDeclinedComm\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'in unpaid declined commission.'), $this);?>
