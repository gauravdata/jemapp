<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:38
         compiled from cron_job_integration.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'cron_job_integration.tpl', 3, false),)), $this); ?>
<!-- cron_job_integration -->
<fieldset> 
<legend><?php echo smarty_function_localize(array('str' => 'Cron job status'), $this);?>
</legend>
<h4><?php echo smarty_function_localize(array('str' => 'Last execution of cron job:'), $this);?>
 <?php echo "<div id=\"lastRunTime\"></div>"; ?></h4>
<?php echo smarty_function_localize(array('str' => 'To review list of pending background tasks push following button:'), $this);?>
 <?php echo "<div id=\"openPendingTasks\"></div>"; ?>
</fieldset>
<br><br>



<fieldset> 
    <legend><?php echo smarty_function_localize(array('str' => 'Cron setup'), $this);?>
</legend>
    <h3><?php echo smarty_function_localize(array('str' => 'Set up cron interval (default value is every five minutes).'), $this);?>
</h3>
    <div class="CronInterval">
        <?php echo "<div id=\"cronIntervalListBox\"></div>"; ?>
    </div>
    <br>
    <?php echo smarty_function_localize(array('str' => 'This command you can use directly from crontab:'), $this);?>

    <?php echo "<div id=\"cronIntervalCommand\"></div>"; ?>
    <br>
    <?php echo smarty_function_localize(array('str' => 'This command will run job that is necessary when you generate recurring commmissions and some other features.'), $this);?>

    <?php echo "<div id=\"cronCommand\"></div>"; ?>
    <br><br>
    <h3><?php echo smarty_function_localize(array('str' => 'Example of Cron job setup in CPanel'), $this);?>
</h3>
    <div class="CronJobSettingExample"></div>
</fieldset>