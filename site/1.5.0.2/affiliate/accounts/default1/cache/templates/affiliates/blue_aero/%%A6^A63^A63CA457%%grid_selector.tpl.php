<?php /* Smarty version 2.6.18, created on 2011-12-16 03:38:38
         compiled from grid_selector.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'grid_selector.tpl', 2, false),)), $this); ?>
<!-- grid_selector -->
<div><?php echo smarty_function_localize(array('str' => 'Select'), $this);?>
:</div> <?php echo "<div id=\"SelectAll\"></div>"; ?> <?php echo "<div id=\"SelectAllInGrid\"></div>"; ?> <?php echo "<div id=\"SelectNone\"></div>"; ?>