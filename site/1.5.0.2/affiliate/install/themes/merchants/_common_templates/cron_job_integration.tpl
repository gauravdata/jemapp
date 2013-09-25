<!-- cron_job_integration -->
<fieldset> 
<legend>##Cron job status##</legend>
<h4>##Last execution of cron job:## {widget id="lastRunTime"}</h4>
##To review list of pending background tasks push following button:## {widget id="openPendingTasks"}
</fieldset>
<br><br>



<fieldset> 
    <legend>##Cron setup##</legend>
    <h3>##Set up cron interval (default value is every five minutes).##</h3>
    <div class="CronInterval">
        {widget id="cronIntervalListBox"}
    </div>
    <br>
    ##This command you can use directly from crontab:##
    {widget id="cronIntervalCommand"}
    <br>
    ##This command will run job that is necessary when you generate recurring commmissions and some other features.##
    {widget id="cronCommand"}
    <br><br>
    <h3>##Example of Cron job setup in CPanel##</h3>
    <div class="CronJobSettingExample"></div>
</fieldset>
