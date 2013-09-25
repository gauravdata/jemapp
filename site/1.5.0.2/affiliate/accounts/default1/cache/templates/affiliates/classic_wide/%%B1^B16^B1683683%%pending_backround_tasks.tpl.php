<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:35
         compiled from pending_backround_tasks.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'pending_backround_tasks.tpl', 3, false),)), $this); ?>
<!-- pending_backround_tasks -->
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Simulate cron'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'To start manual execution of background tasks click button Start. Application will execute all pending background tasks automatically in multiple requests from browser (one browser request takes about 10 seconds and in this time can be other requests sent from browser delayed). Cron tasks execution will continue until you push Stop button or close this window.'), $this);?>

<div class="clear"></div><br/><br/>
<div class="clear"></div>
<?php echo "<div id=\"progressBar\" class=\"PendingTasksProgressBar\"></div>"; ?> <?php echo "<div id=\"stopButton\"></div>"; ?><?php echo "<div id=\"startButton\"></div>"; ?>
</fieldset>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Pending tasks'), $this);?>
</legend>
<?php echo "<div id=\"grid\"></div>"; ?>
</fieldset>