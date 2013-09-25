<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:31
         compiled from onlinegadgets_no_data.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'onlinegadgets_no_data.tpl', 2, false),)), $this); ?>
<!-- gadgets_no_data -->
<div class="noDataHeader"><?php echo smarty_function_localize(array('str' => 'No gadget found in this category or nothing matches your search query'), $this);?>
</div>
<div class="noDataText"><?php echo smarty_function_localize(array('str' => 'Please review your search query and try to search gadgets again'), $this);?>
</div>