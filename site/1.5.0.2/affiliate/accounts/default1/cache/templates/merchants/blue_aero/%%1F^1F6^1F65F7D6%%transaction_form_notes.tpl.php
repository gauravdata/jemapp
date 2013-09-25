<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:43
         compiled from transaction_form_notes.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'transaction_form_notes.tpl', 9, false),)), $this); ?>
<!-- transaction_form_notes -->

<table border=0 cellpadding=0 cellspacing=0>
<tr>
<td>

<div class="Note">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Merchant note'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'Visible only to merchant'), $this);?>

<?php echo "<div id=\"systemnote\" class=\"Note\"></div>"; ?>
</fieldset>
</div>

</td><td>

<div class="Note">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Affiliate note'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'Visible also to affiliate'), $this);?>

<?php echo "<div id=\"merchantnote\" class=\"Note\"></div>"; ?>
</fieldset>
</div>

</td>
</tr>
</table>
