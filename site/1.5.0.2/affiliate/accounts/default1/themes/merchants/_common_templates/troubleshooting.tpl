<!-- troubleshooting -->

<h3>##Troubleshooting##</h3>
<br/>
##This troubleshooter will try to solve the problems with affiliate tracking.##<br/>
##Usually, the reason why clicks or sales are not tracked, is missing or incorrect tracking code.##
<br/>

<h3>##1. Check if you have your tracking codes on place##</h3>
<fieldset>
##Before continuing with troubleshooting, make sure you performed <b>Clicks tracking</b> and <b>Sale tracking</b> integration and
that the tracking codes are on the respective pages.##
<br/><br/>
##You can check your HTML page source to make sure the page contains JavaScript tracking code.##
</fieldset>

##Tracking codes seem to be in place and correct, yet the clicks or sales are still not tracked##

<h3>##2. Turn on debugging##</h3>
<fieldset>
##<b>Post Affiliate</b> offers powerful debugging tool that allows you to investigate every 
step of affiliate software during the tracking process.##
<br/><br/>
##Note that debugging should be turned only for testing, because it adds high load to the system.##
<div style="clear: both;"></div>
{widget id="buttonDebugging"}

<div style="clear: both;"></div>
</fieldset>

<h3>##3. Make test click or sale##</h3>
<fieldset>
##Now you should click on some affiliate's link, and (if you have problem tracking sales / leads) also make a test sale.##<br/>
##This will trigger the system, and it will write information about your actions to the Event log.##
</fieldset>

<h3>##3. Check Event log for the logged actions##</h3>
<fieldset>
{widget id="buttonEventLog"}
<div style="clear: both;"></div>
##When the debugging is turned on, Event log will contain line for every action performed.##<br/>
##If you don't see any new lines in the log, it means that the tracking code was not called at all, 
so your JavaScript was probably not on the right place.##
<br/><br/>
##If you see new lines, read them, they contain descriptive texts about every action, and they will tell you what went wrong.##
<br/><br/>
##For example, the tracking could have been aborted because some tracking parameter was missing (affiiate ID or TotalCost).##
</fieldset>

{widget id="readMoreInKB"}
