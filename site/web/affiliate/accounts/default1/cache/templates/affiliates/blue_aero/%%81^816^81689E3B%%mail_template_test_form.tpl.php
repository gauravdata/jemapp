<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:21
         compiled from mail_template_test_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'mail_template_test_form.tpl', 5, false),)), $this); ?>
<!-- mail_template_test_form -->

<?php echo "<div id=\"recipient\"></div>"; ?>
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Template Variables'), $this);?>
</legend>
    <?php echo smarty_function_localize(array('str' => 'Template variable values entered below will be used only in your mail template test.'), $this);?>
  
    <?php echo "<div id=\"fieldsPanel\"></div>"; ?>
</fieldset>
<?php echo "<div id=\"FormMessage\"></div>"; ?>
<?php echo "<div id=\"sendButton\"></div>"; ?>
<?php echo "<div id=\"closeButton\"></div>"; ?>