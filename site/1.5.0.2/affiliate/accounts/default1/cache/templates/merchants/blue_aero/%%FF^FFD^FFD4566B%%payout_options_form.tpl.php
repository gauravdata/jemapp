<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:21
         compiled from payout_options_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'payout_options_form.tpl', 5, false),)), $this); ?>
<!-- payout_options_form -->

<div class="PayoutsOptionsForm">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Default payout method for affiliates'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'Default payout method'), $this);?>
<?php echo "<div id=\"defaultPayoutMethod\"></div>"; ?>
</fieldset>
</div>

<?php echo "<div id=\"PayoutOptionsGrid\"></div>"; ?>
<?php echo "<div id=\"saveButton\"></div>"; ?>
<div class="clear"></div>