<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:00
         compiled from invoice_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'invoice_form.tpl', 4, false),)), $this); ?>
<!--	invoice_form	-->

<fieldset>
	<legend><?php echo smarty_function_localize(array('str' => 'Invoice'), $this);?>
</legend>
	<?php echo "<div id=\"number\"></div>"; ?>
	<?php echo "<div id=\"numberButton\"></div>"; ?>
	<?php echo "<div id=\"datefrom\"></div>"; ?>
	<?php echo "<div id=\"dateto\"></div>"; ?>
	<?php echo "<div id=\"duedate\"></div>"; ?>
	<?php echo "<div id=\"accountid\"></div>"; ?>
	<?php echo "<div id=\"commissions\"></div>"; ?>
	<?php echo "<div id=\"fee\"></div>"; ?>
	<?php echo "<div id=\"amount\"></div>"; ?>
	<?php echo "<div id=\"afterbalance\"></div>"; ?>
	<?php echo "<div id=\"merchantnote\"></div>"; ?>
	<?php echo "<div id=\"systemnote\"></div>"; ?>	
	<?php echo "<div id=\"AccountDetails\"></div>"; ?>		
</fieldset>
<?php echo "<div id=\"FormMessage\"></div>"; ?>
<?php echo "<div id=\"CreateButton\"></div>"; ?>