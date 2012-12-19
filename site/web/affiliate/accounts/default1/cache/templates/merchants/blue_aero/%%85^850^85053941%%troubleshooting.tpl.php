<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:43
         compiled from troubleshooting.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'troubleshooting.tpl', 3, false),)), $this); ?>
<!-- troubleshooting -->

<h3><?php echo smarty_function_localize(array('str' => 'Troubleshooting'), $this);?>
</h3>
<br/>
<?php echo smarty_function_localize(array('str' => 'This troubleshooter will try to solve the problems with affiliate tracking.'), $this);?>
<br/>
<?php echo smarty_function_localize(array('str' => 'Usually, the reason why clicks or sales are not tracked, is missing or incorrect tracking code.'), $this);?>

<br/>

<h3><?php echo smarty_function_localize(array('str' => '1. Check if you have your tracking codes on place'), $this);?>
</h3>
<fieldset>
<?php echo smarty_function_localize(array('str' => 'Before continuing with troubleshooting, make sure you performed <b>Clicks tracking</b> and <b>Sale tracking</b> integration and
that the tracking codes are on the respective pages.'), $this);?>

<br/><br/>
<?php echo smarty_function_localize(array('str' => 'You can check your HTML page source to make sure the page contains JavaScript tracking code.'), $this);?>

</fieldset>

<?php echo smarty_function_localize(array('str' => 'Tracking codes seem to be in place and correct, yet the clicks or sales are still not tracked'), $this);?>


<h3><?php echo smarty_function_localize(array('str' => '2. Turn on debugging'), $this);?>
</h3>
<fieldset>
<?php echo smarty_function_localize(array('str' => '<b>Post Affiliate</b> offers powerful debugging tool that allows you to investigate every 
step of affiliate software during the tracking process.'), $this);?>

<br/><br/>
<?php echo smarty_function_localize(array('str' => 'Note that debugging should be turned only for testing, because it adds high load to the system.'), $this);?>

<div style="clear: both;"></div>
<?php echo "<div id=\"buttonDebugging\"></div>"; ?>

<div style="clear: both;"></div>
</fieldset>

<h3><?php echo smarty_function_localize(array('str' => '3. Make test click or sale'), $this);?>
</h3>
<fieldset>
<?php echo smarty_function_localize(array('str' => 'Now you should click on some affiliate\'s link, and (if you have problem tracking sales / leads) also make a test sale.'), $this);?>
<br/>
<?php echo smarty_function_localize(array('str' => 'This will trigger the system, and it will write information about your actions to the Event log.'), $this);?>

</fieldset>

<h3><?php echo smarty_function_localize(array('str' => '3. Check Event log for the logged actions'), $this);?>
</h3>
<fieldset>
<?php echo "<div id=\"buttonEventLog\"></div>"; ?>
<div style="clear: both;"></div>
<?php echo smarty_function_localize(array('str' => 'When the debugging is turned on, Event log will contain line for every action performed.'), $this);?>
<br/>
<?php echo smarty_function_localize(array('str' => 'If you don\'t see any new lines in the log, it means that the tracking code was not called at all, 
so your JavaScript was probably not on the right place.'), $this);?>

<br/><br/>
<?php echo smarty_function_localize(array('str' => 'If you see new lines, read them, they contain descriptive texts about every action, and they will tell you what went wrong.'), $this);?>

<br/><br/>
<?php echo smarty_function_localize(array('str' => 'For example, the tracking could have been aborted because some tracking parameter was missing (affiiate ID or TotalCost).'), $this);?>

</fieldset>

<?php echo "<div id=\"readMoreInKB\"></div>"; ?>