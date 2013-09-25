<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:35
         compiled from quicklaunch_settings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'quicklaunch_settings.tpl', 4, false),)), $this); ?>
<!--	quicklaunch_settings	-->

<fieldset>
	<legend><?php echo smarty_function_localize(array('str' => 'Quick launch settings'), $this);?>
</legend>
	<?php echo "<div id=\"showQuickLaunch\"></div>"; ?>
</fieldset>	
<?php echo "<div id=\"SaveButton\"></div>"; ?>