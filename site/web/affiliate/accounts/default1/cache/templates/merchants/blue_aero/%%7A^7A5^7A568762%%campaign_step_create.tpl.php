<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:17
         compiled from campaign_step_create.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'campaign_step_create.tpl', 3, false),)), $this); ?>
<!-- campaign_step_create -->
<fieldset>
	<legend><?php echo smarty_function_localize(array('str' => 'Details'), $this);?>
</legend>
	<?php echo "<div id=\"name\"></div>"; ?>
	<?php echo "<div id=\"logourl\"></div>"; ?>
	<?php echo "<div id=\"description\"></div>"; ?>
</fieldset>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Commission types you want to support'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'Specify which types of commissions you want to support. You\'ll configure the commission amounts in the next step.'), $this);?>

<?php echo "<div id=\"checkpersale\"></div>"; ?>
<?php echo "<div id=\"checkperlead\"></div>"; ?>
<?php echo "<div id=\"checkperclick\"></div>"; ?>
<?php echo "<div id=\"checkpercpm\"></div>"; ?>
<?php echo "<div id=\"CheckErrorMessage\"></div>"; ?>
</fieldset>