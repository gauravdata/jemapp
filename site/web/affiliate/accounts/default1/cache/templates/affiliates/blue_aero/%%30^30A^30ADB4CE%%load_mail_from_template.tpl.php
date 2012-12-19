<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:21
         compiled from load_mail_from_template.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'load_mail_from_template.tpl', 3, false),)), $this); ?>
<!-- load_mail_from_template -->
<br/>
<?php echo smarty_function_localize(array('str' => 'To load template into your mail, just click on icon next to selected template in column Actions.'), $this);?>

<br/>
<?php echo smarty_function_localize(array('str' => 'IMPORTANT: Content of original mail will be replaced with selected mail template without option to rollback your changes.'), $this);?>

<br/><br/>
<?php echo "<div id=\"tabPanel\"></div>"; ?>
<br/>
<?php echo "<div id=\"closeButton\"></div>"; ?>