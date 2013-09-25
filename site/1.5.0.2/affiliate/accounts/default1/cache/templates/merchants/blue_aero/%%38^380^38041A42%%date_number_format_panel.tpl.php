<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:38
         compiled from date_number_format_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'date_number_format_panel.tpl', 2, false),)), $this); ?>
<!-- date_number_format_panel -->
<h4 class="TabDescription"><?php echo smarty_function_localize(array('str' => 'Date & Time format'), $this);?>
</h4>
<?php echo "<div id=\"dateformat\"></div>"; ?>
<?php echo "<div id=\"timeformat\"></div>"; ?>
<h4 class="TabDescription"><?php echo smarty_function_localize(array('str' => 'Number format'), $this);?>
</h4>
<?php echo "<div id=\"thousandsseparator\"></div>"; ?>
<?php echo "<div id=\"decimalseparator\"></div>"; ?>