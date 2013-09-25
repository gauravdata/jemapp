<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:21
         compiled from payout_option_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'payout_option_form.tpl', 3, false),)), $this); ?>
<!-- payout_option_form -->
<fieldset>
	<legend><?php echo smarty_function_localize(array('str' => 'Payout option details'), $this);?>
</legend>
	<?php echo "<div id=\"name\"></div>"; ?>
	<?php echo "<div id=\"rstatus\"></div>"; ?>
	<?php echo "<div id=\"rorder\"></div>"; ?>
</fieldset>
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Fields'), $this);?>
</legend>
<?php echo "<div id=\"PayoutFieldsGrid\"></div>"; ?>
</fieldset>
 
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Mass pay export format'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'You can specify name and format of mass pay export file for this payout option.'), $this);?>

<?php echo smarty_function_localize(array('str' => 'When you pay your affiliates you will be able to download export file for each payout option.'), $this);?>
<br>
<?php echo smarty_function_localize(array('str' => 'Format of this file consists of three parts: header, row and footer template.'), $this);?>

<?php echo smarty_function_localize(array('str' => 'Header is at the beginning of the file, row is generated for each affiliate that is going to be paid and footer is at the end of file.'), $this);?>

<?php echo smarty_function_localize(array('str' => 'In each of this templates you can use Smarty syntax and row template allows you also to use some other constants.'), $this);?>

<?php echo smarty_function_localize(array('str' => 'List of supported template constants is visible in the listbox above the row template text area.'), $this);?>

<br>
<?php echo "<div id=\"data4\"></div>"; ?>
<?php echo "<div id=\"data1\" class=\"RowTextArea\"></div>"; ?>
<?php echo "<div id=\"data2\" class=\"RowTextAreaTemplateEdit\"></div>"; ?>
<?php echo "<div id=\"data3\" class=\"RowTextAreaTemplateEdit\"></div>"; ?>
</fieldset>

<?php echo "<div id=\"FormMessage\"></div>"; ?><br/>
<?php echo "<div id=\"SaveButton\"></div>"; ?>