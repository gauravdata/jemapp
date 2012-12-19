<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:43
         compiled from transaction_form_tracking.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'transaction_form_tracking.tpl', 5, false),)), $this); ?>
<!-- transaction_form_tracking -->

<div class="GeneralTracking">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'GeneralTrackingInformation'), $this);?>
</legend>
<?php echo "<div id=\"trackmethod\" class=\"Tracking\"></div>"; ?>
<?php echo "<div id=\"refererurl\" class=\"Tracking\"></div>"; ?>
<?php echo "<div id=\"ip\" class=\"Tracking\"></div>"; ?>
</fieldset>
</div>

<table border=0 cellpadding=0 cellspacing=0>
<tr>
<td>
<div class="ClickTitle">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'FirstClickTitle'), $this);?>
</legend>
<?php echo "<div id=\"firstclicktime\"></div>"; ?>
<?php echo "<div id=\"firstclickreferer\"></div>"; ?>
<?php echo "<div id=\"firstclickip\"></div>"; ?>
<?php echo "<div id=\"firstclickdata1\"></div>"; ?>
<?php echo "<div id=\"firstclickdata2\"></div>"; ?>
</fieldset>
</div>

</td><td>
<div class="ClickTitle">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'LastClickTitle'), $this);?>
</legend>
<?php echo "<div id=\"lastclicktime\"></div>"; ?>
<?php echo "<div id=\"lastclickreferer\"></div>"; ?>
<?php echo "<div id=\"lastclickip\"></div>"; ?>
<?php echo "<div id=\"lastclickdata1\"></div>"; ?>
<?php echo "<div id=\"lastclickdata2\"></div>"; ?>
</fieldset>
</div>
</td>
</tr>
</table>