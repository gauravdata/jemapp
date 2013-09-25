<?php /* Smarty version 2.6.18, created on 2012-01-17 14:02:49
         compiled from email_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'email_form.tpl', 5, false),)), $this); ?>
<!-- email_form -->
<div class="EmailForm">

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Mail'), $this);?>
</legend>
    <?php echo "<div id=\"subject\"></div>"; ?>

	<?php echo "<div id=\"body_html\"></div>"; ?>
	<?php echo "<div id=\"body_text\"></div>"; ?>
	<?php echo "<div id=\"customTextBodyControl\" class=\"EmailFormControlTextBody\"></div>"; ?>
</fieldset>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Attachments'), $this);?>
</legend>
    <?php echo "<div id=\"uploadedFiles\"></div>"; ?>
</fieldset>
</div>
<?php echo "<div id=\"clearButton\"></div>"; ?>
<?php echo "<div id=\"loadTemplateButton\"></div>"; ?>