<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:03
         compiled from email_settings_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'email_settings_form.tpl', 5, false),)), $this); ?>
<!-- email_settings_form -->
<?php echo "<div id=\"account_email\"></div>"; ?>
<?php echo "<div id=\"from_name\"></div>"; ?>
<div class="Line"></div>
<?php echo smarty_function_localize(array('str' => 'Choose how emails should be sent. Sending by mail() is highly recommended. If you don\'t receive emails, talk to your webhosting support to check and enable the PHP mail() function.<br/>Only if you cannot solve it, use sending by SMTP.'), $this);?>

<br/>
<?php echo "<div id=\"use_smtp\"></div>"; ?>

<?php echo "<div id=\"smtp_settings\"></div>"; ?>