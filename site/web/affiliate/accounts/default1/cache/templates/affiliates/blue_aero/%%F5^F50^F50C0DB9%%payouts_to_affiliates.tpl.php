<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:31
         compiled from payouts_to_affiliates.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'payouts_to_affiliates.tpl', 5, false),)), $this); ?>
<!-- payouts_to_affiliates -->
<?php echo "<div id=\"financialOverview\"></div>"; ?>

<br/>
<h3><?php echo smarty_function_localize(array('str' => 'History of payouts to me'), $this);?>
</h3> 
<?php echo "<div id=\"grid\"></div>"; ?>