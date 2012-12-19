<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:21
         compiled from pay_invoice_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'pay_invoice_form.tpl', 4, false),)), $this); ?>
<!--	pay_invoice_form	-->

<fieldset>
	<legend><?php echo smarty_function_localize(array('str' => 'Mark as paid'), $this);?>
</legend>
	<?php echo "<div id=\"datepaid\"></div>"; ?>
	<?php echo "<div id=\"amount\"></div>"; ?>
	<?php echo "<div id=\"merchantnote\"></div>"; ?>
	<?php echo "<div id=\"systemnote\"></div>"; ?>
</fieldset>	
<?php echo "<div id=\"PaidButton\"></div>"; ?>