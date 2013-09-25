<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:46
         compiled from validity.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'validity.tpl', 3, false),)), $this); ?>
<!--    validity    -->

<div><?php echo smarty_function_localize(array('str' => 'Valid from:'), $this);?>
</div><?php echo "<div id=\"validfrom\"></div>"; ?>
<div style="clear: both;"></div>
<div><?php echo smarty_function_localize(array('str' => 'Valid to:'), $this);?>
</div><?php echo "<div id=\"validto\"></div>"; ?>
<div style="clear: both;"></div>
<div><?php echo smarty_function_localize(array('str' => 'Limit use:'), $this);?>
</div><?php echo "<div id=\"limituse\"></div>"; ?>
<div style="clear: both;"></div>
<div><?php echo smarty_function_localize(array('str' => 'Sales:'), $this);?>
</div><?php echo "<div id=\"sales\"></div>"; ?>
<div style="clear: both;"></div>