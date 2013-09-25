<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:03
         compiled from email_settings_tab.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'email_settings_tab.tpl', 4, false),)), $this); ?>
<!-- email_settings_tab -->
<div class="EmailSettingsForm">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Email settings'), $this);?>
</legend>
<?php echo "<div id=\"email\"></div>"; ?>
<?php echo "<div id=\"send_test_mail_to\"></div>"; ?>
<?php echo "<div id=\"form_message\"></div>"; ?>
<?php echo "<div id=\"form_message_sendform\"></div>"; ?>
</fieldset>
</div>
<?php echo "<div id=\"save_button\"></div>"; ?>
<?php echo "<div id=\"send_mail_button\"></div>"; ?>

<div class="clear"></div>