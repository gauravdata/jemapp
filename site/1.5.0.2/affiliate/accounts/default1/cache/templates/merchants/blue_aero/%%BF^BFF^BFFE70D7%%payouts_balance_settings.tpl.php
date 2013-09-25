<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:21
         compiled from payouts_balance_settings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'payouts_balance_settings.tpl', 5, false),)), $this); ?>
<!-- payout_general_settings -->

<div class="PayoutsBalanceForm">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Minimum payout balances'), $this);?>
</legend>

<?php echo "<div id=\"payoutOptions\"></div>"; ?>
<div class="Line"></div>
<div class="HintText"><?php echo smarty_function_localize(array('str' => 'Affiliates will be able to choose only from these payout balance options.'), $this);?>
</div>

<div class="Line"></div>

<?php echo "<div id=\"minimumPayout\"></div>"; ?>
</fieldset>
</div>

<?php echo "<div id=\"FormMessage\"></div>"; ?>
<?php echo "<div id=\"SaveButton\"></div>"; ?>
<div class="clear"></div>