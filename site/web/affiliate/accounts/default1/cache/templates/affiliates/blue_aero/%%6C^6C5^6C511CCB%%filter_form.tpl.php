<?php /* Smarty version 2.6.18, created on 2012-01-17 14:02:49
         compiled from filter_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'filter_form.tpl', 3, false),)), $this); ?>
<!-- filter_form -->
<fieldset>
	<legend><?php echo smarty_function_localize(array('str' => 'Filter Info'), $this);?>
</legend>
	<?php echo "<div id=\"name\"></div>"; ?>
</fieldset>

<?php echo "<div id=\"FormMessage\"></div>"; ?>
<?php echo "<div id=\"SaveButton\"></div>"; ?> <?php echo "<div id=\"CancelButton\"></div>"; ?>