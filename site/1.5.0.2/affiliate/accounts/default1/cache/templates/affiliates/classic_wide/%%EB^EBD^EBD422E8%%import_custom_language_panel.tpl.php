<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:14
         compiled from import_custom_language_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'import_custom_language_panel.tpl', 2, false),)), $this); ?>
<!-- import_custom_language_panel -->
<?php echo smarty_function_localize(array('str' => 'You uploaded language file with following metadata:'), $this);?>

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Uploaded language file'), $this);?>
</legend>
	<?php echo "<div id=\"code\"></div>"; ?>
	<?php echo "<div id=\"name\"></div>"; ?>
	<?php echo "<div id=\"eng_name\"></div>"; ?>
	<?php echo "<div id=\"author\"></div>"; ?>
	<?php echo "<div id=\"version\"></div>"; ?>
	<?php echo "<div id=\"translated\"></div>"; ?>
</fieldset>
<?php echo smarty_function_localize(array('str' => 'If language metadata are correct, you can start import.'), $this);?>

<?php echo "<div id=\"importButton\"></div>"; ?>
<?php echo "<div id=\"cancelImportButton\"></div>"; ?>