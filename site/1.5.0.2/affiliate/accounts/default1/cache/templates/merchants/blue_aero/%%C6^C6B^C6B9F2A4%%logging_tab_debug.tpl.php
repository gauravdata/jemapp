<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:10
         compiled from logging_tab_debug.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'logging_tab_debug.tpl', 5, false),)), $this); ?>
<!-- logging_tab_debug -->

<div class="SpecialDebug">

<?php echo smarty_function_localize(array('str' => 'Special debug settings'), $this);?>


<fieldset>
<?php echo smarty_function_localize(array('str' => 'Types of actions to log'), $this);?>
 
<?php echo "<div id=\"debug_types\"></div>"; ?>
<?php echo "<div id=\"debug_to_display\"></div>"; ?>
</fieldset>

</div>