<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:49
         compiled from email_notifications.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'email_notifications.tpl', 6, false),)), $this); ?>
<!-- email_notifications -->

<div class="EmailNotificationForm">

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Email notifications for merchant'), $this);?>
</legend>
<?php echo "<div id=\"merchant_notification_email\"></div>"; ?>
<br/><div class="Line"></div>
<h4><?php echo smarty_function_localize(array('str' => 'Actions'), $this);?>
</h4>
<table>
<tr><td><?php echo "<div id=\"notification_on_sale\"></div>"; ?></td><td><?php echo "<div id=\"notification_on_sale_status\"></div>"; ?></td></tr>
<tr><td><?php echo "<div id=\"notification_on_sale_summary\"></div>"; ?></td><td><?php echo "<div id=\"notification_on_sale_summary_status\"></div>"; ?></td></tr>
<tr><td><?php echo "<div id=\"notification_new_user\"></div>"; ?></td><td></td></tr>
<tr><td><?php echo "<div id=\"notification_on_join_to_campaign\"></div>"; ?></td><td></td></tr>
<tr><td><?php echo "<div id=\"notification_new_direct_link\"></div>"; ?></td><td></td></tr>
<tr><td><?php echo "<div id=\"notification_on_new_account_signup\"></div>"; ?></td><td></td></tr>
<tr><td><?php echo "<div id=\"notification_on_commission_approved\"></div>"; ?></td><td></td></tr>
</table>
<br/><div class="Line"></div>
<h4><?php echo smarty_function_localize(array('str' => 'Planned notifications'), $this);?>
</h4>
<?php echo "<div id=\"cronText\" class=\"Inline\"></div>"; ?><?php echo "<div id=\"setupCron\" class=\"Inline\"></div>"; ?>
<br/>
<br/>
<?php echo "<div id=\"notification_pay_day_reminder\"></div>"; ?>
<?php echo "<div id=\"notification_pay_day_reminder_day_of_month\"></div>"; ?>
<?php echo "<div id=\"notification_pay_day_reminder_recurrence_month\"></div>"; ?>
<br/>
<strong><?php echo smarty_function_localize(array('str' => 'Reports'), $this);?>
</strong><br/>
<br/>
<?php echo "<div id=\"notification_daily_report\"></div>"; ?>
<?php echo "<div id=\"notification_weekly_report\"></div>"; ?>
<?php echo "<div id=\"notification_weekly_report_start_day\"></div>"; ?>
<?php echo "<div id=\"notification_weekly_report_sent_on\"></div>"; ?>
<?php echo "<div id=\"notification_monthly_report\"></div>"; ?>
<?php echo "<div id=\"notification_monthly_report_sent_on\"></div>"; ?>
<br/>
<strong><?php echo smarty_function_localize(array('str' => 'Shared options'), $this);?>
</strong><br/>
<br/>
<?php echo "<div id=\"notification_report_maxtransactions\"></div>"; ?>
</fieldset>

<?php echo "<div id=\"FormMessage\"></div>"; ?>
<?php echo "<div id=\"SaveButton\"></div>"; ?>
<div class="clear"></div>