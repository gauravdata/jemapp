<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:45
         compiled from search_gadget_entries_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'search_gadget_entries_panel.tpl', 2, false),)), $this); ?>
<!-- search_gadget_entries_panel -->
<div style="color: white; font-weight: bold;"><?php echo smarty_function_localize(array('str' => 'Found'), $this);?>
:</div>
<?php echo "<div id=\"Entries\"></div>"; ?>