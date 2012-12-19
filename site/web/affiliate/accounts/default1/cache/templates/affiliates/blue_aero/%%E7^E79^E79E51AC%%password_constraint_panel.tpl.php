<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:31
         compiled from password_constraint_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'password_constraint_panel.tpl', 3, false),)), $this); ?>
<!-- password_constraint_panel -->
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Password Constraints'), $this);?>
</legend>
	<?php echo "<div id=\"minLength\"></div>"; ?>
	<?php echo "<div id=\"maxLength\"></div>"; ?>
	<?php echo smarty_function_localize(array('str' => 'Required characters:'), $this);?>

	<?php echo "<div id=\"azChars\"></div>"; ?>
    <?php echo "<div id=\"digitsChars\"></div>"; ?>
    <?php echo "<div id=\"specialChars\"></div>"; ?>
</fieldset>
<?php echo "<div id=\"save\"></div>"; ?> <?php echo "<div id=\"FormMessage\"></div>"; ?>