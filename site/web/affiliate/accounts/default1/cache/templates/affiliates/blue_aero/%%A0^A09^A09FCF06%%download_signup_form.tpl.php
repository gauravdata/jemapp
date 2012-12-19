<?php /* Smarty version 2.6.18, created on 2011-12-16 03:39:02
         compiled from download_signup_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'download_signup_form.tpl', 2, false),)), $this); ?>
<!-- download_signup_form -->
<?php echo smarty_function_localize(array('str' => 'Copy and paste the code below to display join form on your web page'), $this);?>

<div class="DownloadSignUpForm">
<?php echo "<div id=\"formSource\"></div>"; ?>
<div class="Line"></div>
<div class="FormPreview"><?php echo smarty_function_localize(array('str' => 'Form preview'), $this);?>
</div>
<div class="Line"></div>
<?php echo "<div id=\"formPreview\"></div>"; ?>
</div>