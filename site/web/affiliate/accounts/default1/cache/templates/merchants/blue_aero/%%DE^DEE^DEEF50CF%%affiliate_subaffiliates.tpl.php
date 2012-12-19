<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:06
         compiled from affiliate_subaffiliates.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_subaffiliates.tpl', 2, false),)), $this); ?>
<!-- affiliate_subaffiliates -->
<?php echo smarty_function_localize(array('str' => 'Tree will show you relation of selected affiliate to his parents and his referred affiliates.'), $this);?>
 
<?php echo smarty_function_localize(array('str' => 'Filter is applicable just on referred affiliates, parent affiliates are always displayed all.'), $this);?>
 
<?php echo "<div id=\"filter\"></div>"; ?>
<?php echo "<div id=\"tree\"></div>"; ?>