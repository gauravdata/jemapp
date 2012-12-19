<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:32
         compiled from search_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'search_filter.tpl', 3, false),)), $this); ?>
<!-- search_filter -->
<div class="SearchAndFilterContent">
    <div class="SearchTextFloat"><?php echo smarty_function_localize(array('str' => 'Search for'), $this);?>
</div> <div class="SearchElementFloat"><?php echo "<div id=\"SearchInput\"></div>"; ?></div> <?php echo "<div id=\"SearchButton\" class=\"AdvancedFilter\"></div>"; ?>
</div>