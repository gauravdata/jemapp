<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:00
         compiled from language_create_new.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'language_create_new.tpl', 2, false),)), $this); ?>
<!-- language_create_new -->
<h3 class="TabDescription"><?php echo smarty_function_localize(array('str' => 'Creating new language translation'), $this);?>
</h3>
<?php echo smarty_function_localize(array('str' => 'Here you can create your own language. Define the fields below, then the new language will be created. You should edit it\'s language strings (translations) manually in Edit language form.'), $this);?>
 
<?php echo smarty_function_localize(array('str' => 'It is not possible to create new language with language code that already exists in the system!'), $this);?>

<br/><br/>
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Language metadata'), $this);?>
</legend>
	<h4 class="TabDescription"><?php echo smarty_function_localize(array('str' => 'Language details'), $this);?>
</h4>
	<?php echo "<div id=\"code\"></div>"; ?>
	<?php echo "<div id=\"name\"></div>"; ?>
	<?php echo "<div id=\"eng_name\"></div>"; ?>
	<?php echo "<div id=\"author\"></div>"; ?>
	<?php echo "<div id=\"version\"></div>"; ?>
	<?php echo "<div id=\"date_number_format_panel\"></div>"; ?>
</fieldset>
<?php echo "<div id=\"createButton\"></div>"; ?>
<?php echo "<div id=\"cancelButton\"></div>"; ?>
<?php echo "<div id=\"FormMessage\"></div>"; ?>