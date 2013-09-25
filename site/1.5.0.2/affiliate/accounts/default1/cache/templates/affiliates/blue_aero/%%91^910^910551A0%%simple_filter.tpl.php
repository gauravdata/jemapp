<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:02
         compiled from simple_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'simple_filter.tpl', 3, false),)), $this); ?>
<!-- simple_filter -->
<div class="SimpleFilterContent">
	<div class="SearchTextFloat"><?php echo smarty_function_localize(array('str' => 'Filter'), $this);?>
</div>
</div>
<?php echo "<div id=\"AdvancedSearchPanel\" class=\"SimpleFilterAdvanced\"></div>"; ?>