<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from add_custom_translation.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'add_custom_translation.tpl', 2, false),)), $this); ?>
<!-- add_custom_translation -->
<?php echo smarty_function_localize(array('str' => 'Source message has to be exactly same as you have text e.g. in your mail'), $this);?>
 
<?php echo "<div id=\"source\"></div>"; ?>
<?php echo "<div id=\"translation\"></div>"; ?>
<?php echo "<div id=\"SaveButton\"></div>"; ?><?php echo "<div id=\"FormMessage\"></div>"; ?>
<?php echo "<div id=\"CancelButton\"></div>"; ?>