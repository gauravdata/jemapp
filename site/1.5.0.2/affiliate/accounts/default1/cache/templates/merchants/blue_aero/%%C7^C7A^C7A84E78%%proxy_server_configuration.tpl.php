<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:32
         compiled from proxy_server_configuration.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'proxy_server_configuration.tpl', 3, false),)), $this); ?>
<!-- proxy_server_configuration -->
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'ProxyServer'), $this);?>
</legend>
<?php echo "<div id=\"server\"></div>"; ?>
<?php echo "<div id=\"port\"></div>"; ?>
<?php echo "<div id=\"user\"></div>"; ?>
<?php echo "<div id=\"password\"></div>"; ?>
<?php echo "<div id=\"FormMessage\"></div>"; ?>
</fieldset>
<?php echo "<div id=\"saveButton\"></div>"; ?>
<div class="clear"></div>