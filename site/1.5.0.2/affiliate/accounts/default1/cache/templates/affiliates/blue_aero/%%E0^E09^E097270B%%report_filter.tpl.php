<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:32
         compiled from report_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'report_filter.tpl', 4, false),)), $this); ?>
<!-- report_filter -->
<div class="SearchAndFilter">
<div class="SearchAndFilterContent">
	<div class="SearchTextFloat"><?php echo smarty_function_localize(array('str' => 'Report for'), $this);?>
</div>
	<div class="SearchElementFloat"><?php echo "<div id=\"SearchOptions\"></div>"; ?></div>
	<div class="SearchElementFloat AdvancedSearchButtonElement"><?php echo "<div id=\"AdvancedSearchButton\" class=\"AdvancedSearchButton\"></div>"; ?></div>	
</div>
<?php echo "<div id=\"AdvancedSearchPanel\"></div>"; ?>
</div>