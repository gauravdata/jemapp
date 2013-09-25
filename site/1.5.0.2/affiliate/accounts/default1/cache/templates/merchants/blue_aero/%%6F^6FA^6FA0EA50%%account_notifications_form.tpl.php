<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from account_notifications_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'account_notifications_form.tpl', 4, false),)), $this); ?>
<!--	account_notifications_form		-->

<fieldset>
	<legend><?php echo smarty_function_localize(array('str' => 'Email notifications for account'), $this);?>
</legend>
	<h4><?php echo smarty_function_localize(array('str' => 'Actions'), $this);?>
</h4>
	<?php echo "<div id=\"acc_notif_on_new_account_signup_before_approval\"></div>"; ?>
	<?php echo "<div id=\"account_notification_on_request_payment\"></div>"; ?>
	<?php echo "<div id=\"account_notification_on_invoice_paid\"></div>"; ?>
	<?php echo "<div id=\"account_notification_on_suspended\"></div>"; ?>
	<?php echo "<div id=\"account_notification_on_declined\"></div>"; ?>
</fieldset>
<?php echo "<div id=\"formMessage\"></div>"; ?>
<?php echo "<div id=\"saveButton\"></div>"; ?>