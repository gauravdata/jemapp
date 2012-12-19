<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:24
         compiled from login_form_downloadable.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'login_form_downloadable.tpl', 4, false),)), $this); ?>
<!-- login_form_downloadable -->

<div class="LoginFormDownloadable">
<?php echo smarty_function_localize(array('str' => 'Username'), $this);?>
 <?php echo "<div id=\"username\"></div>"; ?>
<?php echo smarty_function_localize(array('str' => 'Password'), $this);?>
 <?php echo "<div id=\"password\"></div>"; ?>
<?php echo smarty_function_localize(array('str' => 'Remember me'), $this);?>
 <?php echo "<div id=\"rememberMe\"></div>"; ?>
</div>
<?php echo "<div id=\"LoginButton\"></div>"; ?>