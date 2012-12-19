<?php /* Smarty version 2.6.18, created on 2011-12-16 03:39:02
         compiled from direct_linking_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'direct_linking_panel.tpl', 3, false),)), $this); ?>
<!-- direct_linking_panel -->
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Direct Linking'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'DirectLinkingDescription'), $this);?>


<?php echo "<div id=\"ManageDirectLinks\"></div>"; ?>
</fieldset>