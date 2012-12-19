<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:21
         compiled from pay_affilates_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'pay_affilates_form.tpl', 4, false),)), $this); ?>
<!-- pay_affilates_form -->

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Notes about this payment'), $this);?>
</legend>
		<div class="Inliner"><?php echo smarty_function_localize(array('str' => 'Merchant note'), $this);?>
</div>
	<div class="clear"></div>
		<div class="PayAffiliatesTextArea"><?php echo "<div id=\"paymentNote\"></div>"; ?></div>
	<div class="clear"></div>
		<div class="Inliner"><?php echo smarty_function_localize(array('str' => 'Affiliate note (visible to affiliate)'), $this);?>
</div>
	<div class="clear"></div>
		<div class="PayAffiliatesTextArea"><?php echo "<div id=\"affiliateNote\"></div>"; ?></div>
	<div class="clear"></div>
</fieldset>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Payout information email'), $this);?>
</legend>
	<?php echo "<div id=\"send_payment_to_affiliate\"></div>"; ?>
	<?php echo "<div id=\"send_generated_invoices_to_merchant\"></div>"; ?>
	<?php echo "<div id=\"send_generated_invoices_to_affiliates\"></div>"; ?>
</fieldset>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'MassPay export files'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'Here you can download export files for all payouts grouped by payout option.'), $this);?>

<?php echo "<div id=\"filesList\" class=\"ExportFilesList\"></div>"; ?>
</fieldset>

<div style="clear: both;"></div>
<?php echo "<div id=\"sendButton\"></div>"; ?>