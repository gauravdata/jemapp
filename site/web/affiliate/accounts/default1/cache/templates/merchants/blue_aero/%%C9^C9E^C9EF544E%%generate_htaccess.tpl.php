<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:00
         compiled from generate_htaccess.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'generate_htaccess.tpl', 3, false),)), $this); ?>
<!--    generate_htaccess   -->
<fieldset class="ReplicationHtaccess">
<legend><?php echo smarty_function_localize(array('str' => '.htaccess code for replicated site'), $this);?>
</legend>
<?php echo "<div id=\"htAccessCode\" class=\"CodeTextBox\"></div>"; ?>
<?php echo "<div id=\"url\"></div>"; ?>
</fieldset>
<?php echo "<div id=\"checkButton\"></div>"; ?><?php echo "<div id=\"closeButton\"></div>"; ?>