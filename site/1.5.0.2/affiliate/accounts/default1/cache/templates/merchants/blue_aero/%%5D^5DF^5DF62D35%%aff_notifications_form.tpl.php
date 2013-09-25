<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from aff_notifications_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'aff_notifications_form.tpl', 4, false),)), $this); ?>
<!--	aff_notifications_form		-->

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Email notifications for affiliate'), $this);?>
</legend>
<h4><?php echo smarty_function_localize(array('str' => 'Actions'), $this);?>
</h4>
<?php echo "<div id=\"aff_notification_campaign_invitation\"></div>"; ?>
<?php echo "<div id=\"aff_notification_on_change_status_for_campaign\"></div>"; ?>
<?php echo "<div id=\"aff_notification_before_approval\"></div>"; ?>
<?php echo "<div id=\"aff_notification_on_campaigns_suspended\"></div>"; ?>
<?php echo "<div id=\"aff_notification_signup_approved_declined\"></div>"; ?>

<div class="clear"></div>
<br/><div class="Line"></div><br/>
<div class="clear"></div>


<table class="EmailNotificationsTable">
    <tr><td></td><td><b class="FloatLeft"><?php echo smarty_function_localize(array('str' => 'Changeable by affiliate'), $this);?>
</b><?php echo "<div id=\"aff_notification_changeable_infotooltip\" class=\"FloatLeft\"></div>"; ?></td><td><b class="FloatLeft"><?php echo smarty_function_localize(array('str' => 'Default value for affiliate'), $this);?>
</b><?php echo "<div id=\"aff_notification_default_infotooltip\" class=\"FloatLeft\"></div>"; ?></td><td><b class="FloatLeft"><?php echo smarty_function_localize(array('str' => 'Allowed Status'), $this);?>
</b><?php echo "<div id=\"aff_notification_status_infotooltip\" class=\"FloatLeft\"></div>"; ?></td></tr>
    <tr><td><?php echo "<div id=\"aff_notification_on_new_sale_enabledLabel\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_on_new_sale_enabledInput\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_on_new_sale_defaultInput\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_on_new_sale_status\"></div>"; ?></td></tr>
    <tr><td><?php echo "<div id=\"aff_notification_on_change_comm_status_enabledLabel\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_on_change_comm_status_enabledInput\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_on_change_comm_status_defaultInput\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_on_change_comm_status_option\"></div>"; ?></td></tr>
    <tr><td><?php echo "<div id=\"aff_notification_on_subaff_signup_enabledLabel\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_on_subaff_signup_enabledInput\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_on_subaff_signup_defaultInput\"></div>"; ?></td><td></td></tr>
    <tr><td><?php echo "<div id=\"aff_notification_on_subaff_sale_enabledLabel\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_on_subaff_sale_enabledInput\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_on_subaff_sale_defaultInput\"></div>"; ?></td><td></td></tr>
    <tr><td><?php echo "<div id=\"aff_notification_on_direct_link_enabledLabel\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_on_direct_link_enabledInput\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_on_direct_link_defaultInput\"></div>"; ?></td><td></td></tr>
</table>

<br/><div class="Line"></div>
<h4><?php echo smarty_function_localize(array('str' => 'Planned notifications'), $this);?>
</h4>
<strong><?php echo smarty_function_localize(array('str' => 'Reports'), $this);?>
</strong><br/><br/>
<table class="EmailNotificationsTable">    
    <tr><td><?php echo "<div id=\"aff_notification_daily_report_enabledLabel\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_daily_report_enabledInput\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_daily_report_default\"></div>"; ?></td><td>&nbsp;</td></tr>
    <tr><td><?php echo "<div id=\"aff_notification_weekly_report_enabledLabel\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_weekly_report_enabledInput\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_weekly_report_default\"></div>"; ?></td><td></td></tr>
    <tr><td><?php echo "<div id=\"aff_notification_monthly_report_enabledLabel\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_monthly_report_enabledInput\"></div>"; ?></td><td><?php echo "<div id=\"aff_notification_monthly_report_default\"></div>"; ?></td><td></td></tr>    
</table>
</fieldset>

<?php echo "<div id=\"FormMessage\"></div>"; ?>
<?php echo "<div id=\"SaveButton\"></div>"; ?>
<div class="clear"></div>