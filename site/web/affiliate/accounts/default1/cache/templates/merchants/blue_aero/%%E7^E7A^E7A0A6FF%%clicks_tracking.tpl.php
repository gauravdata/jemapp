<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:28
         compiled from clicks_tracking.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'clicks_tracking.tpl', 3, false),)), $this); ?>
<!-- clicks_tracking -->

<h3><?php echo smarty_function_localize(array('str' => 'Tracking clicks (referrals)'), $this);?>
</h3> 
<h4><?php echo smarty_function_localize(array('str' => 'What is it'), $this);?>
</h4>
<?php echo smarty_function_localize(array('str' => 'ClicksTrackingDescription'), $this);?>

<br/><br/>

<h4><?php echo smarty_function_localize(array('str' => 'Where to put it'), $this);?>
</h4>
<?php echo smarty_function_localize(array('str' => 'You should put this clicks tracking code into <b>EVERY</b> page that will be the target of affiliate links.<br/>The best practice is to put it somewhere into the footer of your page.<br/>If you use good website content management system, this should guarantee that the tracking code will be executed in every page.'), $this);?>


<br/><br/>

<?php echo smarty_function_localize(array('str' => 'Copy & paste this JavaScript code to your page'), $this);?>
 
<?php echo "<div id=\"integrationCode\" class=\"ClickTrackingCode\"></div>"; ?>

<br/><br/>
<?php echo "<div id=\"readMoreInKB\"></div>"; ?>