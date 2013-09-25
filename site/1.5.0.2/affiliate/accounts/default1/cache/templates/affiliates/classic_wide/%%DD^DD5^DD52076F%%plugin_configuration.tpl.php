<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:35
         compiled from plugin_configuration.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'plugin_configuration.tpl', 3, false),)), $this); ?>
<!-- plugin_configuration -->
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Plugin settings'), $this);?>
</legend>
<?php echo "<div id=\"DynamicFields\"></div>"; ?>
</fieldset>
<?php echo "<div id=\"FormMessage\"></div>"; ?>
<?php echo "<div id=\"SaveButton\"></div>"; ?>