<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:06
         compiled from affiliate_stats.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_stats.tpl', 2, false),)), $this); ?>
<!-- full_affiliate_stats -->
<?php echo smarty_function_localize(array('str' => 'You have'), $this);?>
&nbsp;<?php echo "<div id=\"countAffiliates\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'affiliates in your program.'), $this);?>

<br/>
<?php echo "<div id=\"approvedAffiliates\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'approved'), $this);?>
&nbsp;/&nbsp;<?php echo "<div id=\"pendingAffiliates\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'pending'), $this);?>
.
<br/>
<?php echo smarty_function_localize(array('str' => 'Total (unpaid) commission generated'), $this);?>
&nbsp;<?php echo "<div id=\"approvedCommissions\"></div>"; ?>&nbsp;/&nbsp;<?php echo "<div id=\"pendingCommissions\"></div>"; ?>.
<br/>
<?php echo smarty_function_localize(array('str' => 'Total sales generated'), $this);?>
&nbsp;<?php echo "<div id=\"totalSales\"></div>"; ?>.