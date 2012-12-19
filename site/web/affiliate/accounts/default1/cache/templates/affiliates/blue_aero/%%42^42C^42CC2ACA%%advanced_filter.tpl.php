<?php /* Smarty version 2.6.18, created on 2011-12-16 03:38:51
         compiled from advanced_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'advanced_filter.tpl', 3, false),)), $this); ?>
<!-- search_and_filter -->
<div class="SearchAndFilterContent">
	<div class="SearchTextFloat"><?php echo smarty_function_localize(array('str' => 'Search in'), $this);?>
</div> <div class="SearchElementFloat"><?php echo "<div id=\"SearchOptions\"></div>"; ?></div> <div class="SearchTextFloat"><?php echo smarty_function_localize(array('str' => 'for'), $this);?>
</div> <div class="SearchElementFloat"><?php echo "<div id=\"SearchInput\"></div>"; ?></div> <?php echo "<div id=\"SearchButton\"></div>"; ?> <?php echo "<div id=\"AdvancedSearchButton\" class=\"InlineBlock\"></div>"; ?>
</div>
<?php echo "<div id=\"AdvancedSearchPanel\"></div>"; ?>