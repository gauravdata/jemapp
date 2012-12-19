<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:49
         compiled from gadgets_filter_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'gadgets_filter_panel.tpl', 6, false),)), $this); ?>
<!-- gadgets_filter_panel -->

<div class="GadgetSearch">


<div class="Search"><?php echo smarty_function_localize(array('str' => 'Search:'), $this);?>
</div>

 <?php echo "<div id=\"SearchQuery\"></div>"; ?> <?php echo "<div id=\"SearchButton\"></div>"; ?>
<div class="clear"></div>

<?php echo "<div id=\"netvibesCheckbox\"></div>"; ?> <div class="Label"><?php echo smarty_function_localize(array('str' => 'Netvibes'), $this);?>
</div>
<?php echo "<div id=\"googleCheckbox\"></div>"; ?> <div class="Label"><?php echo smarty_function_localize(array('str' => 'Google'), $this);?>
</div>



<div class="clear"></div>
<?php echo "<div id=\"CategoriesTable\"></div>"; ?>

</div>