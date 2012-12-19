<!--	aff_notifications_form		-->

<fieldset>
<legend>##Email notifications for affiliate##</legend>
<h4>##Actions##</h4>
{widget id="aff_notification_campaign_invitation"}
{widget id="aff_notification_on_change_status_for_campaign"}
{widget id="aff_notification_before_approval"}
{widget id="aff_notification_on_campaigns_suspended"}
{widget id="aff_notification_signup_approved_declined"}

<div class="clear"></div>
<br/><div class="Line"></div><br/>
<div class="clear"></div>


<table class="EmailNotificationsTable">
    <tr><td></td><td><b class="FloatLeft">##Changeable by affiliate##</b>{widget id="aff_notification_changeable_infotooltip" class="FloatLeft"}</td><td><b class="FloatLeft">##Default value for affiliate##</b>{widget id="aff_notification_default_infotooltip" class="FloatLeft"}</td><td><b class="FloatLeft">##Allowed Status##</b>{widget id="aff_notification_status_infotooltip" class="FloatLeft"}</td></tr>
    <tr><td>{widget id="aff_notification_on_new_sale_enabledLabel"}</td><td>{widget id="aff_notification_on_new_sale_enabledInput"}</td><td>{widget id="aff_notification_on_new_sale_defaultInput"}</td><td>{widget id="aff_notification_on_new_sale_status"}</td></tr>
    <tr><td>{widget id="aff_notification_on_change_comm_status_enabledLabel"}</td><td>{widget id="aff_notification_on_change_comm_status_enabledInput"}</td><td>{widget id="aff_notification_on_change_comm_status_defaultInput"}</td><td>{widget id="aff_notification_on_change_comm_status_option"}</td></tr>
    <tr><td>{widget id="aff_notification_on_subaff_signup_enabledLabel"}</td><td>{widget id="aff_notification_on_subaff_signup_enabledInput"}</td><td>{widget id="aff_notification_on_subaff_signup_defaultInput"}</td><td></td></tr>
    <tr><td>{widget id="aff_notification_on_subaff_sale_enabledLabel"}</td><td>{widget id="aff_notification_on_subaff_sale_enabledInput"}</td><td>{widget id="aff_notification_on_subaff_sale_defaultInput"}</td><td></td></tr>
    <tr><td>{widget id="aff_notification_on_direct_link_enabledLabel"}</td><td>{widget id="aff_notification_on_direct_link_enabledInput"}</td><td>{widget id="aff_notification_on_direct_link_defaultInput"}</td><td></td></tr>
</table>

<br/><div class="Line"></div>
<h4>##Planned notifications##</h4>
<strong>##Reports##</strong><br/><br/>
<table class="EmailNotificationsTable">    
    <tr><td>{widget id="aff_notification_daily_report_enabledLabel"}</td><td>{widget id="aff_notification_daily_report_enabledInput"}</td><td>{widget id="aff_notification_daily_report_default"}</td><td>&nbsp;</td></tr>
    <tr><td>{widget id="aff_notification_weekly_report_enabledLabel"}</td><td>{widget id="aff_notification_weekly_report_enabledInput"}</td><td>{widget id="aff_notification_weekly_report_default"}</td><td></td></tr>
    <tr><td>{widget id="aff_notification_monthly_report_enabledLabel"}</td><td>{widget id="aff_notification_monthly_report_enabledInput"}</td><td>{widget id="aff_notification_monthly_report_default"}</td><td></td></tr>    
</table>
</fieldset>

{widget id="FormMessage"}
{widget id="SaveButton"}
<div class="clear"></div>
