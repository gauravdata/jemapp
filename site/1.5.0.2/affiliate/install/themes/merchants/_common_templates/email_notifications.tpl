<!-- email_notifications -->

<div class="EmailNotificationForm">

<fieldset>
<legend>##Email notifications for merchant##</legend>
{widget id="merchant_notification_email"}
<br/><div class="Line"></div>
<h4>##Actions##</h4>
<table>
<tr><td>{widget id="notification_on_sale"}</td><td>{widget id="notification_on_sale_status"}</td></tr>
<tr><td>{widget id="notification_on_sale_summary"}</td><td>{widget id="notification_on_sale_summary_status"}</td></tr>
<tr><td>{widget id="notification_new_user"}</td><td></td></tr>
<tr><td>{widget id="notification_on_join_to_campaign"}</td><td></td></tr>
<tr><td>{widget id="notification_new_direct_link"}</td><td></td></tr>
<tr><td>{widget id="notification_on_new_account_signup"}</td><td></td></tr>
<tr><td>{widget id="notification_on_commission_approved"}</td><td></td></tr>
</table>
<br/><div class="Line"></div>
<h4>##Planned notifications##</h4>
{widget id="cronText" class="Inline"}{widget id="setupCron" class="Inline"}
<br/>
<br/>
{widget id="notification_pay_day_reminder"}
{widget id="notification_pay_day_reminder_day_of_month"}
{widget id="notification_pay_day_reminder_recurrence_month"}
<br/>
<strong>##Reports##</strong><br/>
<br/>
{widget id="notification_daily_report"}
{widget id="notification_weekly_report"}
{widget id="notification_weekly_report_start_day"}
{widget id="notification_weekly_report_sent_on"}
{widget id="notification_monthly_report"}
{widget id="notification_monthly_report_sent_on"}
<br/>
<strong>##Shared options##</strong><br/>
<br/>
{widget id="notification_report_maxtransactions"}
</fieldset>

{widget id="FormMessage"}
{widget id="SaveButton"}
<div class="clear"></div>
