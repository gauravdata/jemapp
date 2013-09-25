<?php /* Smarty version 2.6.18, created on 2012-01-17 14:02:49
         compiled from email_notifications.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'email_notifications.tpl', 3, false),)), $this); ?>
<!-- email_notifications -->
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Email notifications'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'Set up which email notifications you want to receive'), $this);?>


<?php echo "<div id=\"aff_notification_on_new_sale\" class=\"AffiliateNotification\"></div>"; ?>
<?php echo "<div id=\"aff_notification_on_change_comm_status\" class=\"AffiliateNotification\"></div>"; ?>
<?php echo "<div id=\"aff_notification_on_subaff_signup\" class=\"AffiliateNotification\"></div>"; ?>
<?php echo "<div id=\"aff_notification_on_subaff_sale\" class=\"AffiliateNotification\"></div>"; ?>
<?php echo "<div id=\"aff_notification_on_direct_link_enabled\" class=\"AffiliateNotification\"></div>"; ?>
<?php echo "<div id=\"infoLabel\" class=\"AffiliateNotificationInfo\"></div>"; ?>
<?php echo "<div id=\"aff_notification_daily_report\"></div>"; ?>
<?php echo "<div id=\"aff_notification_weekly_report\"></div>"; ?>
<?php echo "<div id=\"aff_notification_monthly_report\"></div>"; ?>
</fieldset>

<?php echo "<div id=\"FormMessage\"></div>"; ?>
<?php echo "<div id=\"SaveButton\"></div>"; ?>
<div class="clear"></div>