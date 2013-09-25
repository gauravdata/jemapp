<!-- pending_backround_tasks -->
<fieldset>
<legend>##Simulate cron##</legend>
##To start manual execution of background tasks click button Start. Application will execute all pending background tasks automatically in multiple requests from browser (one browser request takes about 10 seconds and in this time can be other requests sent from browser delayed). Cron tasks execution will continue until you push Stop button or close this window.##
<div class="clear"></div><br/><br/>
<div class="clear"></div>
{widget id="progressBar" class="PendingTasksProgressBar"} {widget id="stopButton"}{widget id="startButton"}
</fieldset>

<fieldset>
<legend>##Pending tasks##</legend>
{widget id="grid"}
</fieldset>
