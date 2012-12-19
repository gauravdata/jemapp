<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:17
         compiled from campaign_category_selector.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'campaign_category_selector.tpl', 3, false),)), $this); ?>
<!-- campaign_category_selector -->
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Categories'), $this);?>
</legend>
<?php echo "<div id=\"grid\"></div>"; ?>
<?php echo "<div id=\"editButton\"></div>"; ?>
</fieldset>